<template>
  <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
    <!-- Top Filter Controls with Labels -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5">
      <!-- 1. PENCARIAN -->
      <div class="md:col-span-6 lg:col-span-6">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 font-sans">
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
            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer"
            @click="store.state.searchQuery = ''"
            title="Hapus pencarian"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

      <!-- 2. STATUS KELENGKAPAN (Custom Dropdown matching reference) -->
      <div class="md:col-span-3 lg:col-span-3 relative" ref="statusDropdownRef">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 font-sans">
          STATUS KELENGKAPAN
        </label>
        <button
          type="button"
          class="w-full flex items-center justify-between px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer text-left focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46]"
          @click="isStatusOpen = !isStatusOpen; isSupplierOpen = false"
        >
          <span class="truncate">{{ currentStatusLabel }}</span>
          <ChevronDown class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-1.5 transition-transform" :class="{ 'rotate-180': isStatusOpen }" />
        </button>

        <!-- Dropdown Menu -->
        <div
          v-if="isStatusOpen"
          class="absolute left-0 mt-1.5 w-56 rounded-xl bg-white border border-slate-200 shadow-xl py-1 z-30 animate-in fade-in zoom-in-95 duration-100"
        >
          <button
            v-for="opt in statusOptions"
            :key="opt.value"
            type="button"
            class="w-full flex items-center justify-between px-3.5 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors text-left cursor-pointer"
            :class="{ 'font-semibold text-slate-900 bg-slate-50/70': store.state.selectedStatus === opt.value }"
            @click="selectStatus(opt.value)"
          >
            <span>{{ opt.label }}</span>
            <Check
              v-if="store.state.selectedStatus === opt.value"
              class="w-4 h-4 text-slate-800 shrink-0 ml-2"
            />
          </button>
        </div>
      </div>

      <!-- 3. SUPPLIER (Custom Dropdown) -->
      <div class="md:col-span-3 lg:col-span-3 relative" ref="supplierDropdownRef">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 font-sans">
          SUPPLIER
        </label>
        <button
          type="button"
          class="w-full flex items-center justify-between px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer text-left focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46]"
          @click="isSupplierOpen = !isSupplierOpen; isStatusOpen = false"
        >
          <span class="truncate">{{ currentSupplierLabel }}</span>
          <ChevronDown class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-1.5 transition-transform" :class="{ 'rotate-180': isSupplierOpen }" />
        </button>

        <!-- Dropdown Menu -->
        <div
          v-if="isSupplierOpen"
          class="absolute right-0 sm:left-0 mt-1.5 w-64 max-h-64 overflow-y-auto rounded-xl bg-white border border-slate-200 shadow-xl py-1 z-30 animate-in fade-in zoom-in-95 duration-100"
        >
          <button
            type="button"
            class="w-full flex items-center justify-between px-3.5 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors text-left cursor-pointer"
            :class="{ 'font-semibold text-slate-900 bg-slate-50/70': store.state.selectedSupplier === 'all' }"
            @click="selectSupplier('all')"
          >
            <span>Semua Supplier</span>
            <Check
              v-if="store.state.selectedSupplier === 'all'"
              class="w-4 h-4 text-slate-800 shrink-0 ml-2"
            />
          </button>
          <button
            v-for="sup in suppliersList"
            :key="sup"
            type="button"
            class="w-full flex items-center justify-between px-3.5 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors text-left cursor-pointer truncate"
            :class="{ 'font-semibold text-slate-900 bg-slate-50/70': store.state.selectedSupplier === sup }"
            @click="selectSupplier(sup)"
          >
            <span class="truncate">{{ sup }}</span>
            <Check
              v-if="store.state.selectedSupplier === sup"
              class="w-4 h-4 text-slate-800 shrink-0 ml-2"
            />
          </button>
        </div>
      </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-slate-100 my-3.5"></div>

    <!-- Bottom Summary Strip matching reference screenshot -->
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-slate-500">
      <div>
        Menampilkan <strong class="text-slate-900 font-bold">{{ filteredSummary.count }}</strong> program
      </div>
      <div>
        Total Invoice: <strong class="font-mono text-slate-900 font-bold ml-1">{{ formatRupiah(filteredSummary.totalInvoice) }}</strong>
      </div>
      <div>
        DPP: <strong class="font-mono text-slate-900 font-bold ml-1">{{ formatRupiah(filteredSummary.totalDpp) }}</strong>
      </div>
      <div>
        PPN: <strong class="font-mono text-slate-900 font-bold ml-1">{{ formatRupiah(filteredSummary.totalPpn) }}</strong>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Search, ChevronDown, X, Check } from 'lucide-vue-next';
import { useTaxStore, formatRupiah } from '../../store/taxStore';

const store = useTaxStore();

const isStatusOpen = ref(false);
const isSupplierOpen = ref(false);
const statusDropdownRef = ref(null);
const supplierDropdownRef = ref(null);

const suppliersList = computed(() => store.suppliersList.value);
const filteredPrograms = computed(() => store.filteredPrograms.value);

const statusOptions = [
  { value: 'all', label: 'Semua Status' },
  { value: 'Dokumen Lengkap', label: 'Dokumen Lengkap' },
  { value: 'Terverifikasi Pajak', label: 'Terverifikasi Pajak' },
  { value: 'Kurang Faktur Pajak', label: 'Kurang Faktur Pajak' },
  { value: 'Kurang Memo/MOU', label: 'Kurang Memo/MOU' },
  { value: 'Kurang Invoice', label: 'Kurang Invoice' },
  { value: 'Belum Ada Dokumen', label: 'Belum Ada Dokumen' },
];

const currentStatusLabel = computed(() => {
  const match = statusOptions.find(o => o.value === store.state.selectedStatus);
  return match ? match.label : 'Semua Status';
});

const currentSupplierLabel = computed(() => {
  if (!store.state.selectedSupplier || store.state.selectedSupplier === 'all') {
    return 'Semua Supplier';
  }
  return store.state.selectedSupplier;
});

function selectStatus(val) {
  store.state.selectedStatus = val;
  isStatusOpen.value = false;
}

function selectSupplier(val) {
  store.state.selectedSupplier = val;
  isSupplierOpen.value = false;
}

// Close dropdowns on outside click
function handleClickOutside(event) {
  if (statusDropdownRef.value && !statusDropdownRef.value.contains(event.target)) {
    isStatusOpen.value = false;
  }
  if (supplierDropdownRef.value && !supplierDropdownRef.value.contains(event.target)) {
    isSupplierOpen.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Summary numbers based on filtered programs
const filteredSummary = computed(() => {
  let totalInvoice = 0;
  let totalDpp = 0;
  let totalPpn = 0;

  filteredPrograms.value.forEach(p => {
    totalInvoice += Number(p.total_invoice) || 0;
    totalDpp += Number(p.dpp) || 0;
    totalPpn += Number(p.ppn) || 0;
  });

  return {
    count: filteredPrograms.value.length,
    totalInvoice,
    totalDpp,
    totalPpn,
  };
});
</script>
