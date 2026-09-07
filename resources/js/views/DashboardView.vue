<template>
  <div class="space-y-5 select-none">
    <!-- Header with Title & Fiscal Year Filter Dropdown -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 font-sans">
          Dashboard Program SCM
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">
          Ringkasan arsip dokumen & rekap perpajakan program
        </p>
      </div>

      <!-- Fiscal Year Dropdown matching screenshot -->
      <div class="relative">
        <button
          type="button"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition-colors cursor-pointer"
          @click="showYearMenu = !showYearMenu"
        >
          <Calendar class="w-3.5 h-3.5 text-slate-500" />
          <span>Tahun Pajak 2025</span>
          <ChevronDown class="w-3.5 h-3.5 text-slate-400 ml-0.5" />
        </button>

        <div
          v-if="showYearMenu"
          class="absolute right-0 mt-1.5 w-44 bg-white rounded-lg border border-slate-200 shadow-lg py-1.5 z-30 text-xs"
        >
          <button
            type="button"
            class="w-full text-left px-3.5 py-1.5 hover:bg-slate-50 font-semibold text-[#135A46] flex items-center justify-between"
            @click="showYearMenu = false"
          >
            <span>Tahun Pajak 2025</span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#135A46]"></span>
          </button>
          <button
            type="button"
            class="w-full text-left px-3.5 py-1.5 hover:bg-slate-50 text-slate-600"
            @click="showYearMenu = false"
          >
            <span>Tahun Pajak 2024</span>
          </button>
        </div>
      </div>
    </div>

    <!-- 4 Metric Cards Row matching screenshot -->
    <DashboardMetricCards />

    <!-- Two Charts Grid Row matching screenshot -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
      <!-- Left (Rekap DPP & PPN per Bulan) -->
      <div class="lg:col-span-7 xl:col-span-8 flex flex-col">
        <MonthlyTaxChart class="h-full" />
      </div>

      <!-- Right (Supplier Teratas) -->
      <div class="lg:col-span-5 xl:col-span-4 flex flex-col">
        <TopSuppliersChart class="h-full" />
      </div>
    </div>

    <!-- Bottom Section: Peringatan Dokumen Belum Lengkap -->
    <div>
      <IncompleteAlertSection />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Calendar, ChevronDown } from 'lucide-vue-next';
import DashboardMetricCards from '../components/dashboard/DashboardMetricCards.vue';
import MonthlyTaxChart from '../components/dashboard/MonthlyTaxChart.vue';
import TopSuppliersChart from '../components/dashboard/TopSuppliersChart.vue';
import IncompleteAlertSection from '../components/dashboard/IncompleteAlertSection.vue';

const showYearMenu = ref(false);
</script>
