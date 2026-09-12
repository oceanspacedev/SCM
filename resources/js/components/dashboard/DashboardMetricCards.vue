<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total Nilai Invoice -->
    <div class="bg-white dark:bg-[#111827] rounded-none border border-slate-200 dark:border-slate-800 p-4.5 flex flex-col justify-between shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
      <div>
        <span class="text-[11px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase font-sans">
          TOTAL NILAI INVOICE
        </span>
      </div>

      <div class="my-2.5">
        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 font-sans truncate block">
          {{ formatRupiah(metrics.totalInvoice) }}
        </span>
      </div>

      <div>
        <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">
          {{ metrics.totalPrograms }} program terarsip
        </span>
      </div>
    </div>

    <!-- Card 2: Total DPP -->
    <div class="bg-white dark:bg-[#111827] rounded-none border border-slate-200 dark:border-slate-800 p-4.5 flex flex-col justify-between shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
      <div>
        <span class="text-[11px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase font-sans">
          TOTAL DPP
        </span>
      </div>

      <div class="my-2.5">
        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 font-sans truncate block">
          {{ formatRupiah(metrics.totalDpp) }}
        </span>
      </div>

      <div>
        <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">
          Dasar Pengenaan Pajak
        </span>
      </div>
    </div>

    <!-- Card 3: Total PPN -->
    <div class="bg-white dark:bg-[#111827] rounded-none border border-slate-200 dark:border-slate-800 p-4.5 flex flex-col justify-between shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
      <div>
        <span class="text-[11px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase font-sans">
          TOTAL PPN (11%)
        </span>
      </div>

      <div class="my-2.5">
        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 font-sans truncate block">
          {{ formatRupiah(metrics.totalPpn) }}
        </span>
      </div>

      <div>
        <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">
          Pajak Pertambahan Nilai
        </span>
      </div>
    </div>

    <!-- Card 4: Siap Audit -->
    <div class="bg-white dark:bg-[#111827] rounded-none border border-slate-200 dark:border-slate-800 p-4.5 flex flex-col justify-between shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
      <div>
        <span class="text-[11px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase font-sans">
          SIAP AUDIT
        </span>
      </div>

      <div class="my-2.5">
        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 font-sans">
          {{ metrics.lengkapCount }} / {{ metrics.totalPrograms }}
        </span>
      </div>

      <div>
        <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">
          {{ metrics.lengkapCount }} program berkas lengkap
        </span>
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
