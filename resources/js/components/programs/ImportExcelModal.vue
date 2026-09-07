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
      <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center shrink-0">
            <ExcelIcon class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900 leading-tight">
              Import Program & Penyimpanan Berkas Mentahan
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
              Unggah file Excel mentah ke SeaweedFS SCM dan ekstrak data program ke sistem
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

      <!-- Tab Navigation -->
      <div class="px-6 pt-3 border-b border-slate-200 bg-white flex items-center gap-4">
        <button
          type="button"
          class="pb-2.5 text-xs font-bold border-b-2 transition-all cursor-pointer flex items-center gap-2"
          :class="activeTab === 'upload' 
            ? 'border-[#135A46] text-[#135A46]' 
            : 'border-transparent text-slate-500 hover:text-slate-800'"
          @click="activeTab = 'upload'"
        >
          <UploadCloud class="w-4 h-4" />
          <span>Unggah & Import Data</span>
        </button>

        <button
          type="button"
          class="pb-2.5 text-xs font-bold border-b-2 transition-all cursor-pointer flex items-center gap-2"
          :class="activeTab === 'raw_archive' 
            ? 'border-[#135A46] text-[#135A46]' 
            : 'border-transparent text-slate-500 hover:text-slate-800'"
          @click="switchToRawArchive"
        >
          <Cloud class="w-4 h-4" />
          <span>Berkas Mentahan di SeaweedFS</span>
          <span 
            v-if="rawImports.length > 0" 
            class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-emerald-100 text-emerald-800"
          >
            {{ rawImports.length }}
          </span>
        </button>
      </div>

      <!-- TAB 1: UPLOAD & IMPORT -->
      <div v-if="activeTab === 'upload'" class="p-6 overflow-y-auto space-y-5 flex-1 text-xs">
        <!-- SeaweedFS Storage Notice -->
        <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/80 flex items-start justify-between gap-3 text-emerald-900">
          <div class="flex items-start gap-2.5">
            <Cloud class="w-4 h-4 text-emerald-700 mt-0.5 shrink-0" />
            <div class="text-[11px] leading-relaxed">
              <span class="font-bold">Penyimpanan Terintegrasi:</span> File fisik mentahan (.xlsx / .csv) yang Anda unggah akan otomatis disimpan ke bucket <strong class="font-mono text-emerald-800">SCM</strong> di <strong class="font-mono text-emerald-800">storage.completeselular.com</strong> (SeaweedFS). Anda dapat mengunduh dan memeriksa file mentahan kapan saja.
            </div>
          </div>
          <button
            type="button"
            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-emerald-300 text-[11px] font-bold text-emerald-800 hover:bg-emerald-50 transition-colors shadow-2xs cursor-pointer"
            @click="downloadTemplate('xlsx')"
          >
            <ExcelIcon class="w-3.5 h-3.5" />
            <span>Unduh Contoh Excel</span>
          </button>
        </div>

        <!-- Dropzone -->
        <div
          v-if="parsedRows.length === 0"
          class="border-2 border-dashed rounded-xl p-7 text-center cursor-pointer transition-colors"
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
              Mendukung format .xlsx, .xls, atau .csv (File mentah asli disimpan ke SeaweedFS)
            </p>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="py-8 text-center text-slate-500 flex flex-col items-center justify-center gap-2">
          <div class="w-6 h-6 border-2 border-[#135A46] border-t-transparent rounded-full animate-spin"></div>
          <p class="font-medium text-xs">Membaca dan memvalidasi file data...</p>
        </div>

        <!-- Error Banner -->
        <div
          v-if="errorMessage"
          class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3"
        >
          <AlertCircle class="w-4 h-4 text-rose-600 mt-0.5 shrink-0" />
          <div class="flex-1">
            <p class="font-bold">Gagal Memproses File</p>
            <p class="mt-0.5 text-rose-700 leading-relaxed">{{ errorMessage }}</p>
          </div>
          <button
            type="button"
            class="text-rose-500 hover:text-rose-700 p-1 cursor-pointer"
            @click="errorMessage = ''"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <!-- Parsed Data Preview -->
        <div v-if="parsedRows.length > 0 && !isLoading" class="space-y-3 animate-in fade-in duration-150">
          <!-- File Selected Info Banner -->
          <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center">
                <ExcelIcon class="w-4 h-4" />
              </div>
              <div>
                <p class="font-bold text-slate-900 text-xs flex items-center gap-2">
                  <span>{{ selectedRawFile?.name || 'Berkas Mentahan Excel' }}</span>
                  <span class="text-[10px] font-normal px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                    Siap upload ke SeaweedFS
                  </span>
                </p>
                <p class="text-[11px] text-slate-500">
                  {{ parsedRows.length }} baris data siap dimasukkan ke database Arsip Program
                </p>
              </div>
            </div>
            <button
              type="button"
              class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
              @click="resetFile"
            >
              Ganti File
            </button>
          </div>

          <!-- Preview Table -->
          <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
            <div class="overflow-x-auto max-h-72">
              <table class="w-full text-left text-xs border-collapse">
                <thead class="sticky top-0 bg-slate-100 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                  <tr>
                    <th class="py-2.5 px-3">NO</th>
                    <th class="py-2.5 px-3">PROGRAM</th>
                    <th class="py-2.5 px-3">SUPPLIER</th>
                    <th class="py-2.5 px-3">NO. INVOICE</th>
                    <th class="py-2.5 px-3 text-right">DPP</th>
                    <th class="py-2.5 px-3 text-right">PPN</th>
                    <th class="py-2.5 px-3 text-right">TOTAL INVOICE</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                  <tr
                    v-for="(row, idx) in parsedRows.slice(0, 15)"
                    :key="idx"
                    class="hover:bg-slate-50/70 transition-colors"
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

      <!-- TAB 2: DAFTAR BERKAS MENTAHAN DI SEAWEEDFS -->
      <div v-else-if="activeTab === 'raw_archive'" class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-slate-700">
          <div>
            <p class="font-bold text-slate-900 text-xs">Daftar File Mentahan di SeaweedFS SCM</p>
            <p class="text-[11px] text-slate-500 mt-0.5">
              Setiap kali file Excel diimpor, berkas mentahan aslinya tersimpan di sini sehingga Anda bisa memeriksa kembali isinya kapan saja.
            </p>
          </div>
          <button
            type="button"
            class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer"
            @click="store.fetchRawImports()"
          >
            Segarkan Data
          </button>
        </div>

        <!-- List of Raw Imports -->
        <div v-if="rawImports.length > 0" class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
          <div class="overflow-x-auto max-h-96">
            <table class="w-full text-left text-xs border-collapse">
              <thead class="sticky top-0 bg-slate-100 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                <tr>
                  <th class="py-2.5 px-4">NAMA FILE MENTAHAN</th>
                  <th class="py-2.5 px-3">UKURAN</th>
                  <th class="py-2.5 px-3">HASIL IMPORT</th>
                  <th class="py-2.5 px-3">TANGGAL UNGGAH</th>
                  <th class="py-2.5 px-3">STORAGE</th>
                  <th class="py-2.5 px-4 text-center">AKSI</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr
                  v-for="item in rawImports"
                  :key="item.id"
                  class="hover:bg-slate-50/70 transition-colors"
                >
                  <td class="py-3 px-4">
                    <div class="flex items-center gap-2.5">
                      <ExcelIcon class="w-4 h-4 shrink-0" />
                      <div>
                        <p class="font-bold text-slate-900 line-clamp-1" :title="item.file_name">
                          {{ item.file_name }}
                        </p>
                        <p class="text-[10px] font-mono text-slate-400 mt-0.5">
                          {{ item.file_key || 'mentahan_excel/' + item.file_name }}
                        </p>
                      </div>
                    </div>
                  </td>
                  <td class="py-3 px-3 font-mono text-slate-600 whitespace-nowrap">
                    {{ item.file_size || '-' }}
                  </td>
                  <td class="py-3 px-3 whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      {{ item.imported_rows_count || 0 }} Program
                    </span>
                  </td>
                  <td class="py-3 px-3 text-slate-500 whitespace-nowrap text-[11px]">
                    {{ formatDate(item.created_at) }}
                  </td>
                  <td class="py-3 px-3 whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                      SeaweedFS SCM
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center whitespace-nowrap">
                    <div class="inline-flex items-center gap-2">
                      <a
                        :href="'/api/programs/raw-imports/' + item.id + '/download'"
                        target="_blank"
                        :download="item.file_name"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-[#135A46] text-white text-[11px] font-semibold hover:bg-[#0e4334] transition-colors shadow-2xs cursor-pointer"
                        title="Unduh / Buka File Mentahan Asli"
                      >
                        <Download class="w-3.5 h-3.5" />
                        <span>Unduh File</span>
                      </a>
                      <button
                        type="button"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                        title="Hapus Catatan File Mentahan"
                        @click="store.deleteRawImport(item.id)"
                      >
                        <Trash2 class="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-14 text-center border-2 border-dashed border-slate-200 rounded-xl">
          <Cloud class="w-10 h-10 text-slate-300 mx-auto mb-2" />
          <p class="font-bold text-slate-700 text-sm">Belum Ada File Mentahan yang Tersimpan</p>
          <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
            File Excel mentahan akan otomatis tercatat dan tersimpan di SeaweedFS SCM setiap kali Anda melakukan import data.
          </p>
          <button
            type="button"
            class="mt-4 px-4 py-2 rounded-lg bg-[#135A46] text-white text-xs font-semibold hover:bg-[#0e4334] transition-colors cursor-pointer inline-flex items-center gap-2 shadow-2xs"
            @click="activeTab = 'upload'"
          >
            <UploadCloud class="w-4 h-4" />
            <span>Mulai Unggah File Excel</span>
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50/80 flex items-center justify-between">
        <span class="text-slate-500 text-xs">
          <template v-if="activeTab === 'upload'">
            {{ parsedRows.length > 0 ? `${parsedRows.length} data siap dimasukkan & disimpan ke SeaweedFS` : 'Pilih file terlebih dahulu' }}
          </template>
          <template v-else>
            {{ rawImports.length }} berkas mentahan tersimpan di SeaweedFS SCM
          </template>
        </span>
        <div class="flex items-center gap-2.5">
          <button
            type="button"
            class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
            @click="closeModal"
          >
            Tutup
          </button>
          <button
            v-if="activeTab === 'upload'"
            type="button"
            :disabled="parsedRows.length === 0 || isLoading || isImporting"
            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-[#135A46] text-white text-xs font-bold hover:bg-[#0e4334] disabled:opacity-50 disabled:cursor-not-allowed shadow-2xs transition-all cursor-pointer"
            @click="executeImport"
          >
            <UploadCloud v-if="!isImporting" class="w-4 h-4" />
            <span v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span>{{ isImporting ? 'Menyimpan ke SeaweedFS & Server...' : `Konfirmasi & Import ${parsedRows.length > 0 ? `${parsedRows.length} Data` : ''}` }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  UploadCloud,
  X,
  Info,
  Download,
  AlertCircle,
  CheckCircle2,
  Cloud,
  Trash2
} from 'lucide-vue-next';
import ExcelIcon from '../ui/ExcelIcon.vue';
import { useTaxStore, formatRupiah, formatDate } from '../../store/taxStore';

const router = useRouter();
const store = useTaxStore();

const isOpen = computed(() => store.isImportModalOpen.value);
const rawImports = computed(() => store.rawImports.value);

const activeTab = ref('upload');
const isDragging = ref(false);
const isLoading = ref(false);
const isImporting = ref(false);
const errorMessage = ref('');
const parsedRows = ref([]);
const selectedRawFile = ref(null);
const fileInputRef = ref(null);

function switchToRawArchive() {
  activeTab.value = 'raw_archive';
  store.fetchRawImports();
}

function closeModal() {
  resetFile();
  store.closeImportModal();
}

function resetFile() {
  parsedRows.value = [];
  selectedRawFile.value = null;
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

function normalizeKey(key) {
  return String(key || '')
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9]/g, '');
}

function cleanNumber(val) {
  if (val === null || val === undefined || val === '') return 0;
  if (typeof val === 'number') return val;
  const str = String(val)
    .replace(/rp/gi, '')
    .replace(/\s+/g, '')
    .replace(/\./g, '')
    .replace(/,/g, '.');
  const num = parseFloat(str);
  return isNaN(num) ? 0 : num;
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
  selectedRawFile.value = file;

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
    const ws = XLSX.utils.aoa_to_sheet([sampleHeaders, ...sampleRows]);

    ws['!cols'] = [
      { wch: 38 },
      { wch: 34 },
      { wch: 22 },
      { wch: 16 },
      { wch: 14 },
      { wch: 18 },
      { wch: 20 },
      { wch: 14 }
    ];

    XLSX.utils.book_append_sheet(wb, ws, 'Template Program');
    XLSX.writeFile(wb, 'Template_Import_Arsip_Program_SCM.xlsx');
    store.notify('Template Excel (.xlsx) berhasil diunduh.');
  }
}

async function executeImport() {
  if (parsedRows.value.length === 0 || isImporting.value) return;
  isImporting.value = true;
  errorMessage.value = '';
  try {
    await store.importPrograms(parsedRows.value, selectedRawFile.value);
    closeModal();
    router.push('/programs');
  } catch (err) {
    errorMessage.value = err.message || 'Gagal menyimpan data import ke server.';
  } finally {
    isImporting.value = false;
  }
}

onMounted(() => {
  store.fetchRawImports();
});
</script>
