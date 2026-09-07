<template>
  <div class="space-y-5 select-none">
    <!-- Page Header matching screenshot -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 font-sans">
          Arsip Program SCM
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">
          Daftar program beserta kelengkapan dokumen perpajakan
        </p>
      </div>

      <!-- Action Buttons matching screenshot -->
      <div class="flex items-center gap-2.5 self-start sm:self-auto">
        <!-- Import Excel (Neutral white style with authentic Excel icon) -->
        <button
          type="button"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition-colors cursor-pointer"
          @click="store.openImportModal()"
        >
          <ExcelIcon class="w-4 h-4" />
          <span>Import Excel</span>
        </button>

        <!-- Ekspor Excel -->
        <button
          type="button"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition-colors cursor-pointer"
          @click="handleExport"
        >
          <ExcelIcon class="w-4 h-4" />
          <span>Ekspor Excel</span>
        </button>

        <!-- Tambah Program -->
        <button
          type="button"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#135A46] text-white text-xs font-semibold hover:bg-[#0e4334] shadow-2xs transition-colors cursor-pointer"
          @click="isAddSheetOpen = true"
        >
          <Plus class="w-3.5 h-3.5" />
          <span>Tambah Program</span>
        </button>
      </div>
    </div>

    <!-- Filter & Summary Strip Card -->
    <ProgramToolbar />

    <!-- Program Table matching screenshot -->
    <ProgramTable />

    <!-- Add Program Sheet -->
    <AddProgramSheet
      v-model:open="isAddSheetOpen"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Download, Plus } from 'lucide-vue-next';
import ExcelIcon from '../components/ui/ExcelIcon.vue';
import ProgramToolbar from '../components/programs/ProgramToolbar.vue';
import ProgramTable from '../components/programs/ProgramTable.vue';
import AddProgramSheet from '../components/programs/AddProgramSheet.vue';
import { useTaxStore } from '../store/taxStore';

const store = useTaxStore();
const isAddSheetOpen = ref(false);

function handleExport() {
  store.exportToCsv();
}
</script>
