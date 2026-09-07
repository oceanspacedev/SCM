<template>
  <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs space-y-3.5">
    <!-- Top Filter Controls matching screenshot -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5">
      <!-- Search Input (PENCARIAN) -->
      <div class="md:col-span-6 lg:col-span-6">
        <label class="block text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1.5 font-sans">
          PENCARIAN
        </label>
        <div class="relative">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
          <input
            v-model="store.state.searchQuery"
            type="text"
            placeholder="Cari program, supplier, no. invoice, faktur, MOU..."
            class="w-full pl-9 pr-8 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46] transition-colors"
          />
          <button
            v-if="store.state.searchQuery"
            type="button"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5"
            @click="store.state.searchQuery = ''"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

      <!-- Status Filter (STATUS KELENGKAPAN) -->
      <div class="md:col-span-3 lg:col-span-3">
        <label class="block text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1.5 font-sans">
          STATUS KELENGKAPAN
        </label>
        <div class="relative">
          <select
            v-model="store.state.selectedStatus"
            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-700 focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46] transition-colors appearance-none pr-8 cursor-pointer"
          >
            <option value="all">Semua Status</option>
            <option value="lengkap">Lengkap (3/3)</option>
            <option value="sebagian">Sebagian (1-2/3)</option>
            <option value="belum">Belum Lengkap (0/3)</option>
          </select>
          <ChevronDown class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
        </div>
      </div>

      <!-- Supplier Filter (SUPPLIER) -->
      <div class="md:col-span-3 lg:col-span-3">
        <label class="block text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1.5 font-sans">
          SUPPLIER
        </label>
        <div class="relative">
          <select
            v-model="store.state.selectedSupplier"
            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-700 focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46] transition-colors appearance-none pr-8 cursor-pointer truncate"
          >
            <option value="all">Semua Supplier</option>
            <option
              v-for="sup in suppliersList"
              :key="sup"
              :value="sup"
            >
              {{ sup }}
            </option>
          </select>
          <ChevronDown class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
        </div>
      </div>
    </div>

    <!-- Bottom Summary Row matching screenshot -->
    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-600">
      <div>
        Menampilkan
        <strong class="text-slate-900 font-bold">{{ filteredPrograms.length }} program</strong>
      </div>

      <div class="flex flex-wrap items-center gap-4 sm:gap-6">
        <span>
          Total Invoice:
          <strong class="font-mono text-slate-900 font-bold ml-1">
            {{ formatRupiah(totals.totalInvoice) }}
          </strong>
        </span>
        <span>
          DPP:
          <strong class="font-mono text-slate-900 font-bold ml-1">
            {{ formatRupiah(totals.totalDpp) }}
          </strong>
        </span>
        <span>
          PPN:
          <strong class="font-mono text-slate-900 font-bold ml-1">
            {{ formatRupiah(totals.totalPpn) }}
          </strong>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Search, ChevronDown, X } from 'lucide-vue-next';
import { useTaxStore, formatRupiah } from '../../store/taxStore';

const store = useTaxStore();

const suppliersList = computed(() => store.suppliersList.value);
const filteredPrograms = computed(() => store.filteredPrograms.value);

const totals = computed(() => {
  const progs = filteredPrograms.value;
  const totalInvoice = progs.reduce((sum, p) => sum + (p.total_invoice || 0), 0);
  const totalDpp = progs.reduce((sum, p) => sum + (p.dpp || 0), 0);
  const totalPpn = progs.reduce((sum, p) => sum + (p.ppn || 0), 0);
  return { totalInvoice, totalDpp, totalPpn };
});
</script>
