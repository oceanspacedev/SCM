<template>
  <div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-[#17201E]">
        Pengaturan Sistem
      </h1>
      <p class="text-sm text-[#66736F] mt-1">
        Konfigurasi parameter perpajakan, audit compliance, dan penyimpanan berkas SCM TaxVault.
      </p>
    </div>

    <!-- Section 1: Tax Rate Configuration -->
    <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs overflow-hidden">
      <div class="p-5 border-b border-[#DDE4E1]">
        <h3 class="text-sm font-semibold text-[#17201E]">
          Parameter Tarif PPN & Aturan Perpajakan
        </h3>
        <p class="text-xs text-[#66736F] mt-0.5">
          Pengaturan default perhitungan otomatis Pajak Pertambahan Nilai pada tagihan program SCM
        </p>
      </div>
      <div class="p-5 space-y-4 text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
          <div>
            <label class="font-semibold text-[#17201E] block">Tarif Standar PPN (%)</label>
            <span class="text-[#66736F]">Sesuai UU HPP No. 7 Tahun 2021</span>
          </div>
          <div class="sm:col-span-2 max-w-xs">
            <select class="w-full bg-white text-[#17201E] text-xs rounded-md border border-[#DDE4E1] px-3 py-2">
              <option value="11">11% (Tarif Berlaku Saat Ini)</option>
              <option value="12">12% (Kesiapan Penyesuaian Regulasi Baru)</option>
            </select>
          </div>
        </div>

        <div class="border-t border-[#EBEFEF] pt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
          <div>
            <label class="font-semibold text-[#17201E] block">Validasi Kode Faktur Pajak</label>
            <span class="text-[#66736F]">Format 16 digit e-Faktur DJP</span>
          </div>
          <div class="sm:col-span-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#F0FDF4] text-[#15803D] rounded border border-[#BBF7D0] font-medium">
              Aktif - Format 010.xxx-xx.xxxxxxxx
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 2: Document Compliance Rules -->
    <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs overflow-hidden">
      <div class="p-5 border-b border-[#DDE4E1]">
        <h3 class="text-sm font-semibold text-[#17201E]">
          Syarat Kepatuhan Dokumen (Audit Checklist)
        </h3>
        <p class="text-xs text-[#66736F] mt-0.5">
          3 Dokumen wajib yang menjadi tolok ukur kesiapan audit eksternal & internal
        </p>
      </div>
      <div class="p-5 divide-y divide-[#EBEFEF] text-xs">
        <div class="py-2.5 flex items-center justify-between">
          <div>
            <p class="font-semibold text-[#17201E]">1. Commercial Invoice</p>
            <p class="text-[#66736F]">Bukti tagihan resmi dari supplier berbadan hukum lengkap dengan rincian termin.</p>
          </div>
          <span class="px-2 py-0.5 rounded bg-[#F4F6F5] text-[#17201E] border border-[#DDE4E1] font-mono">Wajib</span>
        </div>
        <div class="py-2.5 flex items-center justify-between">
          <div>
            <p class="font-semibold text-[#17201E]">2. Faktur Pajak (e-Faktur DJP)</p>
            <p class="text-[#66736F]">Faktur pajak masukan yang telah diapprove dan terekonsiliasi dengan DJP Online.</p>
          </div>
          <span class="px-2 py-0.5 rounded bg-[#F4F6F5] text-[#17201E] border border-[#DDE4E1] font-mono">Wajib</span>
        </div>
        <div class="py-2.5 flex items-center justify-between">
          <div>
            <p class="font-semibold text-[#17201E]">3. Memo / MOU Perjanjian Kerjasama</p>
            <p class="text-[#66736F]">Perjanjian kerja, purchase order, atau dasar hukum pengadaan barang/jasa SCM.</p>
          </div>
          <span class="px-2 py-0.5 rounded bg-[#F4F6F5] text-[#17201E] border border-[#DDE4E1] font-mono">Wajib</span>
        </div>
      </div>
    </div>

    <!-- Section 3: Data Management & Demo Settings -->
    <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs overflow-hidden">
      <div class="p-5 border-b border-[#DDE4E1]">
        <h3 class="text-sm font-semibold text-[#17201E]">
          Manajemen Data Uji Coba & Akses Demo
        </h3>
        <p class="text-xs text-[#66736F] mt-0.5">
          Pengaturan akses akun demo login cepat dan reset dataset program SCM
        </p>
      </div>

      <div class="p-5 divide-y divide-[#EBEFEF] text-xs space-y-4">
        <!-- Setting 1: Tampilkan Akun Demo ON / OFF -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="font-semibold text-[#17201E]">Tampilkan Akun Demo di Login</span>
              <span
                class="px-2 py-0.5 rounded-full text-[10px] font-semibold transition-colors"
                :class="showDemoAccounts ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
              >
                {{ showDemoAccounts ? 'Aktif (ON)' : 'Nonaktif (OFF)' }}
              </span>
            </div>
            <p class="text-[#66736F] mt-0.5">
              Tampilkan tombol pemilihan cepat akun simulasi (Admin SCM, Tim Pajak, Staf SCM) pada form login.
            </p>
          </div>
          <button
            type="button"
            role="switch"
            :aria-checked="showDemoAccounts"
            @click="toggleDemoAccounts"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden"
            :class="showDemoAccounts ? 'bg-blue-600' : 'bg-slate-300'"
            title="Nyalakan / Matikan Akun Demo di Login"
          >
            <span
              aria-hidden="true"
              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
              :class="showDemoAccounts ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
        </div>

        <!-- Setting 2: Reset Data (Hapus Semua) -->
        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <p class="font-semibold text-[#17201E]">Hapus & Kosongkan Semua Data</p>
            <p class="text-[#66736F] mt-0.5">
              Hapus seluruh data arsip program, berkas lampiran, dan data hasil impor secara total (data menjadi kosong).
            </p>
          </div>
          <button
            type="button"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-md border border-rose-200 bg-rose-50/60 hover:bg-rose-100 text-rose-700 font-semibold text-xs transition-colors cursor-pointer shrink-0"
            @click="isResetModalOpen = true"
          >
            <RotateCcw class="w-3.5 h-3.5" />
            <span>Hapus Semua Data</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Reset Data -->
    <Teleport to="body">
      <div
        v-if="isResetModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
        @click.self="!isResetting && (isResetModalOpen = false)"
      >
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
          <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100">
            <RotateCcw class="w-6 h-6" :class="{ 'animate-spin': isResetting }" />
          </div>

          <h3 class="text-base font-bold text-slate-900 text-center mb-1">
            Hapus Semua Data?
          </h3>
          <p class="text-xs text-slate-500 text-center leading-relaxed mb-5">
            Apakah Anda yakin ingin menghapus <strong class="text-slate-700">SELURUH data</strong> arsip program, berkas lampiran, dan riwayat dokumen secara permanen? Data akan dikosongkan total dari sistem.
          </p>

          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              :disabled="isResetting"
              class="w-full py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors cursor-pointer disabled:opacity-50"
              @click="isResetModalOpen = false"
            >
              Batal
            </button>
            <button
              type="button"
              :disabled="isResetting"
              class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-1.5 disabled:opacity-75"
              @click="executeReset"
            >
              <RotateCcw class="w-3.5 h-3.5" :class="{ 'animate-spin': isResetting }" />
              <span>{{ isResetting ? 'Menghapus...' : 'Ya, Hapus Semua' }}</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { RotateCcw } from 'lucide-vue-next';
import { useTaxStore } from '../store/taxStore';

const store = useTaxStore();
const isResetModalOpen = ref(false);

const showDemoAccounts = computed(() => store.showDemoAccounts.value);
const isResetting = computed(() => store.isResetting.value);

async function toggleDemoAccounts() {
  await store.setDemoAccountsVisibility(!showDemoAccounts.value);
}

async function executeReset() {
  const res = await store.resetEntireSystemData();
  if (res && res.success) {
    isResetModalOpen.value = false;
  }
}
</script>
