<template>
  <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
    <!-- Header with amber alert icon -->
    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="flex items-start sm:items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 mt-0.5 sm:mt-0">
          <AlertTriangle class="w-4 h-4" />
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-slate-900 tracking-tight">
              Peringatan Dokumen Belum Lengkap
            </h3>
            <span
              v-if="alertList.length > 0"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 shrink-0"
            >
              {{ alertList.length }}
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-0.5">
            Beberapa program masih memerlukan dokumen perpajakan.
          </p>
        </div>
      </div>

      <router-link
        to="/programs"
        class="text-xs font-semibold text-blue-600 hover:underline whitespace-nowrap flex items-center gap-1 self-start sm:self-auto"
      >
        <span>Lihat semua arsip</span>
        <span>→</span>
      </router-link>
    </div>

    <!-- Desktop Table View (hidden md:block) -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            <th class="py-2.5 px-5 font-bold">PROGRAM</th>
            <th class="py-2.5 px-5 font-bold">DOKUMEN KURANG</th>
            <th class="py-2.5 px-5 font-bold">TANGGAL</th>
            <th class="py-2.5 px-5 font-bold text-right">AKSI</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="alertList.length === 0">
            <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
              Semua program telah lengkap dokumennya. Tidak ada dokumen tertunda.
            </td>
          </tr>
          <tr
            v-for="item in alertList"
            :key="item.id"
            class="hover:bg-slate-50/60 transition-colors"
          >
            <td class="py-3 px-5">
              <router-link
                :to="`/programs/${item.id}`"
                class="font-medium text-slate-800 hover:text-blue-600 transition-colors line-clamp-1 block max-w-xs"
              >
                {{ item.name }}
              </router-link>
              <span v-if="item.supplier" class="text-[10px] text-slate-400 block truncate mt-0.5">
                {{ item.supplier }}
              </span>
            </td>

            <td class="py-3 px-5 whitespace-nowrap">
              <span class="inline-flex items-center gap-1.5 text-xs text-slate-700">
                <span
                  class="w-2 h-2 rounded-full shrink-0"
                  :class="item.dotColor === 'red' ? 'bg-red-500' : 'bg-amber-500'"
                ></span>
                <span>{{ item.missingDoc }}</span>
              </span>
            </td>

            <td class="py-3 px-5 text-slate-400 text-[11px] whitespace-nowrap">
              {{ item.date }}
            </td>

            <td class="py-3 px-5 text-right whitespace-nowrap">
              <router-link
                :to="`/programs/${item.id}`"
                class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition-colors"
              >
                Lihat
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Responsive Cards View (block md:hidden) - NO horizontal scroll required! -->
    <div class="block md:hidden divide-y divide-slate-100">
      <div v-if="alertList.length === 0" class="py-8 px-4 text-center text-slate-400 text-xs">
        Semua program telah lengkap dokumennya. Tidak ada dokumen tertunda.
      </div>

      <div
        v-for="item in alertList"
        :key="item.id"
        class="p-3.5 sm:p-4 space-y-2 hover:bg-slate-50/50 transition-colors"
      >
        <!-- Top: Program Name & Button Lihat -->
        <div class="flex items-start justify-between gap-2.5">
          <div class="min-w-0 flex-1">
            <router-link
              :to="`/programs/${item.id}`"
              class="font-semibold text-xs text-slate-900 hover:text-blue-600 transition-colors leading-snug block break-words"
            >
              {{ item.name }}
            </router-link>
            <div v-if="item.supplier" class="text-[11px] text-slate-500 mt-0.5 truncate">
              {{ item.supplier }}
            </div>
          </div>

          <router-link
            :to="`/programs/${item.id}`"
            class="inline-flex items-center justify-center h-7 px-3 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition-colors shrink-0"
          >
            Lihat
          </router-link>
        </div>

        <!-- Missing Documents Tag & Date -->
        <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 text-xs">
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
import { AlertTriangle } from 'lucide-vue-next';
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
