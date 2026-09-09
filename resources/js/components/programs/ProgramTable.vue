<template>
  <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
    <!-- Desktop Table Container (hidden md:block) -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse min-w-[940px]">
        <thead>
          <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 font-sans">
            <th class="py-2.5 px-3 font-bold min-w-[160px]">PROGRAM</th>
            <th class="py-2.5 px-2.5 font-bold min-w-[130px]">SUPPLIER</th>
            <th class="py-2.5 px-2 font-bold min-w-[100px]">NO. INVOICE</th>
            <th class="py-2.5 px-2 font-bold text-right min-w-[90px]">DPP</th>
            <th class="py-2.5 px-2 font-bold text-right min-w-[85px]">PPN</th>
            <th class="py-2.5 px-2.5 font-bold text-right min-w-[105px]">TOTAL INVOICE</th>
            <th class="py-2.5 px-2 font-bold text-center min-w-[95px]">DOKUMEN</th>
            <th class="py-2.5 px-2 font-bold text-center min-w-[100px]">STATUS</th>
            <th class="py-2.5 px-2 font-bold text-center w-20 sticky right-0 bg-slate-50 border-l border-slate-200/70 shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.03)] z-10">AKSI</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr
            v-for="program in paginatedPrograms"
            :key="program.id"
            class="hover:bg-slate-50/70 transition-colors group cursor-pointer"
            @click="goToDetail(program.id)"
          >
            <!-- 1. PROGRAM -->
            <td class="py-2.5 px-3 max-w-[210px]">
              <router-link
                :to="`/programs/${program.id}`"
                class="font-bold text-slate-900 group-hover:text-[#135A46] transition-colors truncate block text-xs"
                :title="program.program_name"
                @click.stop
              >
                {{ program.program_name }}
              </router-link>
              <div class="text-[10px] text-slate-400 mt-0.5 truncate">
                {{ program.category }} · {{ formatDate(program.program_date) }}
              </div>
            </td>

            <!-- 2. SUPPLIER -->
            <td class="py-2.5 px-2.5 max-w-[150px]">
              <div class="font-medium text-slate-800 truncate text-xs" :title="program.supplier">
                {{ program.supplier }}
              </div>
              <div class="text-[10px] font-mono text-slate-400 mt-0.5 truncate">
                {{ program.npwp || '01.000.000.0-000.000' }}
              </div>
            </td>

            <!-- 3. NO. INVOICE -->
            <td class="py-2.5 px-2 font-mono text-slate-600 whitespace-nowrap text-[11px]">
              {{ program.invoice_number || '-' }}
            </td>

            <!-- 4. DPP -->
            <td class="py-2.5 px-2 font-mono text-slate-600 text-right whitespace-nowrap text-[11px]">
              {{ formatRupiah(program.dpp) }}
            </td>

            <!-- 5. PPN -->
            <td class="py-2.5 px-2 font-mono text-amber-700 font-medium text-right whitespace-nowrap text-[11px]">
              {{ formatRupiah(program.ppn) }}
            </td>

            <!-- 6. TOTAL INVOICE -->
            <td class="py-2.5 px-2.5 font-mono font-bold text-slate-900 text-right whitespace-nowrap text-xs">
              {{ formatRupiah(program.total_invoice) }}
            </td>

            <!-- 7. DOKUMEN -->
            <td class="py-2.5 px-2 text-center whitespace-nowrap" @click.stop>
              <div class="inline-flex items-center gap-1">
                <!-- INVOICE -->
                <button
                  type="button"
                  class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold transition-all cursor-pointer select-none active:scale-95"
                  :class="hasDoc(program, 'invoice')
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                    : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700'"
                  :title="hasDoc(program, 'invoice') ? 'Invoice ada - Klik untuk melihat' : 'Invoice belum ada - Klik unggah'"
                  @click="handlePillClick(program, 'invoice', 'Invoice')"
                >
                  <span v-if="!hasDoc(program, 'invoice')" class="mr-0.5 text-[8px] font-normal">+</span>IN
                </button>

                <!-- FAKTUR PAJAK -->
                <button
                  type="button"
                  class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold transition-all cursor-pointer select-none active:scale-95"
                  :class="hasDoc(program, 'faktur_pajak')
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                    : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700'"
                  :title="hasDoc(program, 'faktur_pajak') ? 'Faktur Pajak ada - Klik untuk melihat' : 'Faktur Pajak belum ada - Klik unggah'"
                  @click="handlePillClick(program, 'faktur_pajak', 'Faktur Pajak')"
                >
                  <span v-if="!hasDoc(program, 'faktur_pajak')" class="mr-0.5 text-[8px] font-normal">+</span>FP
                </button>

                <!-- MOU -->
                <button
                  type="button"
                  class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold transition-all cursor-pointer select-none active:scale-95"
                  :class="hasDoc(program, 'mou')
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                    : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700'"
                  :title="hasDoc(program, 'mou') ? 'MOU ada - Klik untuk melihat' : 'MOU belum ada - Klik unggah'"
                  @click="handlePillClick(program, 'mou', 'Memo / MOU')"
                >
                  <span v-if="!hasDoc(program, 'mou')" class="mr-0.5 text-[8px] font-normal">+</span>MO
                </button>
              </div>
            </td>

            <!-- 8. STATUS -->
            <td class="py-2.5 px-2 text-center whitespace-nowrap">
              <span
                :class="[
                  'inline-block px-2 py-0.5 rounded-full text-[10px] font-medium border',
                  getStatusBadgeClass(program)
                ]"
              >
                {{ getStatusText(program) }}
              </span>
            </td>

            <!-- 9. AKSI (Compact Icon Buttons - Sticky right) -->
            <td class="py-2.5 px-2 text-center whitespace-nowrap sticky right-0 bg-white group-hover:bg-slate-50 transition-colors border-l border-slate-100 shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.03)] z-10" @click.stop>
              <div class="inline-flex items-center justify-center gap-0.5">
                <button
                  type="button"
                  class="p-1 rounded-md text-slate-400 hover:text-[#135A46] hover:bg-emerald-50 transition-colors cursor-pointer"
                  title="Lihat Detail Program"
                  @click="goToDetail(program.id)"
                >
                  <Eye class="w-3.5 h-3.5" />
                </button>
                <button
                  type="button"
                  class="p-1 rounded-md text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors cursor-pointer"
                  title="Edit Data Program"
                  @click="openEditModal(program)"
                >
                  <Pencil class="w-3.5 h-3.5" />
                </button>
                <button
                  type="button"
                  class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                  title="Hapus Program"
                  @click="openDeleteConfirm(program)"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty State Desktop -->
          <tr v-if="filteredPrograms.length === 0">
            <td colspan="9" class="py-14 text-center">
              <div class="flex flex-col items-center justify-center space-y-2">
                <FolderArchive class="w-8 h-8 text-slate-300" />
                <p class="text-sm font-semibold text-slate-800">Tidak ada program ditemukan</p>
                <p class="text-xs text-slate-400 max-w-sm">
                  Coba sesuaikan kata kunci pencarian atau reset filter kategori, status, dan supplier.
                </p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Cards List (block md:hidden) - NO horizontal scroll required! -->
    <div class="block md:hidden divide-y divide-slate-100">
      <div
        v-for="program in paginatedPrograms"
        :key="program.id"
        class="p-4 space-y-3 hover:bg-slate-50/50 transition-colors"
        @click="goToDetail(program.id)"
      >
        <!-- Top Row: Program Name & Status Badge -->
        <div class="flex items-start justify-between gap-2.5">
          <div class="min-w-0 flex-1">
            <router-link
              :to="`/programs/${program.id}`"
              class="font-bold text-slate-900 hover:text-[#135A46] text-sm leading-snug block break-words"
              @click.stop
            >
              {{ program.program_name }}
            </router-link>
            <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-500">
              <span
                class="px-2 py-0.5 rounded text-[10px] font-medium border"
                :class="getCategoryBadgeClass(program.category)"
              >
                {{ program.category }}
              </span>
              <span>·</span>
              <span class="text-slate-400">{{ formatDate(program.program_date) }}</span>
            </div>
          </div>

          <!-- Status Badge -->
          <span
            :class="[
              'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold border shrink-0',
              getStatusBadgeClass(program)
            ]"
          >
            <span
              class="w-1.5 h-1.5 rounded-full"
              :class="{
                'bg-emerald-500': getStatusText(program) === 'Lengkap',
                'bg-rose-500': getStatusText(program) === 'Belum Lengkap',
                'bg-amber-500': getStatusText(program).startsWith('Sebagian')
              }"
            ></span>
            <span>{{ getStatusText(program) }}</span>
          </span>
        </div>

        <!-- Supplier & Financial Box (Clean compact card) -->
        <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 space-y-2 text-xs">
          <!-- Supplier -->
          <div class="flex items-center justify-between gap-2">
            <span class="text-slate-500 text-[11px] shrink-0">Supplier:</span>
            <div class="text-right min-w-0">
              <span class="font-semibold text-slate-800 block truncate max-w-[210px]">{{ program.supplier }}</span>
              <span class="font-mono text-[10px] text-slate-400 block">{{ program.npwp || '-' }}</span>
            </div>
          </div>

          <!-- No. Invoice & Total -->
          <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-200/60">
            <div>
              <span class="text-slate-500 text-[11px] block">No. Invoice</span>
              <span class="font-mono text-[11px] font-medium text-slate-700 block">{{ program.invoice_number || '-' }}</span>
            </div>
            <div class="text-right">
              <span class="text-slate-500 text-[11px] block">Total Invoice</span>
              <span class="font-mono font-bold text-xs sm:text-sm block text-[#135A46]">
                {{ formatRupiah(program.total_invoice) }}
              </span>
            </div>
          </div>

          <!-- DPP & PPN Breakdown -->
          <div class="grid grid-cols-2 gap-2 pt-1.5 border-t border-slate-200/60 text-[11px]">
            <div>
              <span class="text-slate-400 text-[10px]">DPP:</span>
              <span class="font-mono text-slate-700 ml-1">{{ formatRupiah(program.dpp) }}</span>
            </div>
            <div class="text-right">
              <span class="text-slate-400 text-[10px]">PPN 11%:</span>
              <span class="font-mono text-amber-700 font-medium ml-1">{{ formatRupiah(program.ppn) }}</span>
            </div>
          </div>
        </div>

        <!-- Dokumen Lampiran & Action Buttons Row -->
        <div class="flex items-center justify-between gap-2 pt-1" @click.stop>
          <!-- Dokumen Lampiran Interactive Pills -->
          <div class="flex items-center gap-1.5">
            <span class="text-[10px] text-slate-400 font-semibold uppercase">Dok:</span>
            <!-- INVOICE -->
            <button
              type="button"
              class="px-2 py-1 rounded text-[10px] font-mono font-bold transition-all cursor-pointer active:scale-95"
              :class="hasDoc(program, 'invoice')
                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700'"
              :title="hasDoc(program, 'invoice') ? 'Invoice ada - Klik untuk melihat' : 'Invoice belum ada - Klik unggah'"
              @click="handlePillClick(program, 'invoice', 'Invoice')"
            >
              <span v-if="!hasDoc(program, 'invoice')" class="mr-0.5 font-normal">+</span>IN
            </button>

            <!-- FAKTUR PAJAK -->
            <button
              type="button"
              class="px-2 py-1 rounded text-[10px] font-mono font-bold transition-all cursor-pointer active:scale-95"
              :class="hasDoc(program, 'faktur_pajak')
                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700'"
              :title="hasDoc(program, 'faktur_pajak') ? 'Faktur Pajak ada - Klik untuk melihat' : 'Faktur Pajak belum ada - Klik unggah'"
              @click="handlePillClick(program, 'faktur_pajak', 'Faktur Pajak')"
            >
              <span v-if="!hasDoc(program, 'faktur_pajak')" class="mr-0.5 font-normal">+</span>FP
            </button>

            <!-- MOU -->
            <button
              type="button"
              class="px-2 py-1 rounded text-[10px] font-mono font-bold transition-all cursor-pointer active:scale-95"
              :class="hasDoc(program, 'mou')
                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                : 'bg-slate-50 text-slate-400 border border-dashed border-slate-300 hover:bg-amber-50 hover:text-amber-700'"
              :title="hasDoc(program, 'mou') ? 'MOU ada - Klik untuk melihat' : 'MOU belum ada - Klik unggah'"
              @click="handlePillClick(program, 'mou', 'Memo / MOU')"
            >
              <span v-if="!hasDoc(program, 'mou')" class="mr-0.5 font-normal">+</span>MO
            </button>
          </div>

          <!-- Quick Action Buttons -->
          <div class="flex items-center gap-1.5">
            <button
              type="button"
              class="h-8 px-2.5 rounded-lg bg-emerald-50 text-[#135A46] border border-emerald-200/80 font-semibold text-xs transition-colors flex items-center gap-1 hover:bg-emerald-100 cursor-pointer"
              @click="goToDetail(program.id)"
              title="Lihat Detail"
            >
              <Eye class="w-3.5 h-3.5" />
              <span>Detail</span>
            </button>
            <button
              type="button"
              class="h-8 w-8 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-slate-200 transition-colors cursor-pointer flex items-center justify-center"
              title="Edit Data Program"
              @click="openEditModal(program)"
            >
              <Pencil class="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              class="h-8 w-8 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 border border-rose-200 transition-colors cursor-pointer flex items-center justify-center"
              title="Hapus Program"
              @click="openDeleteConfirm(program)"
            >
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State Mobile -->
      <div v-if="filteredPrograms.length === 0" class="py-12 px-4 text-center">
        <FolderArchive class="w-8 h-8 text-slate-300 mx-auto mb-2" />
        <p class="text-sm font-semibold text-slate-800">Tidak ada program ditemukan</p>
        <p class="text-xs text-slate-400 mt-1">Sesuaikan filter atau pencarian.</p>
      </div>
    </div>

    <!-- Table Footer / Summary & Pagination -->
    <div class="px-4 sm:px-5 py-3.5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
      <div class="flex items-center justify-between w-full sm:w-auto">
        <span v-if="filteredPrograms.length > 0">
          Menampilkan <strong class="text-slate-800 font-semibold">{{ startIndex + 1 }} - {{ endIndex }}</strong> dari <strong class="text-slate-800 font-semibold">{{ filteredPrograms.length }}</strong> data
        </span>
        <span v-else>
          Menampilkan <strong class="text-slate-800 font-semibold">0</strong> data
        </span>

        <!-- Mobile Total Nilai Badge -->
        <span v-if="filteredPrograms.length > 0" class="sm:hidden font-mono text-slate-800 font-bold text-[11px]">
          {{ formatRupiah(totalSum) }}
        </span>
      </div>

      <!-- Pagination Controls (Sebelumnya / Next) -->
      <div v-if="totalPages > 1" class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-2">
        <button
          type="button"
          :disabled="currentPage === 1"
          @click="currentPage--"
          class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors cursor-pointer flex items-center gap-1 shadow-2xs"
        >
          <ChevronLeft class="w-3.5 h-3.5" />
          <span>Sebelumnya</span>
        </button>

        <!-- Page indicators: Mobile shows 'X / Y', Desktop shows page numbers -->
        <div class="sm:hidden text-xs font-semibold text-slate-700 px-2">
          {{ currentPage }} / {{ totalPages }}
        </div>

        <div class="hidden sm:flex items-center gap-1">
          <button
            v-for="page in totalPages"
            :key="page"
            type="button"
            @click="currentPage = page"
            :class="[
              'w-7 h-7 rounded-lg text-xs font-semibold flex items-center justify-center transition-colors cursor-pointer',
              currentPage === page
                ? 'bg-[#135A46] text-white shadow-2xs'
                : 'text-slate-600 hover:bg-slate-100'
            ]"
          >
            {{ page }}
          </button>
        </div>

        <button
          type="button"
          :disabled="currentPage >= totalPages"
          @click="currentPage++"
          class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors cursor-pointer flex items-center gap-1 shadow-2xs"
        >
          <span>Selanjutnya</span>
          <ChevronRight class="w-3.5 h-3.5" />
        </button>
      </div>

      <div class="hidden sm:flex items-center gap-4 text-xs">
        <span v-if="filteredPrograms.length > 0">
          Total Nilai: <strong class="font-mono text-slate-800 font-bold ml-1">{{ formatRupiah(totalSum) }}</strong>
        </span>
      </div>
    </div>

    <!-- Quick Edit Modal -->
    <Teleport to="body">
      <div
        v-if="isEditModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/40 backdrop-blur-xs animate-in fade-in duration-150"
        @click.self="isEditModalOpen = false"
      >
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden max-h-[92vh] flex flex-col">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <h3 class="text-sm font-bold text-slate-900">Edit Data Program</h3>
            <button
              type="button"
              class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
              @click="isEditModalOpen = false"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <form @submit.prevent="saveEdit" class="p-4 sm:p-5 space-y-3.5 overflow-y-auto flex-1">
            <div>
              <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nama Program</label>
              <input
                v-model="editForm.program_name"
                type="text"
                required
                class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46]"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Supplier / Vendor</label>
                <input
                  v-model="editForm.supplier"
                  type="text"
                  required
                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46]"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">NPWP Vendor</label>
                <input
                  v-model="editForm.npwp"
                  type="text"
                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 font-mono focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46]"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">No. Invoice</label>
                <input
                  v-model="editForm.invoice_number"
                  type="text"
                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 font-mono focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46]"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kategori</label>
                <select
                  v-model="editForm.category"
                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46]"
                >
                  <option v-for="cat in categoriesList" :key="cat" :value="cat">
                    {{ cat }}
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nilai DPP (IDR)</label>
                <input
                  v-model.number="editForm.dpp"
                  type="number"
                  required
                  @input="handleDppInput"
                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 font-mono focus:outline-none focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46]"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">PPN 11% (IDR)</label>
                <input
                  v-model.number="editForm.ppn"
                  type="number"
                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 font-mono bg-slate-50 focus:outline-none"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Total Invoice</label>
                <input
                  :value="editForm.dpp + editForm.ppn"
                  type="number"
                  readonly
                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 font-mono bg-slate-50 font-bold text-slate-800"
                />
              </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
              <button
                type="button"
                class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors cursor-pointer"
                @click="isEditModalOpen = false"
              >
                Batal
              </button>
              <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-[#135A46] text-white font-semibold text-xs hover:bg-[#0e4334] transition-colors shadow-2xs cursor-pointer"
              >
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <div
        v-if="programToDelete"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/40 backdrop-blur-xs animate-in fade-in duration-150"
        @click.self="programToDelete = null"
      >
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full p-5 text-center">
          <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3">
            <Trash2 class="w-5 h-5" />
          </div>
          <h3 class="text-sm font-bold text-slate-900 mb-1">Hapus Program?</h3>
          <p class="text-xs text-slate-500 leading-relaxed mb-5">
            Apakah Anda yakin ingin menghapus <strong class="text-slate-800">{{ programToDelete.program_name }}</strong>? Data yang dihapus tidak dapat dipulihkan.
          </p>
          <div class="grid grid-cols-2 gap-2.5">
            <button
              type="button"
              class="w-full py-2 rounded-lg border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors cursor-pointer"
              @click="programToDelete = null"
            >
              Batal
            </button>
            <button
              type="button"
              class="w-full py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs transition-colors shadow-2xs cursor-pointer"
              @click="executeDelete"
            >
              Ya, Hapus
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Document Preview Sheet -->
    <DocumentPreviewSheet
      v-model:open="isPreviewOpen"
      :document="activeDocument"
      :program="activeProgram"
    />

    <!-- Document Upload Modal -->
    <DocumentUploadModal
      v-model:open="isUploadOpen"
      :docType="uploadDocType"
      :docLabel="uploadDocLabel"
      @uploaded="handleUploaded"
    />
  </div>
</template>

<script setup>
import { computed, ref, reactive, watch } from 'vue';
import { useRouter } from 'vue-router';
import { FolderArchive, Trash2, X, ChevronLeft, ChevronRight, Eye, Pencil } from 'lucide-vue-next';
import { useTaxStore, formatRupiah, formatDate } from '../../store/taxStore';
import DocumentPreviewSheet from '../detail/DocumentPreviewSheet.vue';
import DocumentUploadModal from '../detail/DocumentUploadModal.vue';

const router = useRouter();
const store = useTaxStore();

const filteredPrograms = computed(() => store.filteredPrograms.value);
const totalCount = computed(() => store.programs.value.length);
const categoriesList = computed(() => store.categoriesList.value.filter(c => c !== 'Semua Kategori'));

// Pagination state (10 data per halaman)
const itemsPerPage = 10;
const currentPage = ref(1);

const totalPages = computed(() => {
  return Math.ceil(filteredPrograms.value.length / itemsPerPage) || 1;
});

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage);
const endIndex = computed(() => {
  return Math.min(startIndex.value + itemsPerPage, filteredPrograms.value.length);
});

