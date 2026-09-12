<template>
  <div class="bg-white dark:bg-[#111827] rounded-none border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xs">
    <!-- Header with clean shadcn monochrome attention styling -->
    <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#111827]">
      <div class="flex items-start sm:items-center gap-3">
        <div class="w-9 h-9 rounded-none border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
          <AlertCircle class="w-4.5 h-4.5 stroke-[1.75]" />
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100 tracking-tight font-sans">
              Peringatan Dokumen Belum Lengkap
            </h3>
            <span
              v-if="alertList.length > 0"
              class="px-2 py-0.5 rounded-none text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-mono shrink-0"
            >
              {{ alertList.length }}
            </span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Beberapa program masih memerlukan kelengkapan dokumen perpajakan.
          </p>
        </div>
      </div>

      <router-link
        to="/programs"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-none border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors shadow-2xs whitespace-nowrap self-start sm:self-auto cursor-pointer group"
      >
        <span>Lihat semua arsip</span>
        <ArrowRight class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300 group-hover:translate-x-0.5 transition-transform" />
      </router-link>
    </div>

    <!-- Desktop Table View (hidden md:block) -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="bg-slate-50/70 dark:bg-slate-900/70 border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
            <th class="py-2.5 px-5 font-bold">PROGRAM</th>
            <th class="py-2.5 px-5 font-bold">DOKUMEN KURANG</th>
            <th class="py-2.5 px-5 font-bold">TANGGAL</th>
            <th class="py-2.5 px-5 font-bold text-right">AKSI</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
          <tr v-if="alertList.length === 0">
            <td colspan="4" class="py-8 text-center text-slate-400 dark:text-slate-500 text-xs">
              Semua program telah lengkap dokumennya. Tidak ada dokumen tertunda.
            </td>
          </tr>
          <tr
            v-for="item in alertList"
            :key="item.id"
            class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors"
          >
            <td class="py-3 px-5">
              <router-link
                :to="`/programs/${item.id}`"
                class="font-medium text-slate-800 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors line-clamp-1 block max-w-xs"
              >
                {{ item.name }}
              </router-link>
              <span v-if="item.supplier" class="text-[10px] text-slate-400 dark:text-slate-500 block truncate mt-0.5">
                {{ item.supplier }}
              </span>
            </td>

            <td class="py-3 px-5 whitespace-nowrap">
              <span class="inline-flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300">
                <span
                  class="w-2 h-2 rounded-full shrink-0"
                  :class="item.dotColor === 'red' ? 'bg-red-500' : 'bg-amber-500'"
                ></span>
                <span>{{ item.missingDoc }}</span>
              </span>
            </td>

            <td class="py-3 px-5 text-slate-400 dark:text-slate-500 text-[11px] whitespace-nowrap">
              {{ item.date }}
            </td>

            <td class="py-3 px-5 text-right whitespace-nowrap">
              <router-link
                :to="`/programs/${item.id}`"
                class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-none transition-colors"
              >
                Lihat
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Responsive Cards View (block md:hidden) - NO horizontal scroll required! -->
    <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
      <div v-if="alertList.length === 0" class="py-8 px-4 text-center text-slate-400 dark:text-slate-500 text-xs">
        Semua program telah lengkap dokumennya. Tidak ada dokumen tertunda.
      </div>

      <div
        v-for="item in alertList"
        :key="item.id"
        class="p-3.5 sm:p-4 space-y-2 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors"
      >
        <!-- Top: Program Name & Button Lihat -->
        <div class="flex items-start justify-between gap-2.5">
          <div class="min-w-0 flex-1">
            <router-link
              :to="`/programs/${item.id}`"
              class="font-semibold text-xs text-slate-900 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors leading-snug block break-words"
            >
              {{ item.name }}
            </router-link>
            <div v-if="item.supplier" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
              {{ item.supplier }}
            </div>
          </div>

          <router-link
            :to="`/programs/${item.id}`"
            class="inline-flex items-center justify-center h-7 px-3 text-xs font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-none transition-colors shrink-0"
          >
            Lihat
          </router-link>
        </div>

        <!-- Missing Documents Tag & Date -->
        <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 dark:border-slate-800 text-xs">
          <div class="flex items-center gap-1.5 min-w-0">
            <span
              class="w-2 h-2 rounded-full shrink-0"
              :class="item.dotColor === 'red' ? 'bg-rose-500' : 'bg-amber-500'"
            ></span>
            <span
              class="text-[11px] font-medium truncate"
              :class="item.dotColor === 'red' ? 'text-rose-700' : 'text-amber-800'"
            >
              Kurang: {{ item.missingDoc }}
            </span>
          </div>

          <span class="text-[10px] text-slate-400 font-mono shrink-0 whitespace-nowrap">
            {{ item.date }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { AlertCircle, ArrowRight } from 'lucide-vue-next';
import { useTaxStore, formatDate, getMissingDocuments, getCompleteness } from '../../store/taxStore';

const store = useTaxStore();

const alertList = computed(() => {
  const all = store.dashboardPrograms.value || [];
  return all
    .filter(p => {
      const docs = p.documents || [];
      return docs.length < 3;
    })
    .sort((a, b) => {
      // Show programs with 0 docs or 1-2 docs
      const countA = a.documents?.length || 0;
      const countB = b.documents?.length || 0;
      if (countA !== countB) return countA - countB;
      const dateA = new Date(a.program_date || 0);
      const dateB = new Date(b.program_date || 0);
      return dateB - dateA;
    })
    .slice(0, 10)
    .map(p => {
      const missing = getMissingDocuments(p);
      const completeness = getCompleteness(p);
      const dotColor = completeness.count === 0 ? 'red' : 'orange';

      return {
        id: p.id,
        name: p.program_name,
        supplier: p.supplier,
        missingDoc: missing.join(', ') || 'Belum Ada Dokumen',
        date: formatDate(p.program_date),
        dotColor: dotColor
      };
    });
});
</script>
