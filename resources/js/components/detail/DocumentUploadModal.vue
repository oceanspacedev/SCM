<template>
  <Dialog
    :open="open"
    :title="modalTitle"
    description="Unggah berkas resmi untuk verifikasi kepatuhan pajak SCM."
    maxWidth="max-w-md"
    @update:open="$emit('update:open', $event)"
  >
    <div class="space-y-4 text-sm">
      <!-- Drag & Drop Area -->
      <div
        class="border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer"
        :class="isDragging ? 'border-[#0F766E] bg-[#F0FDFA]' : 'border-[#DDE4E1] hover:border-[#CCD6D2] bg-[#FAFBFA]'"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleFileDrop"
        @click="$refs.fileInput.click()"
      >
        <input
          ref="fileInput"
          type="file"
          accept=".pdf, .jpg, .jpeg, .png"
          class="hidden"
          @change="handleFileChange"
        />

        <div class="flex flex-col items-center justify-center space-y-2">
          <div class="w-10 h-10 rounded-full bg-[#E6F4F1] text-[#0F766E] flex items-center justify-center">
            <Upload class="w-5 h-5" />
          </div>
          <div>
            <p class="text-sm font-semibold text-[#17201E]">
              Drop file here or browse
            </p>
            <p class="text-xs text-[#66736F] mt-0.5">
              Support: <strong>PDF, JPG, PNG</strong> (Maksimum 10 MB)
            </p>
          </div>
        </div>
      </div>

      <!-- File Selected State with Progress -->
      <div
        v-if="selectedFile"
        class="p-3 bg-white rounded-md border border-[#DDE4E1] shadow-2xs space-y-2.5"
      >
        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-2 truncate pr-2">
            <FileText class="w-4 h-4 text-[#0F766E] shrink-0" />
            <div class="truncate">
              <p class="font-medium text-[#17201E] truncate">{{ selectedFile.name }}</p>
              <p class="text-[11px] text-[#66736F]">{{ (selectedFile.size / 1024).toFixed(1) }} KB</p>
            </div>
          </div>
          <span class="text-xs font-semibold text-[#0F766E] shrink-0">
            {{ uploadProgress }}%
          </span>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-[#EBEFEF] rounded-full h-1.5 overflow-hidden">
          <div
            class="bg-[#0F766E] h-1.5 rounded-full transition-all duration-300"
            :style="{ width: uploadProgress + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex items-center justify-end gap-2">
        <Button
          variant="outline"
          size="sm"
          :disabled="isUploading"
          @click="$emit('update:open', false)"
        >
          Cancel
        </Button>
        <Button
          variant="default"
          size="sm"
          :disabled="!selectedFile || isUploading"
          :loading="isUploading"
          @click="startUpload"
        >
          Upload Document
        </Button>
      </div>
    </template>
  </Dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Upload, FileText } from 'lucide-vue-next';
import Dialog from '../ui/Dialog.vue';
import Button from '../ui/Button.vue';

const props = defineProps({
  open: {
    type: Boolean,
    default: false
  },
  docType: {
    type: String,
    default: 'faktur_pajak' // invoice, faktur_pajak, mou
  },
  docLabel: {
    type: String,
    default: 'Faktur Pajak'
  }
});

const emit = defineEmits(['update:open', 'uploaded']);

const isDragging = ref(false);
const selectedFile = ref(null);
const uploadProgress = ref(0);
const isUploading = ref(false);

const modalTitle = computed(() => {
  return `Upload ${props.docLabel}`;
});

watch(() => props.open, (isOpen) => {
  if (isOpen) {
    selectedFile.value = null;
    uploadProgress.value = 0;
    isUploading.value = false;
  }
});

function handleFileChange(e) {
  const file = e.target.files?.[0];
  if (file) {
    processFile(file);
  }
}

function handleFileDrop(e) {
  isDragging.value = false;
  const file = e.dataTransfer.files?.[0];
  if (file) {
    processFile(file);
  }
}

function processFile(file) {
  if (file.size > 10 * 1024 * 1024) {
    alert("Ukuran file melebihi batas maksimum 10 MB.");
    return;
  }
  selectedFile.value = file;
  uploadProgress.value = 25; // initial read progress
}

function startUpload() {
  if (!selectedFile.value) return;

  isUploading.value = true;
  uploadProgress.value = 45;

  setTimeout(() => {
    uploadProgress.value = 85;
    setTimeout(() => {
      uploadProgress.value = 100;
      setTimeout(() => {
        emit('uploaded', {
          docType: props.docType,
          file: selectedFile.value,
          name: selectedFile.value.name,
          sizeFormatted: `${(selectedFile.value.size / (1024 * 1024)).toFixed(1)} MB`,
          type: selectedFile.value.type || 'application/pdf'
        });
        isUploading.value = false;
        emit('update:open', false);
      }, 300);
    }, 400);
  }, 350);
}
</script>
