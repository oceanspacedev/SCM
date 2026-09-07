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

    <!-- Section 3: Data Management & Reset -->
    <div class="bg-white rounded-lg border border-[#DDE4E1] shadow-2xs overflow-hidden">
      <div class="p-5 border-b border-[#DDE4E1]">
        <h3 class="text-sm font-semibold text-[#17201E]">
          Manajemen Data Uji Coba
        </h3>
        <p class="text-xs text-[#66736F] mt-0.5">
          Reset atau muat ulang 18 dataset dummy program SCM untuk simulasi
        </p>
      </div>
      <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
        <div>
          <p class="font-semibold text-[#17201E]">Kembalikan ke Data Awal (18 Program)</p>
          <p class="text-[#66736F] mt-0.5">
            Reset semua perubahan, unggahan dokumen, dan program baru yang telah diimport ke kondisi bawaan (6 lengkap, 7 sebagian, 5 belum lengkap).
          </p>
        </div>
        <button
          type="button"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-md border border-[#DDE4E1] bg-white hover:bg-[#FEF2F2] hover:text-[#B91C1C] hover:border-[#FECACA] font-medium text-[#17201E] transition-colors cursor-pointer shrink-0"
          @click="handleReset"
        >
          <span>Reset ke Data Awal</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useTaxStore } from '../store/taxStore';

const store = useTaxStore();

function handleReset() {
  if (confirm("Apakah Anda ingin mereset seluruh data kembali ke 18 data awal?")) {
    store.resetToDefault();
  }
}
</script>
