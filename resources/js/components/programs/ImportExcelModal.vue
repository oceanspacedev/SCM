<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
  >
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
      @click="closeModal"
    ></div>

    <!-- Modal Dialog -->
    <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl overflow-hidden z-10 flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-150">
      <!-- Header -->
      <div class="px-6 py-4.5 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center shrink-0">
            <ExcelIcon class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900 leading-tight">
              Import Data Program & Invoice
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
              Unggah file Excel (.xlsx, .xls) atau CSV untuk memasukkan data program sekaligus
            </p>
          </div>
        </div>
        <button
          type="button"
          class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
          @click="closeModal"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Body -->
      <div class="p-6 overflow-y-auto space-y-5 flex-1 text-xs">
        <!-- Format Column Reference Note matching User Screenshot -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3 text-slate-700">
          <Info class="w-4 h-4 text-slate-500 mt-0.5 shrink-0" />
          <div class="flex-1 text-[11px] leading-relaxed">
            <p class="font-bold text-slate-900">Format Kolom yang Didukung:</p>
            <div class="mt-1.5 flex flex-wrap gap-1.5">
              <span class="px-2 py-0.5 bg-white rounded border border-slate-200 font-mono font-semibold text-slate-700">PROGRAM</span>
              <span class="px-2 py-0.5 bg-white rounded border border-slate-200 font-mono font-semibold text-slate-700">SUPPLIER</span>
              <span class="px-2 py-0.5 bg-white rounded border border-slate-200 font-mono font-semibold text-slate-700">NO. INVOICE</span>
              <span class="px-2 py-0.5 bg-white rounded border border-slate-200 font-mono font-semibold text-slate-700">DPP</span>
              <span class="px-2 py-0.5 bg-white rounded border border-slate-200 font-mono font-semibold text-slate-700">PPN</span>
              <span class="px-2 py-0.5 bg-white rounded border border-slate-200 font-mono font-semibold text-slate-700">TOTAL INVOICE</span>
            </div>
            <p class="text-slate-500 mt-1.5 text-[10px]">
              * PPN (11%) dan Total Invoice akan dihitung otomatis jika nilainya dikosongkan.
            </p>
          </div>
          <button
            type="button"
            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-[11px] font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer"
            @click="downloadTemplate('xlsx')"
          >
            <ExcelIcon class="w-3.5 h-3.5" />
            <span>Unduh Contoh Excel</span>
          </button>
        </div>

        <!-- Dropzone -->
        <div
          class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-colors"
          :class="isDragging ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-300 hover:border-emerald-400 bg-slate-50/40'"
          @dragover.prevent="isDragging = true"
          @dragleave.prevent="isDragging = false"
          @drop.prevent="handleDrop"
          @click="triggerFileInput"
        >
          <input
            ref="fileInputRef"
            type="file"
            accept=".xlsx, .xls, .csv"
            class="hidden"
            @change="handleFileSelect"
          />

          <div class="flex flex-col items-center justify-center gap-2">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-[#135A46] flex items-center justify-center mb-1">
              <UploadCloud class="w-6 h-6" />
            </div>
            <p class="font-bold text-slate-800 text-sm">
              Tarik file Excel / CSV ke sini, atau <span class="text-[#135A46] underline">pilih dari perangkat</span>
            </p>
            <p class="text-slate-400 text-[11px]">
              Mendukung format .xlsx, .xls, atau .csv (Maksimal 10 MB)
            </p>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="py-8 text-center text-slate-500 flex flex-col items-center justify-center gap-2">
          <div class="w-6 h-6 border-2 border-[#135A46] border-t-transparent rounded-full animate-spin"></div>
          <p class="font-medium text-xs">Membaca dan memvalidasi file data...</p>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl flex items-start gap-2.5 text-rose-700">
          <AlertCircle class="w-4 h-4 mt-0.5 shrink-0" />
          <div>
            <p class="font-bold text-xs">Gagal Memproses File</p>
            <p class="text-[11px] mt-0.5">{{ errorMessage }}</p>
          </div>
        </div>

        <!-- Parsed Data Preview -->
        <div v-if="parsedRows.length > 0 && !isLoading" class="space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <CheckCircle2 class="w-4 h-4 text-emerald-600" />
              <span class="font-bold text-slate-900 text-xs">
                Preview Data ({{ parsedRows.length }} Baris Terdeteksi)
              </span>
            </div>
            <button
              type="button"
              class="text-slate-400 hover:text-rose-600 text-[11px] font-semibold cursor-pointer"
              @click="resetFile"
            >
              Ganti File
            </button>
          </div>

          <!-- Preview Table matching User Table Headers -->
          <div class="border border-slate-200 rounded-xl overflow-hidden">
            <div class="max-h-56 overflow-y-auto overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 sticky top-0 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400 font-sans">
                  <tr>
                    <th class="py-2.5 px-3">#</th>
                    <th class="py-2.5 px-3">PROGRAM</th>
                    <th class="py-2.5 px-3">SUPPLIER</th>
                    <th class="py-2.5 px-3">NO. INVOICE</th>
                    <th class="py-2.5 px-3 text-right">DPP</th>
                    <th class="py-2.5 px-3 text-right">PPN</th>
                    <th class="py-2.5 px-3 text-right">TOTAL INVOICE</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr
                    v-for="(row, idx) in parsedRows.slice(0, 15)"
                    :key="idx"
                    class="hover:bg-slate-50/70"
                  >
                    <td class="py-2 px-3 text-slate-400 font-mono text-[10px]">{{ idx + 1 }}</td>
                    <td class="py-2 px-3 font-semibold text-slate-900 max-w-[160px] truncate" :title="row.program_name">
                      {{ row.program_name }}
                    </td>
                    <td class="py-2 px-3 text-slate-700 max-w-[140px] truncate" :title="row.supplier">
                      {{ row.supplier }}
                    </td>
                    <td class="py-2 px-3 font-mono text-slate-600 whitespace-nowrap">
                      {{ row.invoice_number }}
                    </td>
                    <td class="py-2 px-3 font-mono text-right text-slate-700 whitespace-nowrap">
                      {{ formatRupiah(row.dpp) }}
                    </td>
                    <td class="py-2 px-3 font-mono text-right text-amber-700 font-medium whitespace-nowrap">
                      {{ formatRupiah(row.ppn) }}
                    </td>
                    <td class="py-2 px-3 font-mono text-right font-bold text-slate-900 whitespace-nowrap">
                      {{ formatRupiah(row.total_invoice) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="parsedRows.length > 15" class="p-2 bg-slate-50 border-t border-slate-200 text-center text-[10px] text-slate-500">
              Menampilkan 15 dari {{ parsedRows.length }} baris data
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50/80 flex items-center justify-between">
        <span class="text-slate-500 text-xs">
          {{ parsedRows.length > 0 ? `${parsedRows.length} data siap dimasukkan` : 'Pilih file terlebih dahulu' }}
        </span>
        <div class="flex items-center gap-2.5">
          <button
            type="button"
            class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
            @click="closeModal"
          >
            Batal
          </button>
          <button
            type="button"
            :disabled="parsedRows.length === 0 || isLoading || isImporting"
            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-[#135A46] text-white text-xs font-bold hover:bg-[#0e4334] disabled:opacity-50 disabled:cursor-not-allowed shadow-2xs transition-all cursor-pointer"
            @click="executeImport"
          >
            <UploadCloud v-if="!isImporting" class="w-4 h-4" />
            <span v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span>{{ isImporting ? 'Menyimpan ke Server...' : `Konfirmasi & Import ${parsedRows.length > 0 ? `${parsedRows.length} Data` : ''}` }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  FileSpreadsheet,
  UploadCloud,
  X,
  Info,
  Download,
  AlertCircle,
  CheckCircle2
} from 'lucide-vue-next';
import ExcelIcon from '../ui/ExcelIcon.vue';
import { useTaxStore, formatRupiah } from '../../store/taxStore';

const router = useRouter();
const store = useTaxStore();

const isOpen = computed(() => store.isImportModalOpen.value);
const isDragging = ref(false);
const isLoading = ref(false);
const isImporting = ref(false);
const errorMessage = ref('');
const parsedRows = ref([]);
const fileInputRef = ref(null);

function closeModal() {
  resetFile();
  store.closeImportModal();
}

function resetFile() {
  parsedRows.value = [];
  errorMessage.value = '';
  isLoading.value = false;
  if (fileInputRef.value) {
    fileInputRef.value.value = '';
  }
}

function triggerFileInput() {
  if (fileInputRef.value) {
    fileInputRef.value.click();
  }
}

function handleFileSelect(e) {
  const file = e.target.files?.[0];
  if (file) {
    processFile(file);
  }
}

function handleDrop(e) {
  isDragging.value = false;
  const file = e.dataTransfer.files?.[0];
  if (file) {
    processFile(file);
  }
}

// Clean and parse numbers (handles "Rp 15.000.000", "15000000", "15.000.000,00", etc.)
function cleanNumber(val) {
  if (val === null || val === undefined) return 0;
  if (typeof val === 'number') return val;
  let s = String(val).trim();
  s = s.replace(/^(Rp|IDR)\s*/i, '').trim();

  // Indonesian format: 15.000.000 or 15.000.000,00
  if (/^\d{1,3}(\.\d{3})+(,\d+)?$/.test(s)) {
    s = s.replace(/\./g, '').replace(',', '.');
  } else if (/^\d{1,3}(,\d{3})+(\.\d+)?$/.test(s)) {
    // US format: 15,000,000.00
    s = s.replace(/,/g, '');
  } else {
    s = s.replace(/[^0-9.-]/g, '');
  }
  const n = parseFloat(s);
  return isNaN(n) ? 0 : Math.round(n);
}

// Flexible header normalizer
function normalizeKey(str) {
  return String(str || '').toLowerCase().replace(/[^a-z0-9]/g, '');
}

function mapRawRow(raw) {
  let program_name = '';
  let supplier = '';
  let invoice_number = '';
  let dpp = 0;
  let ppn = 0;
  let total_invoice = 0;
  let npwp = '';
  let category = 'Logistik';
  let program_date = new Date().toISOString().split('T')[0];

  for (const [key, val] of Object.entries(raw)) {
    const k = normalizeKey(key);

    // PROGRAM
    if (k.includes('program') || k === 'nama' || k === 'project') {
      program_name = String(val || '').trim();
    }
    // SUPPLIER
    else if (k.includes('supplier') || k.includes('vendor') || k.includes('suplier') || k === 'perusahaan') {
      supplier = String(val || '').trim();
    }
    // NO. INVOICE
    else if (k.includes('invoice') || k.includes('inv') || k.includes('faktur')) {
      invoice_number = String(val || '').trim();
    }
    // DPP
    else if (k === 'dpp' || k.includes('dpp') || k.includes('dasar')) {
      dpp = cleanNumber(val);
    }
    // PPN
    else if (k === 'ppn' || k.includes('ppn') || k === 'pajak') {
      ppn = cleanNumber(val);
    }
    // TOTAL INVOICE
    else if (k.includes('total') || k.includes('grand') || k.includes('jumlah')) {
      total_invoice = cleanNumber(val);
    }
    // NPWP
    else if (k.includes('npwp')) {
      npwp = String(val || '').trim();
    }
    // KATEGORI
    else if (k.includes('kategori') || k.includes('category')) {
      category = String(val || '').trim();
    }
    // TANGGAL
    else if (k.includes('tanggal') || k.includes('date') || k.includes('tgl')) {
      program_date = String(val || '').trim() || program_date;
    }
  }

  // Automatic Fallbacks & Calculations
  if (dpp > 0 && ppn === 0) {
    ppn = Math.round(dpp * 0.11);
  }
  if (total_invoice === 0) {
    total_invoice = dpp + ppn;
  }
  if (!invoice_number) {
    invoice_number = `INV-${Date.now().toString().slice(-6)}`;
  }
  if (!npwp) {
    npwp = '01.234.567.8-012.000';
  }

  return {
    program_name: program_name || 'Program SCM',
    supplier: supplier || 'Vendor SCM',
    invoice_number,
    dpp,
    ppn,
    total_invoice,
    npwp,
    category,
    program_date
  };
}

async function processFile(file) {
  errorMessage.value = '';
  isLoading.value = true;
  parsedRows.value = [];

  try {
    const fileName = file.name.toLowerCase();
    let rawData = [];

    if (fileName.endsWith('.xlsx') || fileName.endsWith('.xls')) {
      if (!window.XLSX) {
        throw new Error('Pustaka SheetJS belum termuat. Silakan muat ulang halaman atau unggah format .csv.');
      }
      const buffer = await file.arrayBuffer();
      const workbook = window.XLSX.read(new Uint8Array(buffer), { type: 'array' });
      const firstSheet = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[firstSheet];
      rawData = window.XLSX.utils.sheet_to_json(worksheet, { defval: '' });
    } else if (fileName.endsWith('.csv')) {
      const text = await file.text();
      rawData = parseCsvText(text);
    } else {
      throw new Error('Format file tidak didukung. Harap unggah file .xlsx, .xls, atau .csv.');
    }

    if (!rawData || rawData.length === 0) {
      throw new Error('File tidak memiliki data atau baris kosong.');
    }

    const mapped = rawData.map(mapRawRow).filter(r => r.program_name && (r.dpp > 0 || r.total_invoice > 0 || r.supplier));

    if (mapped.length === 0) {
      throw new Error('Tidak ada data valid yang dapat dikenali. Pastikan terdapat kolom PROGRAM, SUPPLIER, NO. INVOICE, DPP, PPN, dan TOTAL INVOICE.');
    }

    parsedRows.value = mapped;
  } catch (err) {
    console.error('Import error:', err);
    errorMessage.value = err.message || 'Terjadi kesalahan saat memproses file.';
  } finally {
    isLoading.value = false;
  }
}

function parseCsvText(text) {
  const lines = text.split(/\r\n|\n/).map(l => l.trim()).filter(Boolean);
  if (lines.length < 2) return [];

  const delimiter = lines[0].includes(';') ? ';' : ',';
  const headers = lines[0].split(delimiter).map(h => h.replace(/^["']|["']$/g, '').trim());

  const rows = [];
  for (let i = 1; i < lines.length; i++) {
    const line = lines[i];
    const values = line.split(delimiter).map(v => v.replace(/^["']|["']$/g, '').trim());
    const obj = {};
    headers.forEach((h, idx) => {
      obj[h] = values[idx] || '';
    });
    rows.push(obj);
  }
  return rows;
}

function downloadTemplate(format = 'xlsx') {
  const sampleHeaders = ['PROGRAM', 'SUPPLIER', 'NO. INVOICE', 'DPP', 'PPN', 'TOTAL INVOICE', 'KATEGORI', 'TANGGAL'];
  const sampleRows = [
    ['Pengadaan Komponen Pipa Gas Tuban', 'PT Steel Pipe Industry of Indonesia Tbk', 'INV/2026/SCM/0101', 45000000, 4950000, 49950000, 'Pipa & Tubing', '2026-03-01'],
    ['Penyewaan Heavy Crane Lepas Pantai', 'PT Radiant Utama Interinsco Tbk', 'INV/2026/SCM/0102', 120000000, 13200000, 133200000, 'Sewa Alat Berat', '2026-03-02'],
    ['Jasa Inspeksi Tangki Kilang Balikpapan', 'PT Sucofindo (Persero)', 'INV/2026/SCM/0103', 75000000, 8250000, 83250000, 'Inspeksi & Sertifikasi', '2026-03-03'],
    ['Pengadaan High Pressure Valve & Flange', 'PT Kitz Valve Indonesia', 'INV/2026/SCM/0104', 38500000, 4235000, 42735000, 'Mekanikal & Valve', '2026-03-04'],
    ['Pengadaan Chemical Demulsifier Lapangan', 'PT Clariant Indonesia', 'INV/2026/SCM/0105', 92000000, 10120000, 102120000, 'Bahan Kimia', '2026-03-05']
  ];

  if (format === 'xlsx' && window.XLSX) {
    const XLSX = window.XLSX;
    const wb = XLSX.utils.book_new();

    // 1. Main Sheet
    const ws = XLSX.utils.aoa_to_sheet([sampleHeaders, ...sampleRows]);

    // Set Column Widths (Auto-fitted and spacious)
    ws['!cols'] = [
      { wch: 44 }, // PROGRAM
      { wch: 42 }, // SUPPLIER
      { wch: 24 }, // NO. INVOICE
      { wch: 20 }, // DPP
      { wch: 18 }, // PPN
      { wch: 22 }, // TOTAL INVOICE
      { wch: 26 }, // KATEGORI
      { wch: 16 }  // TANGGAL
    ];

    // Format numbers with thousands separators (#,##0)
    for (let R = 1; R <= sampleRows.length; ++R) {
      const dppCell = XLSX.utils.encode_cell({ r: R, c: 3 });
      const ppnCell = XLSX.utils.encode_cell({ r: R, c: 4 });
      const totalCell = XLSX.utils.encode_cell({ r: R, c: 5 });

      if (ws[dppCell]) { ws[dppCell].t = 'n'; ws[dppCell].z = '#,##0'; }
      if (ws[ppnCell]) { ws[ppnCell].t = 'n'; ws[ppnCell].z = '#,##0'; }
      if (ws[totalCell]) { ws[totalCell].t = 'n'; ws[totalCell].z = '#,##0'; }
    }

    // 2. Panduan Pengisian Sheet
    const guideData = [
      ['PANDUAN PENGISIAN TEMPLATE IMPORT PROGRAM SCM TAXVAULT'],
      ['Gunakan template ini untuk memasukkan data program dan perpajakan secara massal ke dalam sistem.'],
      [''],
      ['KOLOM', 'STATUS', 'CONTOH PENGISIAN', 'KETERANGAN'],
      ['PROGRAM', 'WAJIB', 'Pengadaan Komponen Pipa Gas Tuban', 'Nama program atau paket pengadaan SCM.'],
      ['SUPPLIER', 'WAJIB', 'PT Steel Pipe Industry of Indonesia Tbk', 'Nama resmi perusahaan vendor / penyedia.'],
      ['NO. INVOICE', 'WAJIB', 'INV/2026/SCM/0101', 'Nomor invoice resmi dari vendor.'],
      ['DPP', 'WAJIB', '45000000', 'Dasar Pengenaan Pajak (masukkan angka saja, tanpa Rp/titik).'],
      ['PPN', 'OPSIONAL', '4950000', 'Nilai PPN (11%). Jika dikosongkan, sistem menghitung otomatis dari DPP.'],
      ['TOTAL INVOICE', 'OPSIONAL', '49950000', 'Nilai total tagihan. Jika dikosongkan, dihitung dari DPP + PPN.'],
      ['KATEGORI', 'OPSIONAL', 'Pipa & Tubing', 'Kategori program SCM.'],
      ['TANGGAL', 'OPSIONAL', '2026-03-01', 'Format YYYY-MM-DD atau tanggal standar Excel.'],
      [''],
      ['TIPS:'],
      ['1. Anda dapat menambahkan baris baru di bawah baris contoh atau menghapus baris contoh.'],
      ['2. Jangan mengubah atau menghapus nama header di baris pertama.'],
      ['3. File siap diunggah langsung melalui tombol "Import Excel" di aplikasi SCM TaxVault.']
    ];

    const wsGuide = XLSX.utils.aoa_to_sheet(guideData);
    wsGuide['!cols'] = [
      { wch: 18 },
      { wch: 12 },
      { wch: 44 },
      { wch: 75 }
    ];

    XLSX.utils.book_append_sheet(wb, ws, 'Template Program');
    XLSX.utils.book_append_sheet(wb, wsGuide, 'Panduan Pengisian');

    XLSX.writeFile(wb, 'Template_Import_Arsip_Program_SCM.xlsx');
    store.notify('Template Excel (.xlsx) berhasil diunduh.');
  } else {
    // CSV fallback
    const csvContent = [
      sampleHeaders.join(','),
      ...sampleRows.map(r => r.map(c => `"${c}"`).join(','))
    ].join('\r\n');
    const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'Template_Import_Arsip_Program_SCM.csv';
    link.click();
    URL.revokeObjectURL(link);
    store.notify('Template CSV berhasil diunduh.');
  }
}

async function executeImport() {
  if (parsedRows.value.length === 0 || isImporting.value) return;
  isImporting.value = true;
  errorMessage.value = '';
  try {
    await store.importPrograms(parsedRows.value);
    closeModal();
    router.push('/programs');
  } catch (err) {
    errorMessage.value = err.message || 'Gagal menyimpan data import ke server.';
  } finally {
    isImporting.value = false;
  }
}
</script>
