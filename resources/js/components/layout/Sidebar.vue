<template>
  <aside class="w-64 bg-[#F8FAFA] border-r border-slate-200 flex flex-col shrink-0 h-screen sticky top-0 z-20 select-none text-slate-700">
    <!-- Brand Header (aligned with h-16 Topbar) -->
    <div class="h-16 px-5 border-b border-slate-200 bg-white flex items-center">
      <router-link to="/dashboard" class="block group w-full">
        <div class="flex items-center gap-1.5">
          <span class="text-xs font-black uppercase tracking-wider text-[#135A46]">SCM</span>
          <span class="text-base font-bold tracking-tight text-slate-900">TaxVault</span>
        </div>
        <p class="text-[11px] text-slate-400 font-medium leading-tight mt-0.5">
          Arsip Dokumen Pajak
        </p>
      </router-link>
    </div>

    <!-- Navigation List -->
    <div class="px-3.5 py-5 flex-1 overflow-y-auto space-y-6">
      <!-- Main Nav -->
      <nav class="space-y-1.5">
        <!-- Dashboard Link -->
        <router-link
          to="/dashboard"
          v-slot="{ isActive }"
        >
          <div
            :class="[
              'h-10 px-3.5 rounded-xl text-xs transition-all cursor-pointer font-medium flex items-center justify-between',
              isActive
                ? 'bg-[#135A46] text-white shadow-sm font-semibold'
                : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900'
            ]"
          >
            <div class="flex items-center gap-3">
              <LayoutDashboard class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-500'" />
              <span>Dashboard</span>
            </div>
            <ChevronRight class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-400'" />
          </div>
        </router-link>

        <!-- Arsip Program Link -->
        <router-link
          to="/programs"
          v-slot="{ isActive }"
        >
          <div
            :class="[
              'h-10 px-3.5 rounded-xl text-xs transition-all cursor-pointer font-medium flex items-center justify-between',
              isActive
                ? 'bg-[#135A46] text-white shadow-sm font-semibold'
                : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900'
            ]"
          >
            <div class="flex items-center gap-3">
              <FolderArchive class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-500'" />
              <span>Arsip Program</span>
            </div>
            <ChevronRight class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-400'" />
          </div>
        </router-link>

        <!-- Manajemen User Link -->
        <router-link
          to="/users"
          v-slot="{ isActive }"
        >
          <div
            :class="[
              'h-10 px-3.5 rounded-xl text-xs transition-all cursor-pointer font-medium flex items-center justify-between',
              isActive
                ? 'bg-[#135A46] text-white shadow-sm font-semibold'
                : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900'
            ]"
          >
            <div class="flex items-center gap-3">
              <Users class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-500'" />
              <span>Manajemen User</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span
                v-if="pendingCount > 0"
                class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
                :class="isActive ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900'"
              >
                {{ pendingCount }}
              </span>
              <ChevronRight class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-400'" />
            </div>
          </div>
        </router-link>
      </nav>

      <!-- Section: TOOLS -->
      <div>
        <p class="px-3.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">
          Tools
        </p>
        <nav class="space-y-1.5">
          <button
            type="button"
            class="w-full h-10 px-3.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-200/60 hover:text-slate-900 transition-all cursor-pointer flex items-center justify-between"
            @click="openImportExcel"
          >
            <div class="flex items-center gap-3">
              <ExcelIcon class="w-4 h-4 shrink-0" />
              <span>Import Excel</span>
            </div>
          </button>

          <button
            type="button"
            class="w-full h-10 px-3.5 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-200/60 hover:text-slate-900 transition-all cursor-pointer flex items-center justify-between"
            @click="exportCsv"
          >
            <div class="flex items-center gap-3">
              <ExcelIcon class="w-4 h-4 shrink-0" />
              <span>Ekspor Excel</span>
            </div>
          </button>
        </nav>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { LayoutDashboard, FolderArchive, ChevronRight, Users } from 'lucide-vue-next';
import ExcelIcon from '../ui/ExcelIcon.vue';
import { useTaxStore } from '../../store/taxStore';

const router = useRouter();
const store = useTaxStore();

const isAdmin = computed(() => store.isAdmin.value);
const pendingCount = computed(() => store.pendingUsersCount.value);

function openImportExcel() {
  store.openImportModal();
}

function exportCsv() {
  store.exportToCsv();
}
</script>
