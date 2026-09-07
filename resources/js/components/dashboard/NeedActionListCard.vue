<template>
  <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
    <!-- Header -->
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900 tracking-tight">
        Program yang Perlu Dilengkapi
      </h3>
      <router-link
        to="/programs"
        class="text-xs font-semibold text-[#135A46] hover:underline"
      >
        Lihat semua →
      </router-link>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            <th class="py-2.5 px-4 font-bold">PROGRAM</th>
            <th class="py-2.5 px-4 font-bold">DOKUMEN KURANG</th>
            <th class="py-2.5 px-4 font-bold text-right">AKSI</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr
            v-for="item in programsList"
            :key="item.id"
            class="hover:bg-slate-50/60 transition-colors"
          >
            <td class="py-2.5 px-4">
              <router-link
                :to="`/programs/${item.id}`"
                class="font-medium text-slate-800 hover:text-[#135A46] transition-colors truncate block max-w-[190px]"
                :title="item.name"
              >
                {{ item.name }}
              </router-link>
            </td>

            <td class="py-2.5 px-4 whitespace-nowrap">
              <span class="inline-flex items-center gap-1.5 text-xs text-slate-700">
                <span
                  class="w-2 h-2 rounded-full shrink-0"
                  :class="item.dotColor === 'red' ? 'bg-red-500' : 'bg-amber-500'"
                ></span>
                <span>{{ item.missingDoc }}</span>
              </span>
            </td>

            <td class="py-2.5 px-4 text-right whitespace-nowrap">
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

// 5 rows exactly matching the screenshot
const programsList = ref([
  { id: 7, name: 'Program Restock Otomatis Minimarket', missingDoc: 'Faktur Pajak', dotColor: 'red' },
  { id: 8, name: 'Program Branding In-Store', missingDoc: 'MOU', dotColor: 'orange' },
  { id: 9, name: 'Program Kemitraan Warung Digital', missingDoc: 'Invoice', dotColor: 'red' },
  { id: 10, name: 'Program Diskon Grosir Kuartal II', missingDoc: 'Faktur Pajak', dotColor: 'orange' },
  { id: 11, name: 'Program Aktivasi Event Nasional', missingDoc: 'MOU', dotColor: 'red' },
]);
</script>
