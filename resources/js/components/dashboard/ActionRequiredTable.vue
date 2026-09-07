<template>
  <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs overflow-hidden">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-[#DDE4E1] flex items-center justify-between">
      <div>
        <h3 class="text-sm font-semibold text-[#17201E]">
          Program yang Perlu Dilengkapi
        </h3>
        <p class="text-xs text-[#66736F] mt-0.5">
          Daftar 5 program prioritas dengan dokumen perpajakan yang belum lengkap
        </p>
      </div>

      <router-link
        to="/programs"
        class="text-xs font-semibold text-[#0F766E] hover:underline flex items-center gap-1"
      >
        <span>Lihat Semua Program</span>
        <ChevronRight class="w-3.5 h-3.5" />
      </router-link>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead>
          <tr class="bg-[#FAFBFA] border-b border-[#DDE4E1] text-[11px] font-semibold uppercase tracking-wider text-[#66736F]">
            <th class="py-2.5 px-5">Program</th>
            <th class="py-2.5 px-5">Supplier</th>
            <th class="py-2.5 px-5">Dokumen Kurang</th>
            <th class="py-2.5 px-5 text-right">Nilai Tagihan</th>
            <th class="py-2.5 px-5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#EBEFEF]">
          <tr
            v-for="item in attentionList"
            :key="item.id"
            class="hover:bg-[#F7F8F7] transition-colors group"
          >
            <!-- Program Name -->
            <td class="py-3 px-5">
              <router-link
                :to="`/programs/${item.id}`"
                class="font-medium text-[#17201E] hover:text-[#0F766E] transition-colors line-clamp-1"
              >
                {{ item.name }}
              </router-link>
              <span class="text-xs text-[#66736F] font-mono mt-0.5 block">
                {{ item.invoiceNumber }}
              </span>
            </td>

            <!-- Supplier -->
            <td class="py-3 px-5 text-xs text-[#17201E]">
              {{ item.supplier }}
            </td>

            <!-- Dokumen Kurang -->
            <td class="py-3 px-5">
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-[#FFFBEB] text-[#B45309] border border-[#FDE68A]">
                <AlertTriangle class="w-3 h-3 text-[#B45309]" />
                {{ item.missingDocs }}
              </span>
            </td>

            <!-- Nilai Tagihan -->
            <td class="py-3 px-5 text-right font-mono text-xs font-medium text-[#17201E]">
              {{ formatRupiah(item.total) }}
            </td>

            <!-- Action -->
            <td class="py-3 px-5 text-right">
              <router-link
                :to="`/programs/${item.id}`"
                class="inline-flex items-center justify-center h-7 px-2.5 text-xs font-medium rounded border border-[#DDE4E1] bg-white text-[#17201E] hover:bg-[#F0FDFA] hover:text-[#0F766E] hover:border-[#CCFBF1] transition-colors shadow-2xs"
              >
                Lihat
              </router-link>
            </td>
          </tr>

          <tr v-if="attentionList.length === 0">
            <td colspan="5" class="py-8 text-center text-xs text-[#66736F]">
              Semua dokumen program telah lengkap dan siap audit.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { ChevronRight, AlertTriangle } from 'lucide-vue-next';
import { useTaxStore, formatRupiah } from '../../store/taxStore';

const store = useTaxStore();
const attentionList = computed(() => store.needAttentionPrograms.value);
</script>
