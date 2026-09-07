<template>
  <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs p-5 flex flex-col h-full">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-[#EBEFEF]">
      <div>
        <h3 class="text-sm font-semibold text-[#17201E]">
          Kelengkapan Arsip
        </h3>
        <p class="text-xs text-[#66736F] mt-0.5">
          Status kelengkapan 3 dokumen wajib per program
        </p>
      </div>

      <router-link
        to="/programs"
        class="text-xs font-medium text-[#0F766E] hover:underline"
      >
        Lihat Semua
      </router-link>
    </div>

    <!-- Donut & Breakdown Grid -->
    <div class="pt-5 flex-1 flex flex-col sm:flex-row items-center justify-center gap-6">
      <!-- SVG Donut -->
      <div class="relative w-36 h-36 shrink-0 flex items-center justify-center">
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
          <!-- Background track -->
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="#F4F6F5"
            stroke-width="12"
          />

          <!-- Belum Lengkap slice -->
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="#B91C1C"
            stroke-width="12"
            :stroke-dasharray="`${belumStroke} 238.76`"
            :stroke-dashoffset="0"
            stroke-linecap="round"
            class="transition-all duration-700"
          />

          <!-- Sebagian slice -->
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="#B45309"
            stroke-width="12"
            :stroke-dasharray="`${sebagianStroke} 238.76`"
            :stroke-dashoffset="`-${belumStroke}`"
            stroke-linecap="round"
            class="transition-all duration-700"
          />

          <!-- Lengkap slice -->
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="#15803D"
            stroke-width="12"
            :stroke-dasharray="`${lengkapStroke} 238.76`"
            :stroke-dashoffset="`-${belumStroke + sebagianStroke}`"
            stroke-linecap="round"
            class="transition-all duration-700"
          />
        </svg>

        <!-- Center Total Count -->
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
          <span class="text-2xl font-bold tracking-tight text-[#17201E]">{{ total }}</span>
          <span class="text-[10px] text-[#66736F] uppercase tracking-wider font-medium">Program</span>
        </div>
      </div>

      <!-- Legend List -->
      <div class="flex-1 w-full space-y-2.5">
        <!-- Lengkap -->
        <div
          class="flex items-center justify-between p-2 rounded-md hover:bg-[#FAFBFA] transition-colors border border-transparent hover:border-[#DDE4E1] cursor-pointer"
          @click="filterByStatus('lengkap')"
        >
          <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-[#15803D]"></span>
            <div>
              <p class="text-xs font-semibold text-[#17201E]">Lengkap</p>
              <p class="text-[11px] text-[#66736F]">3/3 dokumen terverifikasi</p>
            </div>
          </div>
          <div class="text-right">
            <span class="text-sm font-bold text-[#15803D]">{{ lengkap }}</span>
            <span class="text-[11px] text-[#66736F] block">{{ Math.round((lengkap / total) * 100) }}%</span>
          </div>
        </div>

        <!-- Sebagian -->
        <div
          class="flex items-center justify-between p-2 rounded-md hover:bg-[#FAFBFA] transition-colors border border-transparent hover:border-[#DDE4E1] cursor-pointer"
          @click="filterByStatus('sebagian')"
        >
          <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-[#B45309]"></span>
            <div>
              <p class="text-xs font-semibold text-[#17201E]">Sebagian</p>
              <p class="text-[11px] text-[#66736F]">1-2 dokumen tersedia</p>
            </div>
          </div>
          <div class="text-right">
            <span class="text-sm font-bold text-[#B45309]">{{ sebagian }}</span>
            <span class="text-[11px] text-[#66736F] block">{{ Math.round((sebagian / total) * 100) }}%</span>
          </div>
        </div>

        <!-- Belum Lengkap -->
        <div
          class="flex items-center justify-between p-2 rounded-md hover:bg-[#FAFBFA] transition-colors border border-transparent hover:border-[#DDE4E1] cursor-pointer"
          @click="filterByStatus('belum')"
        >
          <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-[#B91C1C]"></span>
            <div>
              <p class="text-xs font-semibold text-[#17201E]">Belum Lengkap</p>
              <p class="text-[11px] text-[#66736F]">0 dokumen terunggah</p>
            </div>
          </div>
          <div class="text-right">
            <span class="text-sm font-bold text-[#B91C1C]">{{ belum }}</span>
            <span class="text-[11px] text-[#66736F] block">{{ Math.round((belum / total) * 100) }}%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useTaxStore } from '../../store/taxStore';

const router = useRouter();
const store = useTaxStore();

const metrics = computed(() => store.summaryMetrics.value);
const total = computed(() => metrics.value.totalPrograms || 18);
const lengkap = computed(() => metrics.value.lengkapCount || 6);
const sebagian = computed(() => metrics.value.sebagianCount || 7);
const belum = computed(() => metrics.value.belumLengkapCount || 5);

// Circumference = 2 * PI * 38 ≈ 238.76
const circumference = 238.76;
const lengkapStroke = computed(() => (lengkap.value / total.value) * circumference);
const sebagianStroke = computed(() => (sebagian.value / total.value) * circumference);
const belumStroke = computed(() => (belum.value / total.value) * circumference);

function filterByStatus(status) {
  store.state.selectedStatus = status;
  router.push('/programs');
}
</script>