const paginatedPrograms = computed(() => {
  return filteredPrograms.value.slice(startIndex.value, endIndex.value);
});

// Reset ke halaman 1 jika filter berubah
watch(filteredPrograms, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = 1;
  }
});

const totalSum = computed(() => {
  return filteredPrograms.value.reduce((acc, p) => acc + (Number(p.total_invoice) || 0), 0);
});

// Document Preview & Upload states
const isPreviewOpen = ref(false);
const activeDocument = ref(null);
const activeProgram = ref(null);

const isUploadOpen = ref(false);
const uploadTargetProgram = ref(null);
const uploadDocType = ref('invoice');
const uploadDocLabel = ref('Invoice');

// Edit Modal State
const isEditModalOpen = ref(false);
const editProgramId = ref(null);
const editForm = reactive({
  program_name: '',
  supplier: '',
  npwp: '',
  invoice_number: '',
  category: 'Logistik',
  dpp: 0,
  ppn: 0
});

// Delete Modal State
const programToDelete = ref(null);

function goToDetail(id) {
  router.push(`/programs/${id}`);
}

function hasDoc(program, docType) {
  if (!program.documents || !Array.isArray(program.documents)) return false;
  return program.documents.some((doc) => doc.document_type === docType);
}

function getDoc(program, docType) {
  if (!program.documents || !Array.isArray(program.documents)) return null;
  return program.documents.find((doc) => doc.document_type === docType);
}

