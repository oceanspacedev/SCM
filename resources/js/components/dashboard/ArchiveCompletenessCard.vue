<template>
  <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-2xs">
    <!-- Header -->
    <div class="mb-3">
      <h3 class="text-sm font-bold text-slate-900 tracking-tight">
        Kelengkapan Arsip
      </h3>
      <p class="text-xs text-slate-400 mt-0.5">
        Status kelengkapan dokumen program
      </p>
    </div>

    <!-- Donut Chart & Counts Grid -->
    <div class="flex items-center justify-between gap-4 pt-1">
      <!-- SVG Donut Chart -->
      <div class="relative w-32 h-32 shrink-0 flex items-center justify-center">
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="#F1F5F9"
            stroke-width="12"
          />

          <!-- Belum Lengkap slice (Red) -->
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="#DC2626"
            stroke-width="12"
            stroke-dasharray="66.3 238.76"
            stroke-dashoffset="0"
            class="donut-slice cursor-pointer hover:stroke-[14] transition-all duration-200"
            style="--delay: 0.1s"
          />

          <!-- Sebagian slice (Orange) -->
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="#F59E0B"
            stroke-width="12"
            stroke-dasharray="92.8 238.76"
            stroke-dashoffset="-66.3"
            class="donut-slice cursor-pointer hover:stroke-[14] transition-all duration-200"
            style="--delay: 0.25s"
          />

          <!-- Lengkap slice (Deep Emerald) -->
          <circle
            cx="50"
            cy="50"
            r="38"
            fill="transparent"
            stroke="#2563EB"
            stroke-width="12"
            stroke-dasharray="79.6 238.76"
            stroke-dashoffset="-159.1"
            class="donut-slice cursor-pointer hover:stroke-[14] transition-all duration-200"
            style="--delay: 0.4s"
          />
        </svg>

        <!-- Center Text -->
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
          <span class="text-lg font-bold text-slate-900 leading-none">6 / 18</span>
          <span class="text-[10px] text-slate-400 font-medium mt-1">Siap Audit</span>
        </div>
      </div>

      <!-- Legend with Counts -->
      <div class="flex-1 space-y-3 pr-2">
        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB]"></span>
            <span class="text-slate-700 font-medium">Lengkap</span>
          </div>
          <span class="font-bold text-slate-900 font-mono">6</span>
        </div>

        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span>
            <span class="text-slate-700 font-medium">Sebagian</span>
          </div>
          <span class="font-bold text-slate-900 font-mono">7</span>
        </div>

        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626]"></span>
            <span class="text-slate-700 font-medium">Belum Lengkap</span>
          </div>
          <span class="font-bold text-slate-900 font-mono">5</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTaxStore } from '../../store/taxStore';

const store = useTaxStore();
const metrics = computed(() => store.summaryMetrics.value);
</script>

<style scoped>
.donut-slice {
  transform-origin: 50px 50px;
  animation: donutEntrance 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
  animation-delay: var(--delay, 0s);
}

@keyframes donutEntrance {
  from {
    stroke-dashoffset: 240;
    opacity: 0;
  }
}
</style>
