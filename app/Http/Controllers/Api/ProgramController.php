<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramDocument;
use App\Models\RawImport;
use App\Services\SeaweedStorageService;
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
     * Import multiple programs (Excel/JSON) and persist raw file to SeaweedFS / S3 storage
     */
    public function import(Request $request)
    {
        // 1. Upload Raw File to SeaweedFS / S3 storage if file is present
        $rawImport = null;
        if ($request->hasFile('file')) {
            try {
                $seaweed = new SeaweedStorageService();
                $uploadedFile = $request->file('file');
                $storageResult = $seaweed->uploadRawFile(
                    $uploadedFile,
                    $uploadedFile->getClientOriginalName(),
                    'mentahan_excel'
                );

                $rawImport = RawImport::create([
                    'file_name' => $storageResult['file_name'],
                    'file_key' => $storageResult['file_key'],
                    'file_size' => $storageResult['file_size'],
                    'file_url' => $storageResult['file_url'],
                    'storage_type' => $storageResult['storage'],
                    'imported_rows_count' => 0,
                    'uploaded_by' => $request->input('uploaded_by', 'Admin SCM'),
                    'notes' => $storageResult['warning'] ?? 'Tersimpan di storage SeaweedFS'
                ]);
            } catch (\Throwable $e) {
                \Log::error('Error uploading raw import file: ' . $e->getMessage());
            }
        }

        // 2. Parse program rows (can be JSON string or array)
        $programsInput = $request->input('programs', []);
        if (is_string($programsInput)) {
            $items = json_decode($programsInput, true) ?: [];
        } else {
            $items = is_array($programsInput) ? $programsInput : [];
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

        if ($rawImport && $imported > 0) {
            $rawImport->update(['imported_rows_count' => $imported]);
        }

        $allPrograms = Program::with('documents')->orderBy('due_date', 'desc')->get();

        $storageMessage = $rawImport ? ' Berkas mentahan tersimpan di SeaweedFS SCM.' : '';

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$imported} data program ke database.{$storageMessage}",
            'imported_count' => $imported,
            'programs' => $allPrograms,
            'raw_import' => $rawImport
        ]);
    }

    /**
     * Get list of raw imported Excel files
     */
    public function rawImports()
    {
        $imports = RawImport::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'raw_imports' => $imports
        ]);
    }

    /**
     * Delete a raw import log entry
     */
    public function deleteRawImport($id)
    {
        $item = RawImport::find($id);
        if ($item) {
            $item->delete();
        }
        return response()->json([
            'success' => true,
            'message' => 'Riwayat file mentahan telah dihapus.'
        ]);
    }

    /**
     * Download or stream raw import file
     */
    public function downloadRawImport($id)
    {
        $raw = RawImport::findOrFail($id);

        // 1. Try local backup file
        if ($raw->file_key) {
            $localPath = public_path('uploads/mentahan_excel/' . basename($raw->file_key));
            if (file_exists($localPath)) {
                return response()->download($localPath, $raw->file_name);
            }
        }

        // 2. Try fetching from SeaweedFS S3
        if ($raw->file_key) {
            $seaweed = new SeaweedStorageService();
            $obj = $seaweed->getObject($raw->file_key);
            if ($obj && !empty($obj['content'])) {
                return response($obj['content'])
                    ->header('Content-Type', $obj['mime'])
                    ->header('Content-Disposition', 'attachment; filename="' . $raw->file_name . '"');
            }
        }

        // 3. Redirect to file_url
        if ($raw->file_url) {
            return redirect($raw->file_url);
        }

        abort(404, 'Berkas mentahan tidak ditemukan.');
    }
}