function handlePillClick(program, docType, docLabel) {
  const existingDoc = getDoc(program, docType);
  if (existingDoc) {
    activeProgram.value = program;
    activeDocument.value = existingDoc;
    isPreviewOpen.value = true;
  } else {
    uploadTargetProgram.value = program;
    uploadDocType.value = docType;
    uploadDocLabel.value = `${docLabel} (${program.program_name})`;
    isUploadOpen.value = true;
  }
}

async function handleUploaded(fileData) {
  if (uploadTargetProgram.value) {
    await store.uploadDocument(uploadTargetProgram.value.id, fileData.docType, fileData);
    isUploadOpen.value = false;
  }
}

function openEditModal(program) {
  editProgramId.value = program.id;
  editForm.program_name = program.program_name;
  editForm.supplier = program.supplier;
  editForm.npwp = program.npwp || '';
  editForm.invoice_number = program.invoice_number || '';
  editForm.category = program.category || 'Logistik';
  editForm.dpp = Number(program.dpp) || 0;
  editForm.ppn = Number(program.ppn) || Math.round((Number(program.dpp) || 0) * 0.11);
  isEditModalOpen.value = true;
}

function handleDppInput() {
  editForm.ppn = Math.round((Number(editForm.dpp) || 0) * 0.11);
}

