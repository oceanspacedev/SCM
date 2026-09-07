<template>
  <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
    <!-- Header with amber alert icon -->
    <div class="p-5 border-b border-slate-100 flex items-start justify-between gap-4">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5 border border-amber-100">
          <AlertTriangle class="w-4 h-4" />
        </div>
        <div>
          <h3 class="text-sm font-bold text-slate-900 tracking-tight">
            Peringatan Dokumen Belum Lengkap
          </h3>
          <p class="text-xs text-slate-400 mt-0.5">
            Beberapa program masih memerlukan dokumen perpajakan.
          </p>
        </div>
      </div>

      <router-link
        to="/programs"
        class="text-xs font-semibold text-[#135A46] hover:underline whitespace-nowrap mt-1"
      >
        Lihat semua arsip →
      </router-link>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
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
          <tr
            v-for="item in alertList"
            :key="item.id"
            class="hover:bg-slate-50/60 transition-colors"
          >
            <td class="py-3 px-5">
              <router-link
                :to="`/programs/${item.id}`"
                class="font-medium text-slate-800 hover:text-[#135A46] transition-colors line-clamp-1 block max-w-xs"
              >
                {{ item.name }}
              </router-link>
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
                class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"
              >
                Lihat
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { AlertTriangle } from 'lucide-vue-next';

// 5 rows matching the screenshot
const alertList = ref([
  { id: 7, name: 'Program Restock Otomatis Minimarket', missingDoc: 'Faktur Pajak', date: '17 Jul 2025', dotColor: 'red' },
  { id: 8, name: 'Program Branding In-Store', missingDoc: 'MOU', date: '12 Feb 2025', dotColor: 'orange' },
  { id: 9, name: 'Program Kemitraan Warung Digital', missingDoc: 'Invoice', date: '18 Agu 2025', dotColor: 'red' },
  { id: 10, name: 'Program Loyalty Member Retail', missingDoc: 'Faktur Pajak', date: '03 Jun 2025', dotColor: 'orange' },
  { id: 11, name: 'Program Event Nasional', missingDoc: 'MOU', date: '15 Mei 2025', dotColor: 'red' },
]);
</script>
