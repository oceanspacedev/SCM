<template>
  <Dialog
    :open="open"
    title="Import Data Program dari Excel"
    description="Impor massal metadata program dan invoice perpajakan dari file .xlsx atau .xls."
    maxWidth="max-w-3xl"
    @update:open="$emit('update:open', $event)"
  >
    <!-- Stepper Indicator -->
    <div class="mb-6 border-b border-[#DDE4E1] pb-4">
      <div class="flex items-center justify-between max-w-xl mx-auto">
        <div
          v-for="(step, idx) in steps"
          :key="step.number"
          class="flex items-center gap-2"
        >
          <div
            :class="[
              'w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold transition-colors',
              currentStep === step.number
                ? 'bg-[#0F766E] text-white ring-4 ring-[#CCFBF1]'
                : currentStep > step.number
                ? 'bg-[#15803D] text-white'
                : 'bg-[#EBEFEF] text-[#66736F]'
            ]"
          >
            <Check v-if="currentStep > step.number" class="w-3.5 h-3.5" />
            <span v-else>{{ step.number }}</span>
          </div>
          <span
            :class="[
              'text-xs font-medium',
              currentStep === step.number ? 'text-[#17201E] font-semibold' : 'text-[#66736F]'
            ]"
          >
            {{ step.title }}
          </span>
          <div
            v-if="idx < steps.length - 1"
            class="w-8 h-px bg-[#DDE4E1] mx-1"
          ></div>
        </div>
      </div>
    </div>

    <!-- STEP 1: Upload Excel -->
    <div v-if="currentStep === 1" class="space-y-4">
      <!-- Drag & Drop Area -->
      <div
        class="border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer"
        :class="isDragging ? 'border-[#0F766E] bg-[#F0FDFA]' : 'border-[#DDE4E1] hover:border-[#CCD6D2] bg-[#FAFBFA]'"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleFileDrop"
        @click="$refs.fileInput.click()"
      >
        <input
          ref="fileInput"
          type="file"
          accept=".xlsx, .xls, .csv"
          class="hidden"
          @change="handleFileSelect"
        />

        <div class="flex flex-col items-center justify-center space-y-3">
          <div class="w-12 h-12 rounded-full bg-[#E6F4F1] text-[#0F766E] flex items-center justify-center">
            <Upload class="w-6 h-6" />
          </div>
          <div>
            <p class="text-sm font-semibold text-[#17201E]">
              Drop file Excel di sini atau <span class="text-[#0F766E] underline">Pilih File</span>
            </p>
            <p class="text-xs text-[#66736F] mt-1">
              Format yang didukung: <strong>.xlsx, .xls</strong> (Maksimum 15 MB)
            </p>
          </div>

          <div v-if="selectedFile" class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded border border-[#0F766E] text-xs font-medium text-[#0F766E]">
            <FileSpreadsheet class="w-4 h-4" />
            <span>{{ selectedFile.name }} ({{ (selectedFile.size / 1024).toFixed(1) }} KB)</span>
          </div>
        </div>
      </div>

      <!-- Instructions & Template Download -->
      <div class="p-3.5 bg-white rounded-md border border-[#DDE4E1] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
        <div>
          <p class="font-semibold text-[#17201E]">Belum punya format Excel baku?</p>
          <p class="text-[#66736F] mt-0.5">
            Unduh template standar kolom SCM TaxVault agar terhindar dari galat validasi.
          </p>
        </div>
        <button
          type="button"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-[#DDE4E1] bg-[#F7F8F7] hover:bg-[#EAEFEF] text-[#17201E] font-medium transition-colors cursor-pointer shrink-0"
          @click="downloadTemplate"
        >
          <Download class="w-3.5 h-3.5 text-[#0F766E]" />
          <span>Download Template Excel</span>
        </button>
      </div>

      <!-- Format Note -->
      <div class="p-3 bg-[#FFFBEB] rounded-md border border-[#FDE68A] text-xs text-[#B45309]">
        <strong>Perhatian:</strong> Excel hanya digunakan untuk metadata program & invoice. Dokumen PDF/Faktur wajib diunggah secara terpisah melalui menu Detail Program.
      </div>
    </div>

    <!-- STEP 2: Preview -->
    <div v-else-if="currentStep === 2" class="space-y-4">
      <div class="flex items-center justify-between text-xs">
        <span class="text-[#66736F]">
          Terbaca <strong>{{ parsedRows.length }} baris data</strong> dari file: <code class="font-mono text-[#17201E]">{{ selectedFile?.name }}</code>
        </span>
        <span class="text-[11px] text-[#0F766E] bg-[#F0FDFA] px-2 py-0.5 rounded border border-[#CCFBF1]">
          Tahap Verifikasi Awal
        </span>
      </div>

      <!-- Preview Table -->
      <div class="border border-[#DDE4E1] rounded-md overflow-x-auto max-h-72">
        <table class="w-full text-left text-xs">
          <thead class="bg-[#FAFBFA] sticky top-0 border-b border-[#DDE4E1] text-[#66736F] font-semibold uppercase tracking-wider">
            <tr>
              <th class="p-2.5">No</th>
              <th class="p-2.5">Nama Program</th>
              <th class="p-2.5">Supplier</th>
              <th class="p-2.5">Invoice</th>
              <th class="p-2.5 text-right">DPP</th>
              <th class="p-2.5 text-right">PPN</th>
              <th class="p-2.5 text-right">Total</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#EBEFEF]">
            <tr
              v-for="(row, i) in parsedRows"
              :key="i"
              class="hover:bg-[#FAFBFA]"
            >
              <td class="p-2.5 text-[#66736F] font-mono">{{ i + 1 }}</td>
              <td class="p-2.5 font-medium text-[#17201E]">{{ row.program_name }}</td>
              <td class="p-2.5 text-[#17201E]">{{ row.supplier || '-' }}</td>
              <td class="p-2.5 font-mono text-[#17201E]">{{ row.invoice_number || '-' }}</td>
              <td class="p-2.5 text-right font-mono">{{ formatRupiah(row.dpp) }}</td>
              <td class="p-2.5 text-right font-mono">{{ formatRupiah(row.ppn) }}</td>
              <td class="p-2.5 text-right font-mono font-semibold text-[#0F766E]">{{ formatRupiah(row.total_invoice) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- STEP 3: Validation -->
    <div v-else-if="currentStep === 3" class="space-y-4">
      <!-- Validation Summary Box -->
      <div class="grid grid-cols-2 gap-3">
        <div class="p-3.5 bg-[#F0FDF4] rounded-lg border border-[#BBF7D0] flex items-center justify-between">
          <div class="flex items-center gap-2">
            <Check class="w-4 h-4 text-[#15803D]" />
            <span class="text-xs font-semibold text-[#15803D]">Data Siap Import</span>
          </div>
          <span class="text-base font-bold text-[#15803D]">{{ validRows.length }} rows valid</span>
        </div>

        <div class="p-3.5 bg-[#FEF2F2] rounded-lg border border-[#FECACA] flex items-center justify-between">
          <div class="flex items-center gap-2">
            <AlertTriangle class="w-4 h-4 text-[#B91C1C]" />
            <span class="text-xs font-semibold text-[#B91C1C]">Data Bermasalah</span>
          </div>
          <span class="text-base font-bold text-[#B91C1C]">{{ invalidRows.length }} rows invalid</span>
        </div>
      </div>

      <!-- Specific Validation Errors -->
      <div v-if="invalidRows.length > 0" class="space-y-2">
        <p class="text-xs font-semibold text-[#17201E]">Detail Galat Validasi Kolom:</p>
        <div class="border border-[#FECACA] rounded-md bg-[#FEF2F2]/50 divide-y divide-[#FECACA]/60 max-h-48 overflow-y-auto">
          <div
            v-for="err in invalidRows"
            :key="err.rowNum"
            class="p-2.5 text-xs flex items-start justify-between gap-4"
          >
            <div class="flex items-start gap-2">
              <span class="px-1.5 py-0.5 rounded bg-white border border-[#FECACA] font-mono text-[11px] font-bold text-[#B91C1C]">
                Row {{ err.rowNum }}
              </span>
              <div>
                <p class="font-medium text-[#17201E]">{{ err.program_name || 'Tanpa Nama Program' }}</p>
                <p class="text-[#B91C1C] text-[11px] mt-0.5">{{ err.errorReason }}</p>
              </div>
            </div>
            <span class="text-[10px] text-[#66736F] whitespace-nowrap">Dilewati saat import</span>
          </div>
        </div>
      </div>
      <div v-else class="p-4 bg-[#F0FDF4] rounded-md border border-[#BBF7D0] text-xs text-[#15803D] text-center">
        Semua {{ validRows.length }} baris data telah lolos validasi skema perpajakan dan siap diimpor.
      </div>
    </div>

    <!-- STEP 4: Import Confirmation & Success -->
    <div v-else-if="currentStep === 4" class="space-y-4 py-4 text-center">
      <div v-if="!importCompleted" class="space-y-3">
        <div class="w-12 h-12 rounded-full bg-[#F0FDFA] text-[#0F766E] mx-auto flex items-center justify-center border border-[#CCFBF1]">
          <FileSpreadsheet class="w-6 h-6" />
        </div>
        <h4 class="text-base font-semibold text-[#17201E]">
          Konfirmasi Impor {{ validRows.length }} Program
        </h4>
        <p class="text-xs text-[#66736F] max-w-md mx-auto">
          Data program dan metadata supplier akan ditambahkan ke sistem. Supplier baru akan didaftarkan secara otomatis tanpa membuat duplikasi.
        </p>

        <div class="p-3 bg-[#FAFBFA] border border-[#DDE4E1] rounded-md max-w-sm mx-auto text-xs text-left space-y-1">
          <div class="flex justify-between">
            <span class="text-[#66736F]">Total Program Valid:</span>
            <strong class="text-[#17201E] font-mono">{{ validRows.length }}</strong>
          </div>
          <div class="flex justify-between">
            <span class="text-[#66736F]">Total Nilai Tagihan:</span>
            <strong class="text-[#0F766E] font-mono">{{ formatRupiah(totalValidAmount) }}</strong>
          </div>
        </div>
      </div>

      <div v-else class="space-y-3">
        <div class="w-12 h-12 rounded-full bg-[#F0FDF4] text-[#15803D] mx-auto flex items-center justify-center border border-[#BBF7D0]">
          <Check class="w-6 h-6" />
        </div>
        <h4 class="text-base font-semibold text-[#17201E]">
          {{ validRows.length }} Program Berhasil Diimport!
        </h4>
        <p class="text-xs text-[#66736F] max-w-md mx-auto">
          Data telah masuk ke database arsip. Silakan masuk ke Detail Program untuk melengkapi upload berkas fisik (Invoice, Faktur Pajak, MOU).
        </p>
      </div>
    </div>

    <!-- Footer Actions -->
    <template #footer>
      <div class="w-full flex items-center justify-between">
        <button
          v-if="currentStep > 1 && currentStep < 4"
          type="button"
          class="text-xs text-[#66736F] hover:text-[#17201E] px-3 py-1.5 rounded hover:bg-[#EAEFEF] cursor-pointer"
          @click="currentStep--"
        >
          Kembali
        </button>
        <div v-else></div>

        <div class="flex items-center gap-2">
          <Button
            variant="outline"
            size="sm"
            @click="handleCancel"
          >
            {{ importCompleted ? 'Selesai' : 'Batal' }}
          </Button>

          <Button
            v-if="currentStep === 1"
            variant="default"
            size="sm"
            :disabled="!selectedFile"
            @click="goToStep(2)"
          >
            Lanjut: Preview
          </Button>

          <Button
            v-else-if="currentStep === 2"
            variant="default"
            size="sm"
            @click="goToStep(3)"
          >
            Lanjut: Validasi
          </Button>

          <Button
            v-else-if="currentStep === 3"
            variant="default"
            size="sm"
            :disabled="validRows.length === 0"
            @click="goToStep(4)"
          >
            Siap Import ({{ validRows.length }})
          </Button>

          <Button
            v-else-if="currentStep === 4 && !importCompleted"
            variant="default"
            size="sm"
            :loading="isImporting"
            @click="executeImport"
          >
            Import {{ validRows.length }} Program
          </Button>
        </div>
      </div>
    </template>
  </Dialog>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Upload, Download, Check, AlertTriangle, FileSpreadsheet } from 'lucide-vue-next';
import Dialog from '../ui/Dialog.vue';
import Button from '../ui/Button.vue';
import { useTaxStore, formatRupiah } from '../../store/taxStore';

const props = defineProps({
  open: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:open']);

const store = useTaxStore();

const steps = [
  { number: 1, title: 'Upload Excel' },
  { number: 2, title: 'Preview' },
  { number: 3, title: 'Validasi' },
  { number: 4, title: 'Import' },
];

const currentStep = ref(1);
const isDragging = ref(false);
const selectedFile = ref(null);
const isImporting = ref(false);
const importCompleted = ref(false);

// Seed sample parsed rows for high-fidelity demonstration
const sampleData = [
  { program_name: "Pengadaan Rak Heavy Duty Medan Hub", category: "Logistik", program_date: "2025-07-02", supplier: "PT Samudera Perkasa Abadi", npwp: "02.887.123.4-041.000", invoice_number: "INV/SPA/2025/1120", dpp: 450000000, ppn: 49500000, total_invoice: 499500000 },
  { program_name: "Sewa Truk Wingbox Rute Surabaya - Bali", category: "Logistik", program_date: "2025-07-05", supplier: "PT Cipta Logistik Nusantara", npwp: "01.345.678.9-012.000", invoice_number: "INV/CLP/2025/1402", dpp: 320000000, ppn: 35200000, total_invoice: 355200000 },
  { program_name: "Upgrade Server Gateway Gudang Pusat", category: "IT & Software", program_date: "2025-07-10", supplier: "PT Global Solusi Informatika", npwp: "03.221.456.7-052.000", invoice_number: "INV/GSI/2025/0891", dpp: 620000000, ppn: 68200000, total_invoice: 688200000 },
  { program_name: "Pengadaan Pallet Plastik Food Grade", category: "Pengadaan Material", program_date: "2025-07-12", supplier: "PT Bahtera Niaga Sentosa", npwp: "02.441.789.0-033.000", invoice_number: "INV/BNS/2025/0932", dpp: 280000000, ppn: 30800000, total_invoice: 310800000 },
  { program_name: "Maintenance Conveyor Belts Plant 2", category: "Operasional", program_date: "2025-07-15", supplier: "PT Tridaya Rekayasa Industri", npwp: "03.778.901.2-064.000", invoice_number: "INV/TRI/2025/0612", dpp: 390000000, ppn: 42900000, total_invoice: 432900000 },
  { program_name: "Distribusi Cold Chain Produk Farmasi", category: "Distribusi", program_date: "2025-07-18", supplier: "PT Buana Sumber Makmur", npwp: "03.119.876.5-045.000", invoice_number: "INV/BSM/2025/0904", dpp: 540000000, ppn: 59400000, total_invoice: 599400000 },
  // Row 7 (invalid as requested in prompt requirement)
  { program_name: "Penyewaan Forklift Elektrik 3 Ton", category: "Operasional", program_date: "2025-07-20", supplier: "", npwp: "01.000.000.0-000.000", invoice_number: "INV/OP/2025/0122", dpp: 180000000, ppn: 19800000, total_invoice: 199800000, rowNum: 7, errorReason: "Supplier wajib diisi" },
  { program_name: "Pengadaan Barcode Scanner Nirkabel", category: "IT & Software", program_date: "2025-07-22", supplier: "PT Inti Sarana Solusindo", npwp: "01.443.210.9-055.000", invoice_number: "INV/ISS/2025/0431", dpp: 210000000, ppn: 23100000, total_invoice: 233100000 },
  { program_name: "Jasa Forwarding Kontainer Pelabuhan", category: "Logistik", program_date: "2025-07-25", supplier: "PT Kencana Lintas Laut", npwp: "03.442.110.8-067.000", invoice_number: "INV/KLL/2025/1042", dpp: 850000000, ppn: 93500000, total_invoice: 943500000 },
  { program_name: "Studi Emisi Karbon Armada Distribusi", category: "Jasa Konsultasi", program_date: "2025-07-28", supplier: "PT Sentosa Solusi Berkelanjutan", npwp: "01.773.456.2-031.000", invoice_number: "INV/SSB/2025/0319", dpp: 150000000, ppn: 16500000, total_invoice: 166500000 },
  // Row 11 (invalid as requested in prompt requirement)
  { program_name: "Pengadaan Plastik Wrapping Ekspor", category: "Pengadaan Material", program_date: "2025-07-30", supplier: "PT Cahaya Megah Abadi", npwp: "01.554.890.1-011.000", invoice_number: "", dpp: 120000000, ppn: 13200000, total_invoice: 133200000, rowNum: 11, errorReason: "Nomor invoice tidak valid (kosong atau format tidak sesuai)" },
];

const parsedRows = ref([...sampleData]);

const validRows = computed(() => {
  return parsedRows.value.filter(r => !r.errorReason);
});

const invalidRows = computed(() => {
  return parsedRows.value.filter(r => r.errorReason);
});

const totalValidAmount = computed(() => {
  return validRows.value.reduce((sum, r) => sum + (Number(r.total_invoice) || 0), 0);
});

function handleFileSelect(e) {
  const file = e.target.files?.[0];
  if (file) {
    selectedFile.value = file;
    currentStep.value = 2;
  }
}

function handleFileDrop(e) {
  isDragging.value = false;
  const file = e.dataTransfer.files?.[0];
  if (file) {
    selectedFile.value = file;
    currentStep.value = 2;
  }
}

function goToStep(step) {
  currentStep.value = step;
}

function executeImport() {
  isImporting.value = true;
  setTimeout(() => {
    store.importPrograms(validRows.value);
    isImporting.value = false;
    importCompleted.value = true;
  }, 700);
}

function handleCancel() {
  currentStep.value = 1;
  selectedFile.value = null;
  importCompleted.value = false;
  emit('update:open', false);
}

function downloadTemplate() {
  const headers = "program_name,category,program_date,supplier,npwp,invoice_number,dpp,ppn,total_invoice";
  const row1 = "Program Optimasi Distribusi Cikarang,Logistik,2025-08-01,PT Cipta Logistik Nusantara,01.345.678.9-012.000,INV/CLP/2025/2001,500000000,55000000,555000000";
  const content = "\uFEFF" + headers + "\r\n" + row1;
  const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = "Template_Import_SCM_TaxVault.csv";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
</script>
