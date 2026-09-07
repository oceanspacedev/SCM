<template>
  <div class="bg-white rounded-xl border border-amber-200 shadow-xs overflow-hidden">
    <!-- Amber Alert Banner Header -->
    <div class="bg-amber-50/70 border-b border-amber-200/80 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
          <AlertTriangle class="w-4 h-4" />
        </div>
        <div>
          <h3 class="text-sm font-bold text-slate-900">
            Peringatan Dokumen Belum Lengkap
          </h3>
          <p class="text-xs text-slate-600 mt-0.5">
            Daftar program prioritas yang memerlukan tindak lanjut sebelum jadwal audit
          </p>
        </div>
      </div>

      <router-link
        to="/programs"
        class="text-xs font-semibold text-[#0284C7] hover:underline flex items-center gap-1"
      >
        <span>Lihat semua arsip</span>
        <ChevronRight class="w-3.5 h-3.5" />
      </router-link>
    </div>

    <!-- Rows List -->
    <div class="divide-y divide-slate-100">
      <div
        v-for="item in attentionList"
        :key="item.id"
        class="p-4 sm:px-6 hover:bg-slate-50/70 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3"
      >
        <div class="space-y-0.5">
          <div class="flex items-center gap-2">
            <router-link
              :to="`/programs/${item.id}`"
              class="font-semibold text-slate-900 hover:text-[#0284C7] transition-colors text-sm"
            >
              {{ item.name }}
            </router-link>
            <span class="text-[11px] font-mono text-slate-400">
              #{{ item.invoiceNumber }}
            </span>
          </div>
          <p class="text-xs text-slate-500">
            Supplier: <strong class="text-slate-700 font-medium">{{ item.supplier }}</strong>
          </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-center">
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
            <AlertTriangle class="w-3 h-3 text-amber-600" />
            <span>Kurang: {{ item.missingDocs }}</span>
          </span>

          <router-link
            :to="`/programs/${item.id}`"
            class="inline-flex items-center justify-center h-8 px-3 text-xs font-semibold rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 transition-colors shadow-2xs"
          >
            Lihat
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { AlertTriangle, ChevronRight } from 'lucide-vue-next';
import { useTaxStore } from '../../store/taxStore';

const store = useTaxStore();
const attentionList = computed(() => store.needAttentionPrograms.value);
</script>
