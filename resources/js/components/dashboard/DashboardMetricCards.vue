<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total Nilai Invoice -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 flex flex-col justify-between shadow-2xs hover:border-slate-300 transition-all">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase font-sans">
          TOTAL NILAI INVOICE
        </span>
        <Wallet class="w-4 h-4 text-blue-600 stroke-[1.75]" />
      </div>

      <div class="my-2.5">
        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 font-sans truncate block">
          {{ formatRupiah(metrics.totalInvoice) }}
        </span>
      </div>

      <div>
        <span class="text-xs text-slate-400 font-normal">
          {{ metrics.totalPrograms }} program terarsip
        </span>
      </div>
    </div>

    <!-- Card 2: Total DPP -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 flex flex-col justify-between shadow-2xs hover:border-slate-300 transition-all">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase font-sans">
          TOTAL DPP
        </span>
        <FileSpreadsheet class="w-4 h-4 text-blue-600 stroke-[1.75]" />
      </div>

      <div class="my-2.5">
        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 font-sans truncate block">
          {{ formatRupiah(metrics.totalDpp) }}
        </span>
      </div>

      <div>
        <span class="text-xs text-slate-400 font-normal">
          Dasar Pengenaan Pajak
        </span>
      </div>
    </div>

    <!-- Card 3: Total PPN -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 flex flex-col justify-between shadow-2xs hover:border-slate-300 transition-all">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase font-sans">
          TOTAL PPN (11%)
        </span>
        <Percent class="w-4 h-4 text-blue-600 stroke-[1.75]" />
      </div>

      <div class="my-2.5">
        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 font-sans truncate block">
          {{ formatRupiah(metrics.totalPpn) }}
        </span>
      </div>

      <div>
        <span class="text-xs text-slate-400 font-normal">
          Pajak Pertambahan Nilai
        </span>
      </div>
    </div>

    <!-- Card 4: Siap Audit -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 flex flex-col justify-between shadow-2xs hover:border-slate-300 transition-all">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase font-sans">
          SIAP AUDIT
        </span>
        <CheckCircle2 class="w-4 h-4 text-blue-600 stroke-[1.75]" />
      </div>

      <div class="my-2.5">
        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 font-sans">
          {{ metrics.lengkapCount }} / {{ metrics.totalPrograms }}
        </span>
      </div>

      <div>
        <span class="text-xs text-slate-400 font-normal">
          {{ metrics.lengkapCount }} program berkas lengkap
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Wallet, FileSpreadsheet, Percent, CheckCircle2 } from 'lucide-vue-next';
import { useTaxStore, formatRupiah } from '../../store/taxStore';

const store = useTaxStore();
const metrics = computed(() => store.summaryMetrics.value);
</script>
