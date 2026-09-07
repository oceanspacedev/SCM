<template>
  <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs p-5 flex flex-col h-full">
    <!-- Section Header -->
    <div class="flex items-center justify-between pb-4 border-b border-[#EBEFEF]">
      <div>
        <h3 class="text-sm font-semibold text-[#17201E]">
          Nilai Perpajakan
        </h3>
        <p class="text-xs text-[#66736F] mt-0.5">
          Perbandingan proporsi DPP dan PPN per kategori program SCM
        </p>
      </div>

      <!-- Legend -->
      <div class="flex items-center gap-4 text-xs">
        <div class="flex items-center gap-1.5">
          <span class="w-3 h-3 rounded-xs bg-[#0F766E]"></span>
          <span class="font-medium text-[#17201E]">DPP</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span class="w-3 h-3 rounded-xs bg-[#DDE4E1]"></span>
          <span class="font-medium text-[#66736F]">PPN (11%)</span>
        </div>
      </div>
    </div>

    <!-- Chart Visualization -->
    <div class="pt-5 flex-1 flex flex-col justify-around space-y-4">
      <div
        v-for="item in categoryData"
        :key="item.category"
        class="group cursor-default"
      >
        <div class="flex items-baseline justify-between text-xs mb-1.5">
          <span class="font-medium text-[#17201E]">{{ item.category }}</span>
          <div class="flex items-center gap-2 font-mono text-[11px]">
            <span class="text-[#0F766E] font-medium">{{ formatRupiah(item.dpp) }}</span>
            <span class="text-[#94A3B8]">/</span>
            <span class="text-[#66736F]">{{ formatRupiah(item.ppn) }}</span>
          </div>
        </div>

        <!-- Stacked Bar -->
        <div class="w-full h-3 bg-[#F4F6F5] rounded-xs overflow-hidden flex">
          <div
            class="bg-[#0F766E] h-full transition-all duration-500 hover:bg-[#115E59]"
            :style="{ width: item.dppPercent + '%' }"
            :title="`DPP: ${formatRupiah(item.dpp)}`"
          ></div>
          <div
            class="bg-[#CBD5E1] h-full transition-all duration-500 hover:bg-[#94A3B8]"
            :style="{ width: item.ppnPercent + '%' }"
            :title="`PPN: ${formatRupiah(item.ppn)}`"
          ></div>
        </div>
      </div>
    </div>

    <!-- Bottom summary note -->
    <div class="mt-4 pt-3 border-t border-[#EBEFEF] flex items-center justify-between text-xs text-[#66736F]">
      <span>Total Akumulasi: <strong class="text-[#17201E] font-mono">{{ formatRupiah(totalValue) }}</strong></span>
      <span class="text-[11px]">Rasio PPN Efektif: 11.33%</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTaxStore, formatRupiah } from '../../store/taxStore';

const store = useTaxStore();

const categoryData = computed(() => {
  const map = {};
  store.programs.value.forEach(p => {
    const cat = p.category || 'Lainnya';
    if (!map[cat]) {
      map[cat] = { category: cat, dpp: 0, ppn: 0, total: 0 };
    }
    map[cat].dpp += Number(p.dpp) || 0;
    map[cat].ppn += Number(p.ppn) || 0;
    map[cat].total += Number(p.total_invoice) || 0;
  });

  const grandTotal = Math.max(1, ...Object.values(map).map(m => m.total));

  return Object.values(map).map(m => {
    const dppPercent = (m.dpp / grandTotal) * 88; // scaled for bar
    const ppnPercent = (m.ppn / grandTotal) * 88;
    return {
      ...m,
      dppPercent: Math.max(2, dppPercent),
      ppnPercent: Math.max(1, ppnPercent)
    };
  }).sort((a, b) => b.total - a.total);
});

const totalValue = computed(() => store.summaryMetrics.value.totalInvoice);
</script>
