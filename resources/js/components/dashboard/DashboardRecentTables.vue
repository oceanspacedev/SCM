<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 select-none">
    <!-- Left Card: Arsip Program Terbaru -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-2xs flex flex-col">
      <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="text-xs font-bold text-slate-900 tracking-tight">
            Arsip Program Terbaru
          </h3>
          <p class="text-[11px] text-slate-500 mt-0.5">
            Program pengadaan dan invoice SCM terdaftar
          </p>
        </div>
        <router-link
          to="/programs"
          class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline"
        >
          Semua Program →
        </router-link>
      </div>

      <!-- Desktop Table -->
      <div class="hidden sm:block overflow-x-auto flex-1">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
              <th class="py-2.5 px-4 font-bold">Program</th>
              <th class="py-2.5 px-4 font-bold">Supplier</th>
              <th class="py-2.5 px-4 font-bold text-right">Nilai Tagihan</th>
              <th class="py-2.5 px-4 font-bold text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="prog in recentPrograms"
              :key="prog.id"
              class="hover:bg-slate-50/70 transition-colors"
            >
              <td class="py-2.5 px-4">
                <router-link
                  :to="`/programs/${prog.id}`"
                  class="font-semibold text-slate-800 hover:text-blue-600 transition-colors line-clamp-1 block"
                >
                  {{ prog.program_name }}
                </router-link>
                <span class="text-[10px] font-mono text-slate-400 block mt-0.5">{{ prog.invoice_number }}</span>
              </td>
              <td class="py-2.5 px-4 text-slate-600 truncate max-w-[130px]">
                {{ prog.supplier }}
              </td>
              <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900 whitespace-nowrap">
                {{ formatRupiah(prog.total_invoice) }}
              </td>
              <td class="py-2.5 px-4 text-center whitespace-nowrap">
                <Badge :variant="getBadge(prog).variant">
                  {{ getBadge(prog).label }}
                </Badge>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile List (block sm:hidden) -->
      <div class="block sm:hidden divide-y divide-slate-100 flex-1">
        <div
          v-for="prog in recentPrograms"
          :key="prog.id"
          class="p-3.5 space-y-2 hover:bg-slate-50/50 transition-colors"
        >
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
              <router-link
                :to="`/programs/${prog.id}`"
                class="font-semibold text-xs text-slate-800 hover:text-blue-600 leading-snug block break-words"
              >
                {{ prog.program_name }}
              </router-link>
              <span class="text-[11px] text-slate-500 block truncate mt-0.5">{{ prog.supplier }}</span>
            </div>
            <Badge :variant="getBadge(prog).variant" class="shrink-0">
              {{ getBadge(prog).label }}
            </Badge>
          </div>
          <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 text-xs">
            <span class="text-[10px] font-mono text-slate-400">
              {{ prog.invoice_number || '-' }}
            </span>
            <span class="font-mono font-bold text-slate-900 text-xs">
              {{ formatRupiah(prog.total_invoice) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Card: Dokumen Perlu Dilengkapi -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-2xs flex flex-col">
      <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="text-xs font-bold text-slate-900 tracking-tight">
            Peringatan Dokumen Belum Lengkap
          </h3>
          <p class="text-[11px] text-slate-500 mt-0.5">
            Dokumen wajib yang perlu diunggah segera
          </p>
        </div>
        <router-link
          to="/programs"
          class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline"
        >
          Semua Dokumen →
        </router-link>
      </div>

      <!-- Desktop Table -->
      <div class="hidden sm:block overflow-x-auto flex-1">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
              <th class="py-2.5 px-4 font-bold">Program</th>
              <th class="py-2.5 px-4 font-bold">Dokumen Kurang</th>
              <th class="py-2.5 px-4 font-bold text-center">Status</th>
              <th class="py-2.5 px-4 font-bold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="item in attentionList"
              :key="item.id"
              class="hover:bg-slate-50/70 transition-colors"
            >
              <td class="py-2.5 px-4">
                <router-link
                  :to="`/programs/${item.id}`"
                  class="font-semibold text-slate-800 hover:text-blue-600 transition-colors line-clamp-1 block"
                >
                  {{ item.name }}
                </router-link>
                <span class="text-[10px] text-slate-400 truncate block mt-0.5">{{ item.supplier }}</span>
              </td>
              <td class="py-2.5 px-4">
                <span class="inline-block px-1.5 py-0.5 text-[10px] font-medium rounded bg-rose-50 text-rose-600 border border-rose-200">
                  {{ item.missingDocs }}
                </span>
              </td>
              <td class="py-2.5 px-4 text-center whitespace-nowrap">
                <span class="text-[11px] font-mono text-slate-500">
                  {{ item.currentCount }}/3 Dokumen
                </span>
              </td>
              <td class="py-2.5 px-4 text-right whitespace-nowrap">
                <router-link
                  :to="`/programs/${item.id}`"
                  class="inline-flex items-center justify-center px-2 py-1 text-[11px] font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded transition-colors"
                >
                  Lihat
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile List (block sm:hidden) -->
      <div class="block sm:hidden divide-y divide-slate-100 flex-1">
        <div
          v-for="item in attentionList"
          :key="item.id"
          class="p-3.5 space-y-2 hover:bg-slate-50/50 transition-colors"
        >
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
              <router-link
                :to="`/programs/${item.id}`"
                class="font-semibold text-xs text-slate-800 hover:text-blue-600 leading-snug block break-words"
              >
                {{ item.name }}
              </router-link>
              <span class="text-[11px] text-slate-400 block truncate mt-0.5">{{ item.supplier }}</span>
            </div>
            <router-link
              :to="`/programs/${item.id}`"
              class="inline-flex items-center justify-center h-7 px-2.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition-colors shrink-0"
            >
              Lihat
            </router-link>
          </div>
          <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 text-xs">
            <span class="inline-block px-1.5 py-0.5 text-[10px] font-medium rounded bg-rose-50 text-rose-600 border border-rose-200 truncate">
              Kurang: {{ item.missingDocs }}
            </span>
            <span class="text-[10px] font-mono text-slate-400 shrink-0">
              {{ item.currentCount }}/3 Dokumen
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Badge from '../ui/Badge.vue';
import { useTaxStore, formatRupiah, getCompleteness } from '../../store/taxStore';

const store = useTaxStore();

const recentPrograms = computed(() => store.programs.value.slice(0, 5));
const attentionList = computed(() => store.needAttentionPrograms.value.slice(0, 5));

function getBadge(prog) {
  const c = getCompleteness(prog);
  if (c.count === 3) return { variant: 'success', label: 'Lengkap' };
  if (c.count > 0) return { variant: 'warning', label: 'Sebagian' };
  return { variant: 'danger', label: 'Belum Lengkap' };
}
</script>