async function saveEdit() {
  if (!editProgramId.value) return;
  await store.updateProgram(editProgramId.value, {
    program_name: editForm.program_name,
    supplier: editForm.supplier,
    npwp: editForm.npwp,
    invoice_number: editForm.invoice_number,
    category: editForm.category,
    dpp: editForm.dpp,
    ppn: editForm.ppn,
    total_invoice: editForm.dpp + editForm.ppn
  });
  isEditModalOpen.value = false;
}

function openDeleteConfirm(program) {
  programToDelete.value = program;
}

async function executeDelete() {
  if (programToDelete.value) {
    await store.deleteProgram(programToDelete.value.id);
    programToDelete.value = null;
  }
}

function getCategoryBadgeClass(category) {
  switch (category) {
    case 'Logistik':
      return 'bg-blue-50 text-blue-700 border-blue-100';
    case 'IT & Software':
    case 'Teknologi':
      return 'bg-purple-50 text-purple-700 border-purple-100';
    case 'Operasional':
      return 'bg-emerald-50 text-emerald-700 border-emerald-100';
    case 'Pengadaan Material':
    case 'Material':
      return 'bg-orange-50 text-orange-700 border-orange-100';
    case 'Distribusi':
      return 'bg-cyan-50 text-cyan-700 border-cyan-100';
    case 'Jasa Konsultasi':
    case 'Jasa':
      return 'bg-indigo-50 text-indigo-700 border-indigo-100';
    default:
      return 'bg-slate-50 text-slate-700 border-slate-200';
  }
}

function getStatusText(program) {
  const docs = program.documents || [];
  if (docs.length === 3) return 'Lengkap';
  if (docs.length === 0) return 'Belum Lengkap';
  return `Sebagian (${docs.length}/3)`;
}

function getStatusBadgeClass(program) {
  const docs = program.documents || [];
  if (docs.length === 3) {
    return 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
  }
  if (docs.length === 0) {
    return 'bg-rose-50 text-rose-700 border-rose-200/80';
  }
  return 'bg-amber-50 text-amber-700 border-amber-200/80';
}
</script>
