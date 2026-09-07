<template>
  <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs overflow-hidden">
    <!-- Header with Indicator & Progress Bar -->
    <div class="p-5 border-b border-[#DDE4E1]">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-[#17201E]">
            Dokumen Program
          </h3>
          <p class="text-xs text-[#66736F] mt-0.5">
            Kelola berkas perpajakan dan arsip kontrak pengadaan
          </p>
        </div>

        <!-- Indicator & Progress bar -->
        <div class="flex items-center gap-3 bg-[#FAFBFA] px-3.5 py-2 rounded-md border border-[#DDE4E1]">
          <div>
            <div class="flex items-center justify-between text-xs gap-3">
              <span class="font-medium text-[#17201E]">{{ uploadedCount }} dari 3 dokumen tersedia</span>
              <span
                :class="[
                  'text-[11px] font-semibold px-1.5 py-0.2 rounded border',
                  uploadedCount === 3
                    ? 'bg-[#F0FDF4] text-[#15803D] border-[#BBF7D0]'
                    : uploadedCount > 0
                    ? 'bg-[#FFFBEB] text-[#B45309] border-[#FDE68A]'
                    : 'bg-[#FEF2F2] text-[#B91C1C] border-[#FECACA]'
                ]"
              >
                {{ completenessLabel }}
              </span>
            </div>
            <!-- Small Progress Bar -->
            <div class="w-48 bg-[#EBEFEF] rounded-full h-1.5 mt-1.5 overflow-hidden">
              <div
                :class="[
                  'h-1.5 rounded-full transition-all duration-500',
                  uploadedCount === 3 ? 'bg-[#15803D]' : 'bg-[#0F766E]'
                ]"
                :style="{ width: progressPercent + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Document List (Clean Table / List format, NOT 3 huge cards!) -->
    <div class="divide-y divide-[#EBEFEF]">
      <div
        v-for="item in docTypes"
        :key="item.type"
        class="p-4 sm:px-6 hover:bg-[#FAFBFA] transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4"
      >
        <!-- Left: Doc Type & Filename -->
        <div class="flex items-start gap-3.5">
          <div
            :class="[
              'w-9 h-9 rounded-md flex items-center justify-center shrink-0 border',
              item.doc
                ? 'bg-[#F0FDFA] text-[#0F766E] border-[#CCFBF1]'
                : 'bg-[#F4F6F5] text-[#94A3B8] border-[#DDE4E1]'
            ]"
          >
            <FileText v-if="item.doc" class="w-4 h-4" />
            <FileCheck v-else class="w-4 h-4" />
          </div>

          <div>
            <div class="flex items-center gap-2">
              <h4 class="text-sm font-semibold text-[#17201E]">
                {{ item.title }}
              </h4>
              <span class="text-[10px] font-mono uppercase px-1.5 py-0.2 rounded bg-[#F4F6F5] text-[#66736F] border border-[#DDE4E1]">
                PDF
              </span>
            </div>

            <!-- Filename or Missing label -->
            <p v-if="item.doc" class="text-xs font-mono text-[#0F766E] mt-0.5">
              {{ item.doc.file_name }}
            </p>
            <p v-else class="text-xs text-[#94A3B8] italic mt-0.5">
              Berkas belum diunggah
            </p>

            <!-- Metadata info -->
            <div class="flex items-center gap-3 text-[11px] text-[#66736F] mt-1">
              <span v-if="item.doc">Ukuran: {{ item.doc.file_size || '1.2 MB' }}</span>
              <span v-if="item.doc">•</span>
              <span v-if="item.doc">Diunggah: {{ item.doc.uploaded_at }}</span>
              <span v-if="item.doc && item.doc.uploaded_by">• oleh {{ item.doc.uploaded_by }}</span>
            </div>
          </div>
        </div>

        <!-- Center: Status Badge -->
        <div class="flex items-center gap-2 self-start sm:self-center">
          <Badge
            v-if="item.doc"
            variant="success"
            dot
          >
            Uploaded
          </Badge>
          <Badge
            v-else
            variant="danger"
            dot
          >
            Missing
          </Badge>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
          <template v-if="item.doc">
            <!-- View Button -->
            <Button
              variant="outline"
              size="sm"
              @click="$emit('view', item.doc)"
            >
              <Eye class="w-3.5 h-3.5 text-[#0F766E]" />
              <span>View</span>
            </Button>

            <!-- Download Button -->
            <Button
              variant="ghost"
              size="sm"
              @click="$emit('download', item.doc)"
              title="Unduh File"
            >
              <Download class="w-3.5 h-3.5 text-[#66736F]" />
              <span class="hidden md:inline">Download</span>
            </Button>

            <!-- Replace Button -->
            <Button
              variant="ghost"
              size="sm"
              @click="$emit('upload', { type: item.type, label: item.title })"
              title="Ganti File"
            >
              <RefreshCw class="w-3.5 h-3.5 text-[#66736F]" />
              <span class="hidden md:inline">Replace</span>
            </Button>

            <!-- Delete Button -->
            <button
              type="button"
              class="text-[#B91C1C] hover:bg-[#FEF2F2] p-1.5 rounded transition-colors cursor-pointer"
              title="Hapus Dokumen"
              @click="$emit('delete', item.type)"
            >
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </template>

          <template v-else>
            <!-- Upload Button -->
            <Button
              variant="default"
              size="sm"
              @click="$emit('upload', { type: item.type, label: item.title })"
            >
              <Upload class="w-3.5 h-3.5" />
              <span>Upload</span>
            </Button>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { FileText, FileCheck, Eye, Download, Upload, RefreshCw, Trash2 } from 'lucide-vue-next';
import Badge from '../ui/Badge.vue';
import Button from '../ui/Button.vue';

const props = defineProps({
  program: {
    type: Object,
    required: true
  }
});

defineEmits(['view', 'download', 'upload', 'delete']);

const docTypes = computed(() => {
  const docs = props.program.documents || [];
  const invoiceDoc = docs.find(d => d.document_type === 'invoice');
  const fakturDoc = docs.find(d => d.document_type === 'faktur_pajak');
  const mouDoc = docs.find(d => d.document_type === 'mou');

  return [
    {
      type: 'invoice',
      title: 'Invoice',
      doc: invoiceDoc
    },
    {
      type: 'faktur_pajak',
      title: 'Faktur Pajak',
      doc: fakturDoc
    },
    {
      type: 'mou',
      title: 'Memo / MOU',
      doc: mouDoc
    }
  ];
});

const uploadedCount = computed(() => (props.program.documents || []).length);
const progressPercent = computed(() => Math.round((uploadedCount.value / 3) * 100));

const completenessLabel = computed(() => {
  if (uploadedCount.value === 3) return 'Lengkap';
  if (uploadedCount.value > 0) return 'Sebagian';
  return 'Belum Lengkap';
});
</script>
