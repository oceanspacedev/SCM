<template>
  <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
    <!-- Table Container -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400 font-sans">
            <th class="py-3 px-5 font-bold">PROGRAM</th>
            <th class="py-3 px-5 font-bold">SUPPLIER</th>
            <th class="py-3 px-5 font-bold">NO. INVOICE</th>
            <th class="py-3 px-5 font-bold text-right">DPP</th>
            <th class="py-3 px-5 font-bold text-right">PPN</th>
            <th class="py-3 px-5 font-bold text-right">TOTAL INVOICE</th>
            <th class="py-3 px-5 font-bold text-center">DOKUMEN</th>
            <th class="py-3 px-5 font-bold text-center">STATUS</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr
            v-for="program in filteredPrograms"
            :key="program.id"
            class="hover:bg-slate-50/70 transition-colors group cursor-pointer"
            @click="goToDetail(program.id)"
          >
            <!-- PROGRAM -->
            <td class="py-3 px-5">
              <router-link
                :to="`/programs/${program.id}`"
                class="font-bold text-slate-900 group-hover:text-[#135A46] transition-colors line-clamp-1 block"
                @click.stop
              >
                {{ program.program_name }}
              </router-link>
              <div class="text-[11px] text-slate-400 mt-0.5">
                {{ program.category }} · {{ formatDate(program.program_date) }}
              </div>
            </td>

            <!-- SUPPLIER -->
            <td class="py-3 px-5">
              <div class="font-medium text-slate-800 line-clamp-1">
                {{ program.supplier }}
              </div>
              <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                {{ program.npwp || '02.887.123.4-041.000' }}
              </div>
            </td>

            <!-- NO. INVOICE -->
            <td class="py-3 px-5 font-mono text-slate-600 whitespace-nowrap">
              {{ program.invoice_number }}
            </td>

            <!-- DPP -->
            <td class="py-3 px-5 font-mono text-slate-700 text-right whitespace-nowrap">
              {{ formatRupiah(program.dpp) }}
            </td>

            <!-- PPN (Amber text matching screenshot) -->
            <td class="py-3 px-5 font-mono text-amber-700 font-medium text-right whitespace-nowrap">
              {{ formatRupiah(program.ppn) }}
            </td>

            <!-- TOTAL INVOICE -->
            <td class="py-3 px-5 font-mono font-bold text-slate-900 text-right whitespace-nowrap">
              {{ formatRupiah(program.total_invoice) }}
            </td>

            <!-- DOKUMEN (IN, FP, MO Pills - Click to View or Click to Upload) -->
            <td class="py-3 px-5 text-center whitespace-nowrap">
              <div class="inline-flex items-center gap-1.5" @click.stop>
                <!-- Invoice Pill -->
                <button
                  type="button"
                  class="px-2 py-0.5 rounded text-[10px] font-mono font-bold transition-all cursor-pointer select-none active:scale-95"
                  :class="hasDoc(program, 'invoice')
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:shadow-2xs'
                    : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-400'"
                  :title="hasDoc(program, 'invoice') ? 'Invoice tersedia — Klik untuk melihat' : 'Invoice belum ada — Klik untuk mengunggah'"
                  @click.stop="handlePillClick(program, 'invoice', 'Invoice')"
                >
                  <span v-if="!hasDoc(program, 'invoice')" class="mr-0.5 text-[9px] font-normal">+</span>IN
                </button>

                <!-- Faktur Pajak Pill -->
                <button
                  type="button"
                  class="px-2 py-0.5 rounded text-[10px] font-mono font-bold transition-all cursor-pointer select-none active:scale-95"
                  :class="hasDoc(program, 'faktur_pajak')
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:shadow-2xs'
                    : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-400'"
                  :title="hasDoc(program, 'faktur_pajak') ? 'Faktur Pajak tersedia — Klik untuk melihat' : 'Faktur Pajak belum ada — Klik untuk mengunggah'"
                  @click.stop="handlePillClick(program, 'faktur_pajak', 'Faktur Pajak')"
                >
                  <span v-if="!hasDoc(program, 'faktur_pajak')" class="mr-0.5 text-[9px] font-normal">+</span>FP
                </button>

                <!-- MOU Pill -->
                <button
                  type="button"
                  class="px-2 py-0.5 rounded text-[10px] font-mono font-bold transition-all cursor-pointer select-none active:scale-95"
                  :class="hasDoc(program, 'mou')
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:shadow-2xs'
                    : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-400'"
                  :title="hasDoc(program, 'mou') ? 'MOU tersedia — Klik untuk melihat' : 'MOU belum ada — Klik untuk mengunggah'"
                  @click.stop="handlePillClick(program, 'mou', 'MOU / Perjanjian')"
                >
                  <span v-if="!hasDoc(program, 'mou')" class="mr-0.5 text-[9px] font-normal">+</span>MO
                </button>
              </div>
            </td>

            <!-- STATUS (Badge matching screenshot) -->
            <td class="py-3 px-5 text-center whitespace-nowrap">
              <span
                :class="[
                  'inline-block px-3 py-1 rounded-full text-[11px] font-semibold tracking-tight border',
                  getStatusBadgeClass(program)
                ]"
              >
                {{ getStatusText(program) }}
              </span>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="filteredPrograms.length === 0">
            <td colspan="8" class="py-12 text-center">
              <div class="flex flex-col items-center justify-center space-y-2">
                <FolderArchive class="w-8 h-8 text-slate-300" />
                <p class="text-sm font-semibold text-slate-800">Tidak ada program ditemukan</p>
                <p class="text-xs text-slate-400 max-w-sm">
                  Coba sesuaikan kata kunci pencarian atau filter status dan supplier.
                </p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Table Footer / Summary -->
    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs text-slate-500">
      <div>
        Total Arsip: <strong class="text-slate-800 font-bold">{{ filteredPrograms.length }} Program</strong>
      </div>
      <div class="flex items-center gap-4 text-[11px] text-slate-500">
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>Hijau: Klik untuk <strong>Lihat Berkas</strong></span>
        </span>
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-slate-300"></span>
          <span>Abu-abu (+): Klik untuk <strong>Unggah Berkas</strong></span>
        </span>
      </div>
    </div>

    <!-- Document Preview Sheet -->
    <DocumentPreviewSheet
      v-model:open="isPreviewOpen"
      :document="activeDocument"
      :program="activeProgram"
    />

    <!-- Document Upload Modal -->
    <DocumentUploadModal
      v-model:open="isUploadOpen"
      :docType="uploadDocType"
      :docLabel="uploadDocLabel"
      @uploaded="handleUploaded"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { FolderArchive } from 'lucide-vue-next';
