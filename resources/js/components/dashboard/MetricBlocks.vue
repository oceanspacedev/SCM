<template>
  <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-[#DDE4E1]">
      <!-- Metric 1: Total Program -->
      <div class="p-5 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-[#66736F] uppercase tracking-wider">Total Program</span>
            <span class="inline-flex items-center text-[11px] font-medium text-[#15803D] bg-[#F0FDF4] px-1.5 py-0.5 rounded border border-[#BBF7D0]">
              +2 bulan ini
            </span>
          </div>
          <div class="mt-2 text-2xl font-bold tracking-tight text-[#17201E]">
            {{ metrics.totalPrograms }}
          </div>
        </div>
        <p class="mt-2 text-xs text-[#66736F]">
          Semua program SCM aktif
        </p>
      </div>

      <!-- Metric 2: Total Invoice -->
      <div class="p-5 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-[#66736F] uppercase tracking-wider">Total Invoice</span>
            <span class="text-[11px] text-[#66736F] font-mono">Gross</span>
          </div>
          <div class="mt-2 text-xl font-bold tracking-tight text-[#17201E] truncate" :title="formatRupiah(metrics.totalInvoice)">
            {{ formatRupiah(metrics.totalInvoice) }}
          </div>
        </div>
        <p class="mt-2 text-xs text-[#66736F]">
          Akumulasi nilai seluruh tagihan
        </p>
      </div>

      <!-- Metric 3: Total DPP -->
      <div class="p-5 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-[#66736F] uppercase tracking-wider">Total DPP</span>
            <span class="text-[11px] text-[#66736F] font-mono">89.8%</span>
          </div>
          <div class="mt-2 text-xl font-bold tracking-tight text-[#17201E] truncate" :title="formatRupiah(metrics.totalDpp)">
            {{ formatRupiah(metrics.totalDpp) }}
          </div>
        </div>
        <p class="mt-2 text-xs text-[#66736F]">
          Dasar Pengenaan Pajak
        </p>
      </div>

      <!-- Metric 4: Total PPN -->
      <div class="p-5 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-[#66736F] uppercase tracking-wider">Total PPN</span>
            <span class="inline-flex items-center text-[11px] font-medium text-[#0F766E] bg-[#F0FDFA] px-1.5 py-0.5 rounded border border-[#CCFBF1]">
              Porsi 11%
            </span>
          </div>
          <div class="mt-2 text-xl font-bold tracking-tight text-[#0F766E] truncate" :title="formatRupiah(metrics.totalPpn)">
            {{ formatRupiah(metrics.totalPpn) }}
          </div>
        </div>
        <p class="mt-2 text-xs text-[#66736F]">
          Pajak Masukan terlapor
        </p>
      </div>

      <!-- Metric 5: Siap Audit -->
      <div class="p-5 flex flex-col justify-between bg-[#FAFBFA]">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-[#66736F] uppercase tracking-wider">Siap Audit</span>
            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[#15803D]">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
              <span>33.3%</span>
            </span>
          </div>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl font-bold tracking-tight text-[#15803D]">{{ metrics.siapAudit }}</span>
            <span class="text-sm font-semibold text-[#66736F]">/ {{ metrics.totalPrograms }}</span>
          </div>
        </div>
        <div class="mt-2 flex items-center gap-1.5 text-xs text-[#66736F]">
          <span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
          <span>Dokumen 3/3 lengkap</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTaxStore, formatRupiah } from '../../store/taxStore';

const store = useTaxStore();
const metrics = computed(() => store.summaryMetrics.value);
</script>
