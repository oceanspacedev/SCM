<template>
  <Sheet
    :open="open"
    :title="documentTitle"
    :description="document?.file_name || 'Berkas Terverifikasi'"
    width="max-w-2xl"
    @update:open="$emit('update:open', $event)"
  >
    <template #headerActions>
      <button
        type="button"
        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded border border-[#DDE4E1] bg-white hover:bg-[#F4F6F5] text-[#17201E] cursor-pointer"
        @click="handleDownload"
      >
        <Download class="w-3.5 h-3.5 text-[#0F766E]" />
        <span>Download</span>
      </button>
    </template>

    <div class="space-y-4">
      <!-- Document Metadata Banner -->
      <div class="p-3 bg-[#FAFBFA] rounded-md border border-[#DDE4E1] flex flex-wrap items-center justify-between gap-2 text-xs">
        <div class="flex items-center gap-2">
          <FileText class="w-4 h-4 text-[#0F766E]" />
          <span class="font-mono text-[#17201E] font-medium">{{ document?.file_name }}</span>
        </div>
        <div class="flex items-center gap-3 text-[#66736F]">
          <span>Ukuran: <strong class="text-[#17201E]">{{ document?.file_size || '1.2 MB' }}</strong></span>
          <span>•</span>
          <span>Diunggah: <strong class="text-[#17201E]">{{ document?.uploaded_at || 'Baru saja' }}</strong></span>
        </div>
      </div>

      <!-- Tab switch if real file exists -->
      <div v-if="actualFileUrl" class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs">
        <button
          type="button"
          :class="[
            'px-3 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer',
            activeTab === 'actual' ? 'bg-[#135A46] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
          ]"
          @click="activeTab = 'actual'"
        >
          Berkas Asli ({{ document?.file_name }})
        </button>
        <button
          type="button"
          :class="[
            'px-3 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer',
            activeTab === 'template' ? 'bg-[#135A46] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
          ]"
          @click="activeTab = 'template'"
        >
          Template Data Perpajakan
        </button>
      </div>

      <!-- VIEW 1: ACTUAL UPLOADED FILE -->
      <div v-if="actualFileUrl && activeTab === 'actual'" class="space-y-2">
        <div class="flex items-center justify-between text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg">
          <span class="text-slate-600 font-mono truncate max-w-sm">{{ document?.file_name }}</span>
          <a
            :href="actualFileUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1 font-semibold text-[#135A46] hover:underline"
          >
            <ExternalLink class="w-3.5 h-3.5" />
            Buka di Tab Baru
          </a>
        </div>

        <!-- PDF viewer -->
        <div v-if="isPdf" class="w-full bg-slate-100 rounded-xl overflow-hidden border border-slate-200">
          <iframe
            :src="actualFileUrl"
            class="w-full h-[620px] border-none rounded-xl"
          ></iframe>
        </div>

        <!-- Image viewer -->
        <div v-else-if="isImage" class="w-full p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-center min-h-[400px]">
          <img
            :src="actualFileUrl"
            :alt="document?.file_name"
            class="max-h-[620px] w-auto max-w-full rounded-lg shadow-sm border border-slate-200 object-contain"
          />
        </div>

        <!-- Generic file fallback -->
        <div v-else class="p-8 text-center bg-slate-50 rounded-xl border border-slate-200 space-y-3">
          <FileText class="w-12 h-12 text-slate-400 mx-auto" />
          <p class="font-bold text-slate-900 text-sm">{{ document?.file_name }}</p>
          <p class="text-xs text-slate-500">Berkas ini dapat Anda unduh langsung ke komputer.</p>
          <button
            type="button"
            class="px-4 py-2 bg-[#135A46] text-white rounded-lg text-xs font-semibold hover:bg-[#0E4636] transition-colors inline-flex items-center gap-1.5"
            @click="handleDownload"
          >
            <Download class="w-3.5 h-3.5" />
            Unduh Berkas Ini
          </button>
        </div>
      </div>

      <!-- VIEW 2: TEMPLATE CANVAS (For template tab or when no real file) -->
      <div v-else class="bg-white border border-[#DDE4E1] shadow-xs rounded-sm p-6 text-[#17201E] font-sans text-xs">
        <!-- RENDER FAKTUR PAJAK (DJP e-Faktur Style) -->
        <div v-if="document?.document_type === 'faktur_pajak'" class="space-y-4">
          <div class="text-center border-b-2 border-[#17201E] pb-3">
            <h4 class="text-sm font-bold uppercase tracking-wide">FAKTUR PAJAK</h4>
            <p class="font-mono text-xs mt-0.5">Kode dan Nomor Seri Faktur Pajak: 010.000-25.{{ String(program?.id).padStart(8, '0') }}</p>
          </div>

          <!-- Pengusaha Kena Pajak -->
          <div class="border border-[#DDE4E1] p-3 rounded-xs space-y-1">
            <p class="font-semibold text-[11px] text-[#66736F] uppercase">Pengusaha Kena Pajak (Penjual)</p>
            <div class="grid grid-cols-3 gap-1 pt-1">
              <span class="text-[#66736F]">Nama</span>
              <span class="col-span-2 font-medium">{{ program?.supplier }}</span>
              <span class="text-[#66736F]">NPWP</span>
              <span class="col-span-2 font-mono font-medium">{{ program?.npwp || '01.345.678.9-012.000' }}</span>
              <span class="text-[#66736F]">Alamat</span>
              <span class="col-span-2 text-[#66736F]">Kawasan Industri Modern Cikarang Blok B No. 12, Jawa Barat</span>
            </div>
          </div>

          <!-- Pembeli Barang / Penerima Jasa -->
          <div class="border border-[#DDE4E1] p-3 rounded-xs space-y-1">
            <p class="font-semibold text-[11px] text-[#66736F] uppercase">Pembeli Barang Kena Pajak / Penerima Jasa Kena Pajak</p>
            <div class="grid grid-cols-3 gap-1 pt-1">
              <span class="text-[#66736F]">Nama</span>
              <span class="col-span-2 font-semibold">PT SCM ENTERPRISE LOGISTIK INDONESIA</span>
              <span class="text-[#66736F]">NPWP</span>
              <span class="col-span-2 font-mono font-medium">01.000.987.6-021.000</span>
              <span class="text-[#66736F]">Alamat</span>
              <span class="col-span-2 text-[#66736F]">Gedung SCM Tower Lt. 18, Jl. Jend. Sudirman Kav. 25, Jakarta Selatan</span>
            </div>
          </div>

          <!-- Detail Barang / Jasa -->
          <table class="w-full border border-[#DDE4E1] text-left">
            <thead class="bg-[#FAFBFA] border-b border-[#DDE4E1] text-[10px] uppercase font-semibold text-[#66736F]">
              <tr>
                <th class="p-2 border-r border-[#DDE4E1]">No</th>
                <th class="p-2 border-r border-[#DDE4E1]">Nama Barang / Jasa Kena Pajak</th>
                <th class="p-2 text-right">Harga Jual / Penggantian</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#EBEFEF]">
              <tr>
                <td class="p-2 border-r border-[#DDE4E1] text-center font-mono">1</td>
                <td class="p-2 border-r border-[#DDE4E1]">
                  <p class="font-medium">{{ program?.program_name }}</p>
                  <p class="text-[10px] text-[#66736F]">Ref Invoice: {{ program?.invoice_number }}</p>
                </td>
                <td class="p-2 text-right font-mono font-medium">{{ formatRupiah(program?.dpp) }}</td>
              </tr>
            </tbody>
            <tfoot class="border-t border-[#DDE4E1] bg-[#FAFBFA] font-medium text-[11px]">
              <tr>
                <td colspan="2" class="p-2 border-r border-[#DDE4E1] text-right">Dasar Pengenaan Pajak (DPP)</td>
                <td class="p-2 text-right font-mono">{{ formatRupiah(program?.dpp) }}</td>
              </tr>
              <tr>
                <td colspan="2" class="p-2 border-r border-[#DDE4E1] text-right">PPN (11%)</td>
                <td class="p-2 text-right font-mono font-bold text-[#0F766E]">{{ formatRupiah(program?.ppn) }}</td>
              </tr>
              <tr class="bg-[#F0FDFA] font-bold">
                <td colspan="2" class="p-2 border-r border-[#DDE4E1] text-right text-[#0F766E]">Total Pembayaran</td>
                <td class="p-2 text-right font-mono text-[#0F766E]">{{ formatRupiah(program?.total_invoice) }}</td>
              </tr>
            </tfoot>
          </table>

          <!-- QR Verification & Signature -->
          <div class="pt-3 border-t border-[#DDE4E1] flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-14 h-14 bg-[#17201E] text-white flex items-center justify-center text-[10px] font-mono p-1 rounded-xs">
                [QR-DJP]
              </div>
              <div class="text-[10px] text-[#66736F]">
                <p class="font-semibold text-[#17201E]">DJP Validated e-Faktur</p>
                <p>Status: APPROVED BY DJP</p>
                <p class="font-mono">Tanggal Approval: {{ program?.program_date }}</p>
              </div>
            </div>
            <div class="text-right text-[10px]">
              <p>Jakarta, {{ formatDate(program?.program_date) }}</p>
              <p class="font-semibold mt-6 text-[#17201E] underline">{{ program?.supplier }}</p>
              <p class="text-[#66736F]">Kuasa Direksi / Bagian Pajak</p>
            </div>
          </div>
        </div>

        <!-- RENDER INVOICE KOMERSIAL -->
        <div v-else-if="document?.document_type === 'invoice'" class="space-y-4">
          <div class="flex items-start justify-between border-b-2 border-[#0F766E] pb-4">
            <div>
              <h3 class="text-base font-bold text-[#0F766E]">{{ program?.supplier }}</h3>
              <p class="text-[11px] text-[#66736F] mt-0.5">NPWP: {{ program?.npwp }}</p>
              <p class="text-[11px] text-[#66736F]">Division of Supply Chain Partner Services</p>
            </div>
            <div class="text-right">
              <span class="text-xs font-bold uppercase tracking-wider text-[#66736F] block">COMMERCIAL INVOICE</span>
              <span class="font-mono font-bold text-sm text-[#17201E]">{{ program?.invoice_number }}</span>
              <p class="text-[11px] text-[#66736F] mt-0.5">Tanggal: {{ formatDate(program?.program_date) }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
              <p class="font-semibold text-[#66736F] uppercase text-[10px]">Ditagihkan Kepada:</p>
              <p class="font-bold text-[#17201E]">PT SCM Enterprise Logistik Indonesia</p>
              <p class="text-[#66736F]">Divisi Finance & Tax Compliance</p>
              <p class="text-[#66736F]">Jakarta, Indonesia</p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-[#66736F] uppercase text-[10px]">Ketentuan Pembayaran:</p>
              <p class="font-medium text-[#17201E]">Net 30 Days</p>
              <p class="text-[#66736F]">Rekening Bank: Mandiri Corporate</p>
              <p class="font-mono text-[#17201E]">123-00-9876543-1</p>
            </div>
          </div>

          <table class="w-full border border-[#DDE4E1] text-left">
            <thead class="bg-[#FAFBFA] border-b border-[#DDE4E1] text-[10px] uppercase font-semibold text-[#66736F]">
              <tr>
                <th class="p-2 border-r border-[#DDE4E1]">Deskripsi Layanan</th>
                <th class="p-2 text-right border-r border-[#DDE4E1]">Qty</th>
                <th class="p-2 text-right">Jumlah (IDR)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#EBEFEF]">
              <tr>
                <td class="p-2.5 border-r border-[#DDE4E1]">
                  <p class="font-semibold text-[#17201E]">{{ program?.program_name }}</p>
                  <p class="text-[10px] text-[#66736F]">Kategori: {{ program?.category }} - Pelaksanaan SCM 2025</p>
                </td>
                <td class="p-2.5 text-right border-r border-[#DDE4E1] font-mono">1 Paket</td>
                <td class="p-2.5 text-right font-mono font-medium">{{ formatRupiah(program?.dpp) }}</td>
              </tr>
            </tbody>
            <tfoot class="border-t border-[#DDE4E1] bg-[#FAFBFA] font-medium text-[11px]">
              <tr>
                <td colspan="2" class="p-2 border-r border-[#DDE4E1] text-right">Subtotal (DPP)</td>
                <td class="p-2 text-right font-mono">{{ formatRupiah(program?.dpp) }}</td>
              </tr>
              <tr>
                <td colspan="2" class="p-2 border-r border-[#DDE4E1] text-right">PPN 11%</td>
                <td class="p-2 text-right font-mono">{{ formatRupiah(program?.ppn) }}</td>
              </tr>
              <tr class="bg-[#F0FDFA] font-bold text-[#0F766E]">
                <td colspan="2" class="p-2 border-r border-[#DDE4E1] text-right">Total Tagihan</td>
                <td class="p-2 text-right font-mono">{{ formatRupiah(program?.total_invoice) }}</td>
              </tr>
            </tfoot>
          </table>

          <div class="pt-4 flex items-center justify-between border-t border-[#DDE4E1] text-[10px]">
            <div class="text-[#66736F]">
              <p>Stempel Digital Terverifikasi</p>
              <p class="font-mono text-[9px]">HASH: {{ String(program?.id * 987654).slice(0, 16) }}</p>
            </div>
            <div class="text-right">
              <span class="px-2 py-0.5 rounded bg-[#F0FDF4] text-[#15803D] font-semibold border border-[#BBF7D0]">
                PAID & RECONCILED
              </span>
            </div>
          </div>
        </div>

        <!-- RENDER MEMO / MOU -->
        <div v-else class="space-y-4">
          <div class="text-center border-b border-[#DDE4E1] pb-3">
            <h4 class="text-sm font-bold uppercase tracking-wide text-[#17201E]">
              MEMORANDUM OF UNDERSTANDING / SURAT PERJANJIAN KERJASAMA
            </h4>
            <p class="font-mono text-xs text-[#66736F] mt-0.5">Nomor: SPK/SCM-OP/2025/{{ String(program?.id).padStart(4, '0') }}</p>
          </div>

          <div class="text-xs leading-relaxed space-y-2 text-[#17201E]">
            <p>Pada hari ini, disepakati perjanjian kerjasama pelaksanaan program SCM antara:</p>
            <ol class="list-decimal pl-5 space-y-1 text-[#66736F]">
              <li><strong>PT SCM ENTERPRISE LOGISTIK INDONESIA</strong>, selanjutnya disebut sebagai PIHAK PERTAMA.</li>
              <li><strong>{{ program?.supplier }}</strong> (NPWP: {{ program?.npwp }}), selanjutnya disebut sebagai PIHAK KEDUA.</li>
            </ol>

            <div class="pt-2 border-t border-[#EBEFEF]">
              <p class="font-semibold text-[#17201E]">PASAL 1: RUANG LINGKUP PEKERJAAN</p>
              <p class="text-[#66736F]">PIHAK KEDUA berkewajiban melaksanakan <strong>{{ program?.program_name }}</strong> sesuai standar operasional yang telah disetujui bersama.</p>
            </div>

            <div class="pt-2 border-t border-[#EBEFEF]">
              <p class="font-semibold text-[#17201E]">PASAL 2: NILAI KONTRAK & KETENTUAN PERPAJAKAN</p>
              <p class="text-[#66736F]">
                Nilai pekerjaan adalah sebesar <strong>{{ formatRupiah(program?.dpp) }}</strong> (Dasar Pengenaan Pajak) ditambah PPN 11% sebesar <strong>{{ formatRupiah(program?.ppn) }}</strong>, dengan total perjanjian <strong>{{ formatRupiah(program?.total_invoice) }}</strong>. PIHAK KEDUA wajib menerbitkan Faktur Pajak e-Faktur valid sesuai regulasi DJP.
              </p>
            </div>
          </div>

          <div class="pt-6 border-t border-[#DDE4E1] grid grid-cols-2 gap-6 text-center text-xs">
            <div>
              <p class="text-[#66736F]">PIHAK PERTAMA</p>
              <p class="font-semibold text-[#17201E] mt-8 underline">Budi Santoso</p>
              <p class="text-[10px] text-[#66736F]">Head of SCM Operations</p>
            </div>
            <div>
              <p class="text-[#66736F]">PIHAK KEDUA</p>
              <p class="font-semibold text-[#17201E] mt-8 underline">{{ program?.supplier }}</p>
              <p class="text-[10px] text-[#66736F]">Direktur Utama / Perwakilan Sah</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="w-full flex items-center justify-between">
        <span class="text-xs text-[#66736F]">
          Status Verifikasi: <strong class="text-[#15803D]">Lolos Audit Compliance</strong>
        </span>
        <Button
          variant="outline"
          size="sm"
          @click="$emit('update:open', false)"
        >
          Tutup Preview
        </Button>
      </div>
    </template>
  </Sheet>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Download, FileText, ExternalLink } from 'lucide-vue-next';
import Sheet from '../ui/Sheet.vue';
import Button from '../ui/Button.vue';
import { formatRupiah, formatDate, useTaxStore } from '../../store/taxStore';

const props = defineProps({
  open: {
    type: Boolean,
    default: false
  },
  document: {
    type: Object,
    default: null
  },
  program: {
    type: Object,
    default: null
  }
});

defineEmits(['update:open']);

const store = useTaxStore();

const actualFileUrl = ref(null);
const activeTab = ref('actual'); // 'actual' | 'template'

watch(
  () => [props.open, props.document],
  async ([isOpen, doc]) => {
    if (isOpen && doc) {
      let url = doc.file_data || doc.file_url || null;
      if (!url && doc.id) {
        url = await store.loadDocumentContent(doc.id);
      }
      actualFileUrl.value = url;
      activeTab.value = url ? 'actual' : 'template';
    } else {
      actualFileUrl.value = null;
    }
  },
  { immediate: true }
);

const isPdf = computed(() => {
  const mime = props.document?.mime_type || '';
  const name = props.document?.file_name || '';
  const url = actualFileUrl.value || '';
  return mime.includes('pdf') || name.toLowerCase().endsWith('.pdf') || url.startsWith('data:application/pdf');
});

const isImage = computed(() => {
  const mime = props.document?.mime_type || '';
  const name = props.document?.file_name || '';
  const url = actualFileUrl.value || '';
  return mime.includes('image') || /\.(png|jpe?g|webp|gif|svg)$/i.test(name) || url.startsWith('data:image/');
});

const documentTitle = computed(() => {
  if (!props.document) return 'Preview Dokumen';
  return store.getDocTypeLabel(props.document.document_type);
});

function handleDownload() {
  if (!props.document) return;
  const fileName = props.document.file_name || `${props.document.document_type}-${props.program?.id}.pdf`;

  if (actualFileUrl.value) {
    const a = document.createElement('a');
    a.href = actualFileUrl.value;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    store.notify(`Dokumen ${fileName} berhasil diunduh.`);
    return;
  }

  // Simulated download fallback for demo records
  const blob = new Blob([`SCM TaxVault Document Archive: ${fileName}\nProgram: ${props.program?.program_name}\nSupplier: ${props.program?.supplier}\nInvoice: ${props.program?.invoice_number}`], { type: 'application/pdf' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = fileName;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
  store.notify(`Dokumen ${fileName} telah diunduh.`);
}
</script>
