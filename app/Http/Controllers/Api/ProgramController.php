<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProgramController extends Controller
{
    /**
     * Get all programs with summary metrics
     */
    public function index()
    {
        $programs = Program::with('documents')->orderBy('due_date', 'desc')->get();

        $totalDpp = $programs->sum('dpp_amount');
        $totalPpn = $programs->sum('ppn_amount');
        $totalValue = $programs->sum('total_amount');
        $totalCount = $programs->count();
        $completeCount = $programs->where('status', 'Lengkap')->count();
        $attentionCount = $programs->where('status', 'Perlu Tindakan')->count();

        return response()->json([
            'success' => true,
            'programs' => $programs,
            'metrics' => [
                'total_dpp' => $totalDpp,
                'total_ppn' => $totalPpn,
                'total_value' => $totalValue,
                'total_count' => $totalCount,
                'complete_count' => $completeCount,
                'attention_count' => $attentionCount,
                'completeness_percentage' => $totalCount > 0 ? round(($completeCount / $totalCount) * 100) : 0
            ]
        ]);
    }

    /**
     * Get single program
     */
    public function show($id)
    {
        $program = Program::with('documents')->findOrFail($id);

        return response()->json([
            'success' => true,
            'program' => $program
        ]);
    }

    /**
     * Create new program
     */
    public function store(Request $request)
    {
        $title = $request->input('title') ?: $request->input('program_name');
        $supplier = $request->input('supplier');

        if (!$title || !$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Nama program dan supplier wajib diisi.'
            ], 422);
        }

        $dpp = (float) ($request->input('dpp_amount') ?? $request->input('dpp') ?? 0);
        $ppn = (float) ($request->input('ppn_amount') ?? $request->input('ppn') ?? ($dpp * 0.11));
        $total = (float) ($request->input('total_amount') ?? $request->input('total_invoice') ?? ($dpp + $ppn));

        $existingMax = Program::whereRaw('id REGEXP "^[0-9]+$"')->max('id');
        $nextId = (string) ($existingMax ? ((int) $existingMax + 1) : (Program::count() + 1));
        $id = (string) ($request->input('id') ?: $nextId);

        $program = Program::create([
            'id' => $id,
            'title' => $title,
            'supplier' => $supplier,
            'npwp' => $request->input('npwp') ?: '01.000.000.0-000.000',
            'category' => $request->input('category') ?: 'Logistik',
            'invoice_no' => $request->input('invoice_no') ?: $request->input('invoice_number') ?: ('INV/' . date('Y') . '/SCM/' . rand(1000, 9999)),
            'dpp_amount' => $dpp,
            'ppn_amount' => $ppn,
            'total_amount' => $total,
            'due_date' => $request->input('due_date') ?: $request->input('program_date') ?: Carbon::now()->addDays(14)->toDateString(),
            'status' => $request->input('status') ?: 'Perlu Tindakan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program berhasil ditambahkan.',
            'program' => $program->load('documents')
        ], 201);
    }

    /**
     * Update program
     */
    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);

        $title = $request->input('title') ?: $request->input('program_name');
        $invoiceNo = $request->input('invoice_no') ?: $request->input('invoice_number');
        $dueDate = $request->input('due_date') ?: $request->input('program_date');

        $data = [];
        if ($title) $data['title'] = $title;
        if ($request->has('supplier')) $data['supplier'] = $request->input('supplier');
        if ($request->has('npwp')) $data['npwp'] = $request->input('npwp');
        if ($request->has('category')) $data['category'] = $request->input('category');
        if ($invoiceNo !== null) $data['invoice_no'] = $invoiceNo;
        if ($request->has('dpp_amount') || $request->has('dpp')) {
            $data['dpp_amount'] = (float) ($request->input('dpp_amount') ?? $request->input('dpp'));
        }
        if ($request->has('ppn_amount') || $request->has('ppn')) {
            $data['ppn_amount'] = (float) ($request->input('ppn_amount') ?? $request->input('ppn'));
        }
        if ($request->has('total_amount') || $request->has('total_invoice')) {
            $data['total_amount'] = (float) ($request->input('total_amount') ?? $request->input('total_invoice'));
        }
        if ($dueDate) $data['due_date'] = $dueDate;
        if ($request->has('status')) $data['status'] = $request->input('status');

        $program->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Program berhasil diperbarui.',
            'program' => $program->load('documents')
        ]);
    }

    /**
     * Delete program
     */
    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        $program->documents()->delete();
        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program berhasil dihapus.'
        ]);
    }

    /**
     * Upload / Add Document to Program
     */
    public function uploadDocument(Request $request, $id)
    {
        $program = Program::with('documents')->find($id);

        $docType = $request->input('document_type') ?: $request->input('type', 'faktur_pajak');
        $backendType = $docType;
        if ($docType === 'faktur_pajak') $backendType = 'faktur';
        if ($docType === 'mou') $backendType = 'memo';

        $fileName = $request->input('file_name');
        $fileSize = $request->input('file_size');
        $filePath = null;
        $fileUrl = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $fileName ?: $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: 'pdf';
            $safeName = 'doc_' . time() . '_' . Str::random(8) . '.' . $extension;

            $destination = public_path('uploads/documents');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $safeName);
            $filePath = 'uploads/documents/' . $safeName;
            $fileUrl = url('uploads/documents/' . $safeName);

            $bytes = filesize($destination . '/' . $safeName);
            $fileSize = $bytes >= 1048576 
                ? round($bytes / 1048576, 1) . ' MB' 
                : round($bytes / 1024, 1) . ' KB';
        }

        $docId = 'doc-' . time() . '-' . rand(10, 99);

        $docData = [
            'id' => $docId,
            'document_type' => $docType,
            'type' => $backendType,
            'file_name' => $fileName ?: ($docType . '-' . $id . '.pdf'),
            'file_size' => $fileSize ?: '1.2 MB',
            'file_path' => $filePath,
            'file_url' => $fileUrl,
            'uploaded_by' => $request->input('uploaded_by', 'Staff'),
            'uploaded_at' => Carbon::now()->isoFormat('D MMM Y, HH.mm')
        ];

        if ($program) {
            ProgramDocument::updateOrCreate(
                ['program_id' => $program->id, 'type' => $backendType],
                [
                    'id' => $docId,
                    'file_name' => $docData['file_name'],
                    'file_size' => $docData['file_size'],
                    'file_path' => $filePath,
                    'uploaded_at' => Carbon::now()
                ]
            );

            // Check completeness
            $types = $program->documents()->pluck('type')->toArray();
            if (count(array_unique($types)) >= 3) {
                $program->update(['status' => 'Lengkap']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diunggah.',
            'document' => $docData
        ]);
    }

    /**
     * Delete Document
     */
    public function deleteDocument($programId, $docId)
    {
        $doc = ProgramDocument::where('program_id', $programId)
            ->where(function ($q) use ($docId) {
                $q->where('id', $docId)
                  ->orWhere('type', $docId)
                  ->orWhere('type', $docId === 'faktur_pajak' ? 'faktur' : ($docId === 'mou' ? 'memo' : $docId));
            })->first();

        if ($doc) {
            $doc->delete();
        }

        $program = Program::find($programId);
        if ($program) {
            $types = $program->documents()->pluck('type')->toArray();
            if (count(array_unique($types)) < 3) {
                $program->update(['status' => 'Perlu Tindakan']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus.',
            'program' => $program ? $program->fresh()->load('documents') : null
        ]);
    }

    /**
     * Import multiple programs (Excel/JSON) and persist to database
     */
    public function import(Request $request)
    {
        $items = $request->input('programs', []);

        if (!is_array($items) || empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data program yang diimport.'
            ], 400);
        }

        $existingMax = Program::whereRaw('id REGEXP "^[0-9]+$"')->max('id');
        $nextNumericId = $existingMax ? ((int) $existingMax) : Program::count();

        $imported = 0;
        foreach ($items as $p) {
            $nextNumericId++;
            $id = isset($p['id']) && !empty($p['id']) ? (string) $p['id'] : (string) $nextNumericId;

            $dpp = (float) ($p['dpp_amount'] ?? $p['dpp'] ?? 0);
            $ppn = (float) ($p['ppn_amount'] ?? $p['ppn'] ?? ($dpp * 0.11));
            $total = (float) ($p['total_amount'] ?? $p['total_invoice'] ?? ($dpp + $ppn));

            $program = Program::updateOrCreate(
                ['id' => $id],
                [
                    'title' => $p['title'] ?? $p['program_name'] ?? 'Program Pengadaan SCM',
                    'supplier' => $p['supplier'] ?? 'PT Rekanan Vendor',
                    'npwp' => $p['npwp'] ?? '01.000.000.0-000.000',
                    'category' => $p['category'] ?? 'Logistik',
                    'invoice_no' => $p['invoice_no'] ?? $p['invoice_number'] ?? ('INV/' . date('Y') . '/SCM/' . rand(1000, 9999)),
                    'dpp_amount' => $dpp,
                    'ppn_amount' => $ppn,
                    'total_amount' => $total,
                    'due_date' => $p['due_date'] ?? $p['program_date'] ?? Carbon::now()->toDateString(),
                    'status' => $p['status'] ?? 'Perlu Tindakan'
                ]
            );

            // Import attached documents if present
            if (!empty($p['documents']) && is_array($p['documents'])) {
                foreach ($p['documents'] as $d) {
                    $rawType = $d['document_type'] ?? $d['type'] ?? 'invoice';
                    $bType = $rawType === 'faktur_pajak' ? 'faktur' : ($rawType === 'mou' ? 'memo' : $rawType);

                    ProgramDocument::updateOrCreate(
                        ['id' => $d['id'] ?? ('doc-' . Str::random(8))],
                        [
                            'program_id' => $program->id,
                            'type' => $bType,
                            'file_name' => $d['file_name'] ?? ($rawType . '.pdf'),
                            'file_size' => $d['file_size'] ?? '1.2 MB',
                            'uploaded_at' => Carbon::now()
                        ]
                    );
                }
            }
            $imported++;
        }

        $allPrograms = Program::with('documents')->orderBy('due_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$imported} data program ke database.",
            'imported_count' => $imported,
            'programs' => $allPrograms
        ]);
    }
}
