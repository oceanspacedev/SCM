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
        $request->validate([
            'title' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'dpp_amount' => 'required|numeric',
        ]);

        $dpp = (float) $request->dpp_amount;
        $ppn = (float) ($request->ppn_amount ?: $dpp * 0.11);
        $total = (float) ($request->total_amount ?: $dpp + $ppn);

        $id = $request->id ?: 'PRG-' . str_pad((Program::count() + 1), 3, '0', STR_PAD_LEFT);

        $program = Program::create([
            'id' => $id,
            'title' => $request->title,
            'supplier' => $request->supplier,
            'category' => $request->category ?: 'Lainnya',
            'invoice_no' => $request->invoice_no ?: 'INV/' . date('Y') . '/SCM/' . rand(1000, 9999),
            'dpp_amount' => $dpp,
            'ppn_amount' => $ppn,
            'total_amount' => $total,
            'due_date' => $request->due_date ?: Carbon::now()->addDays(14)->toDateString(),
            'status' => 'Perlu Tindakan'
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

        $data = $request->only([
            'title', 'supplier', 'category', 'invoice_no',
            'dpp_amount', 'ppn_amount', 'total_amount', 'due_date', 'status'
        ]);

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
            'type' => $docType,
            'file_name' => $fileName ?: ($docType . '-' . $id . '.pdf'),
            'file_size' => $fileSize ?: '1.2 MB',
            'file_path' => $filePath,
            'file_url' => $fileUrl,
            'uploaded_by' => $request->input('uploaded_by', 'Staff'),
            'uploaded_at' => Carbon::now()->isoFormat('D MMM Y, HH.mm')
        ];

        if ($program) {
            ProgramDocument::updateOrCreate(
                ['program_id' => $program->id, 'type' => $docType],
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
        $doc = ProgramDocument::where('program_id', $programId)->where('id', $docId)->firstOrFail();
        $doc->delete();

        $program = Program::findOrFail($programId);
        $types = $program->documents()->pluck('type')->toArray();
        if (!(in_array('invoice', $types) && in_array('faktur', $types) && in_array('memo', $types))) {
            $program->update(['status' => 'Perlu Tindakan']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus.',
            'program' => $program->fresh()->load('documents')
        ]);
    }

    /**
     * Import multiple programs (Excel/JSON)
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

        $imported = 0;
        foreach ($items as $p) {
            $id = $p['id'] ?? ('PRG-' . Str::upper(Str::random(6)));
            $dpp = (float) ($p['dpp_amount'] ?? 0);
            $ppn = (float) ($p['ppn_amount'] ?? ($dpp * 0.11));
            $total = (float) ($p['total_amount'] ?? ($dpp + $ppn));

            $program = Program::updateOrCreate(
                ['id' => $id],
                [
                    'title' => $p['title'] ?? 'Program Pengadaan SCM',
                    'supplier' => $p['supplier'] ?? 'PT Rekanan Vendor',
                    'category' => $p['category'] ?? 'Lainnya',
                    'invoice_no' => $p['invoice_no'] ?? null,
                    'dpp_amount' => $dpp,
                    'ppn_amount' => $ppn,
                    'total_amount' => $total,
                    'due_date' => $p['due_date'] ?? Carbon::now()->addDays(20)->toDateString(),
                    'status' => $p['status'] ?? 'Perlu Tindakan'
                ]
            );

            // Import attached documents if present
            if (!empty($p['documents']) && is_array($p['documents'])) {
                foreach ($p['documents'] as $d) {
                    ProgramDocument::updateOrCreate(
                        ['id' => $d['id'] ?? ('doc-' . Str::random(8))],
                        [
                            'program_id' => $program->id,
                            'type' => $d['type'] ?? 'invoice',
                            'file_name' => $d['file_name'] ?? 'dokumen.pdf',
                            'file_size' => $d['file_size'] ?? '1.2 MB',
                            'uploaded_at' => Carbon::now()
                        ]
                    );
                }
            }
            $imported++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$imported} data program.",
            'imported_count' => $imported
        ]);
    }
}