import { useTaxStore, formatRupiah, formatDate } from '../../store/taxStore';
import DocumentPreviewSheet from '../detail/DocumentPreviewSheet.vue';
import DocumentUploadModal from '../detail/DocumentUploadModal.vue';

const router = useRouter();
const store = useTaxStore();

const filteredPrograms = computed(() => store.filteredPrograms.value);

// Preview state
const isPreviewOpen = ref(false);
const activeDocument = ref(null);
const activeProgram = ref(null);

// Upload state
const isUploadOpen = ref(false);
const uploadTargetProgram = ref(null);
const uploadDocType = ref('invoice');
const uploadDocLabel = ref('Invoice');

function goToDetail(id) {
  router.push(`/programs/${id}`);
}

function hasDoc(program, docType) {
  if (!program.documents || !Array.isArray(program.documents)) return false;
  return program.documents.some((doc) => doc.document_type === docType);
}

function getDoc(program, docType) {
  if (!program.documents || !Array.isArray(program.documents)) return null;
  return program.documents.find((doc) => doc.document_type === docType);
}

function handlePillClick(program, docType, docLabel) {
  const existingDoc = getDoc(program, docType);
  if (existingDoc) {
    // Open preview sheet
    activeProgram.value = program;
    activeDocument.value = existingDoc;
    isPreviewOpen.value = true;
  } else {
    // Open upload modal
    uploadTargetProgram.value = program;
    uploadDocType.value = docType;
    uploadDocLabel.value = `${docLabel} (${program.program_name})`;
    isUploadOpen.value = true;
  }
}

function handleUploaded(fileData) {
  if (uploadTargetProgram.value) {
    store.uploadDocument(uploadTargetProgram.value.id, fileData.docType, fileData);
    isUploadOpen.value = false;
  }
}

function getStatusText(program) {
  const docs = program.documents || [];
  const hasInvoice = docs.some((d) => d.document_type === 'invoice');
  const hasFaktur = docs.some((d) => d.document_type === 'faktur_pajak');
  const hasMou = docs.some((d) => d.document_type === 'mou');

  if (docs.length === 3) return 'Dokumen Lengkap';
  if (docs.length === 0) return 'Belum Ada Dokumen';
  if (!hasFaktur && hasInvoice && hasMou) return 'Kurang Faktur Pajak';
  if (!hasInvoice && hasFaktur && hasMou) return 'Kurang Invoice';
  if (!hasMou && hasInvoice && hasFaktur) return 'Kurang Memo/MOU';
  if (!hasInvoice && !hasFaktur) return 'Kurang Inv & FP';
  if (!hasFaktur && !hasMou) return 'Kurang FP & MOU';
  return `Kurang ${3 - docs.length} Dokumen`;
}

function getStatusBadgeClass(program) {
  const docs = program.documents || [];
  if (docs.length === 3) {
    return 'bg-emerald-50 text-emerald-700 border-emerald-200';
  }
  if (docs.length === 0) {
    return 'bg-rose-50 text-rose-700 border-rose-200';
  }
  const hasInvoice = docs.some((d) => d.document_type === 'invoice');
  if (!hasInvoice) {
    return 'bg-rose-50 text-rose-700 border-rose-200';
  }
  return 'bg-amber-50 text-amber-800 border-amber-200';
}
</script>
