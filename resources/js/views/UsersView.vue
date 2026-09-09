<template>
  <div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
          Manajemen User
        </h1>
        <p class="text-xs text-slate-500 mt-0.5 sm:mt-1">
          Kelola akses personel divisi SCM dan Tim Pajak
        </p>
      </div>

      <!-- Action: + Tambah User Button -->
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <button
          type="button"
          class="h-9 px-4 w-full sm:w-auto justify-center rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white font-semibold text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-sm shadow-[#135A46]/20"
          @click="openAddModal"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah User</span>
        </button>
      </div>
    </div>

    <!-- Admin Controls Bar: Demo Account Toggle & Reset Data -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-3.5 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <!-- Left: Demo Accounts Toggle -->
      <div class="flex items-start sm:items-center gap-3 sm:gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#135A46] border border-emerald-100 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
          <Eye v-if="showDemoAccounts" class="w-5 h-5" />
          <EyeOff v-else class="w-5 h-5 text-slate-400" />
        </div>
        <div class="min-w-0">
          <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
            <span class="text-xs font-bold text-slate-900">Tampilkan Akun Demo di Login</span>
            <span
              class="px-2 py-0.5 rounded-full text-[10px] font-semibold transition-colors"
              :class="showDemoAccounts ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
            >
              {{ showDemoAccounts ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>
          <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
            Menampilkan tombol cepat akun demo (Admin SCM, Tim Pajak, Staf SCM) di form masuk
          </p>
        </div>
      </div>

      <!-- Right: Actions (Toggle Switch & Reset Data Button) -->
      <div class="flex items-center justify-between md:justify-end gap-3 pt-3 md:pt-0 border-t border-slate-100 md:border-t-0 shrink-0 w-full md:w-auto">
        <!-- Switch Toggle Button -->
        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-600 font-medium md:hidden">Akun Demo:</span>
          <button
            type="button"
            role="switch"
            :aria-checked="showDemoAccounts"
            @click="handleToggleDemoAccounts"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden"
            :class="showDemoAccounts ? 'bg-[#135A46]' : 'bg-slate-300'"
            title="Nyalakan / Matikan Akun Demo di Login"
          >
            <span
              aria-hidden="true"
              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
              :class="showDemoAccounts ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
        </div>

        <div class="h-6 w-px bg-slate-200 hidden md:block"></div>

        <!-- Reset Data Button -->
        <button
          type="button"
          class="h-9 px-3.5 rounded-lg border border-rose-200 bg-rose-50/60 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-all cursor-pointer flex items-center gap-1.5 active:scale-95 shadow-2xs"
          @click="isResetModalOpen = true"
        >
          <RotateCcw class="w-3.5 h-3.5" />
          <span>Reset Data</span>
        </button>
      </div>
    </div>

    <!-- Filter Tabs & Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 text-xs">
      <div class="flex gap-2 sm:gap-6 overflow-x-auto pb-2 -mb-px scrollbar-none">
        <button
          type="button"
          class="pb-2 border-b-2 font-semibold transition-all cursor-pointer flex items-center gap-1.5 sm:gap-2 shrink-0 whitespace-nowrap"
          :class="activeFilter === 'all' ? 'border-[#135A46] text-[#135A46]' : 'border-transparent text-slate-500 hover:text-slate-800'"
          @click="activeFilter = 'all'"
        >
          <span>Semua User</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600 font-normal">
            {{ allUsers.length }}
          </span>
        </button>

        <button
          type="button"
          class="pb-2 border-b-2 font-semibold transition-all cursor-pointer flex items-center gap-1.5 sm:gap-2 shrink-0 whitespace-nowrap"
          :class="activeFilter === 'pending' ? 'border-[#135A46] text-[#135A46]' : 'border-transparent text-slate-500 hover:text-slate-800'"
          @click="activeFilter = 'pending'"
        >
          <span>Menunggu ACC</span>
          <span
            v-if="pendingUsers.length > 0"
            class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900"
          >
            {{ pendingUsers.length }}
          </span>
          <span v-else class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600 font-normal">
            0
          </span>
        </button>

        <button
          type="button"
          class="pb-2 border-b-2 font-semibold transition-all cursor-pointer flex items-center gap-1.5 sm:gap-2 shrink-0 whitespace-nowrap"
          :class="activeFilter === 'approved' ? 'border-[#135A46] text-[#135A46]' : 'border-transparent text-slate-500 hover:text-slate-800'"
          @click="activeFilter = 'approved'"
        >
          <span>Aktif</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600 font-normal">
            {{ approvedUsers.length }}
          </span>
        </button>
      </div>

      <!-- Search Input -->
      <div class="relative w-full sm:w-64 pb-2">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama atau email..."
          class="w-full h-9 pl-9 pr-3 text-xs rounded-lg border border-slate-200 focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46] focus:outline-hidden transition-all placeholder:text-slate-400 bg-white"
        />
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" />
      </div>
    </div>

    <!-- Desktop Users Table (hidden md:block) -->
    <div class="hidden md:block bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse min-w-[640px]">
          <thead>
            <tr class="border-b border-slate-200/80 bg-white text-[11px] font-bold uppercase tracking-wider text-slate-400">
              <th class="py-3.5 px-5">NAMA</th>
              <th class="py-3.5 px-5">EMAIL</th>
              <th class="py-3.5 px-5">PERAN</th>
              <th class="py-3.5 px-5">DIBUAT</th>
              <th class="py-3.5 px-5">STATUS</th>
              <th class="py-3.5 px-5 text-right">AKSI</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr
              v-for="user in filteredList"
              :key="user.id"
              class="hover:bg-slate-50/70 transition-colors"
            >
              <!-- NAMA -->
              <td class="py-3.5 px-5 font-semibold text-slate-900">
                <div class="flex items-center gap-2.5">
                  <div
                    class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-[10px] shrink-0"
                    :class="getAvatarColorClass(user.role)"
                  >
                    {{ getInitials(user.name) }}
                  </div>
                  <span class="truncate max-w-[180px] lg:max-w-none">{{ user.name }}</span>
                </div>
              </td>

              <!-- EMAIL -->
              <td class="py-3.5 px-5 font-mono text-slate-600">
                {{ user.email }}
              </td>

              <!-- PERAN -->
              <td class="py-3.5 px-5 whitespace-nowrap">
                <span
                  class="px-2.5 py-1 rounded-full text-[11px] font-medium border"
                  :class="getRoleBadgeClass(user.role)"
                >
                  {{ user.role }}
                </span>
              </td>

              <!-- DIBUAT -->
              <td class="py-3.5 px-5 text-slate-500 whitespace-nowrap">
                {{ formatDate(user.registered_at) }}
              </td>

              <!-- STATUS -->
              <td class="py-3.5 px-5 whitespace-nowrap">
                <span
                  class="px-2.5 py-1 rounded-full text-[11px] font-semibold inline-flex items-center gap-1.5 border"
                  :class="{
                    'bg-emerald-50 text-emerald-700 border-emerald-200': user.status === 'approved',
                    'bg-amber-50 text-amber-800 border-amber-200': user.status === 'pending',
                    'bg-slate-100 text-slate-600 border-slate-200': user.status === 'rejected'
                  }"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="{
                      'bg-emerald-500': user.status === 'approved',
                      'bg-amber-500': user.status === 'pending',
                      'bg-slate-400': user.status === 'rejected'
                    }"
                  ></span>
                  <span>{{ user.status === 'approved' ? 'Aktif' : user.status === 'pending' ? 'Menunggu ACC' : 'Nonaktif' }}</span>
                </span>
              </td>

              <!-- AKSI -->
              <td class="py-3.5 px-5 text-right whitespace-nowrap">
                <div class="inline-flex items-center gap-2" v-if="user.email !== 'admin@scm.corp'">
                  <!-- Pending -> ACC -->
                  <button
                    v-if="user.status === 'pending'"
                    type="button"
                    :disabled="processingId === user.id"
                    class="h-7 px-3 rounded-md bg-[#135A46] hover:bg-[#0F4939] text-white text-[11px] font-semibold transition-colors cursor-pointer disabled:opacity-60 flex items-center gap-1"
                    @click="handleApprove(user)"
                  >
                    <span v-if="processingId === user.id" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    <span v-else>ACC</span>
                  </button>

                  <!-- Active -> Nonaktifkan -->
                  <button
                    v-else-if="user.status === 'approved'"
                    type="button"
                    :disabled="processingId === user.id"
                    class="h-7 px-3 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 text-[11px] font-medium transition-colors cursor-pointer disabled:opacity-50"
                    @click="handleReject(user)"
                  >
                    Nonaktifkan
                  </button>

                  <!-- Inactive -> Aktifkan -->
                  <button
                    v-else
                    type="button"
                    :disabled="processingId === user.id"
                    class="h-7 px-3 rounded-md text-[#135A46] hover:bg-emerald-50 border border-emerald-200 text-[11px] font-semibold transition-colors cursor-pointer disabled:opacity-50"
                    @click="handleApprove(user)"
                  >
                    Aktifkan
                  </button>

                  <!-- Trash Delete Icon Button -->
                  <button
                    type="button"
                    :disabled="processingId === user.id"
                    class="w-7 h-7 rounded-md text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer flex items-center justify-center disabled:opacity-50"
                    @click="promptDelete(user)"
                    title="Hapus Akun"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
                <span v-else class="text-[11px] text-slate-400 font-medium">
                  Super Admin
                </span>
              </td>
            </tr>

            <!-- Empty State Desktop -->
            <tr v-if="filteredList.length === 0">
              <td colspan="6" class="py-12 text-center text-slate-400">
                <Users class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                <p class="font-medium text-slate-600">Tidak ada data pengguna yang sesuai.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile Cards View (block md:hidden) -->
    <div class="block md:hidden space-y-3">
      <div
        v-for="user in filteredList"
        :key="user.id"
        class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-3.5 sm:p-4 space-y-3 transition-all"
      >
        <!-- Card Top: Avatar, Name, Role & Status -->
        <div class="flex items-start justify-between gap-2.5">
          <div class="flex items-center gap-3 min-w-0">
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
              :class="getAvatarColorClass(user.role)"
            >
              {{ getInitials(user.name) }}
            </div>
            <div class="min-w-0">
              <h3 class="font-bold text-sm text-slate-900 truncate leading-tight">
                {{ user.name }}
              </h3>
              <div class="flex items-center gap-1.5 mt-1">
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-medium border inline-block"
                  :class="getRoleBadgeClass(user.role)"
                >
                  {{ user.role }}
                </span>
              </div>
            </div>
          </div>

          <!-- Status Badge -->
          <span
            class="px-2 py-0.5 rounded-full text-[10px] font-semibold inline-flex items-center gap-1 border shrink-0"
            :class="{
              'bg-emerald-50 text-emerald-700 border-emerald-200': user.status === 'approved',
              'bg-amber-50 text-amber-800 border-amber-200': user.status === 'pending',
              'bg-slate-100 text-slate-600 border-slate-200': user.status === 'rejected'
            }"
          >
            <span
              class="w-1.5 h-1.5 rounded-full"
              :class="{
                'bg-emerald-500': user.status === 'approved',
                'bg-amber-500': user.status === 'pending',
                'bg-slate-400': user.status === 'rejected'
              }"
            ></span>
            <span>{{ user.status === 'approved' ? 'Aktif' : user.status === 'pending' ? 'Menunggu' : 'Nonaktif' }}</span>
          </span>
        </div>

        <!-- Card Middle: Contact & Info -->
        <div class="bg-slate-50/80 rounded-lg p-2.5 space-y-1.5 text-xs text-slate-600 border border-slate-100">
          <div class="flex items-center gap-2 truncate">
            <Mail class="w-3.5 h-3.5 text-slate-400 shrink-0" />
            <span class="font-mono text-[11px] truncate select-all text-slate-700">{{ user.email }}</span>
          </div>

          <div v-if="user.phone" class="flex items-center gap-2 truncate">
            <Phone class="w-3.5 h-3.5 text-slate-400 shrink-0" />
            <span class="font-mono text-[11px] text-slate-700">{{ user.phone }}</span>
          </div>

          <div class="flex items-center gap-2 text-slate-400 text-[10px]">
            <Calendar class="w-3.5 h-3.5 shrink-0" />
            <span>Terdaftar: {{ formatDate(user.registered_at) }}</span>
          </div>
        </div>

        <!-- Card Bottom: Action Buttons -->
        <div class="pt-2 flex items-center justify-between gap-2 border-t border-slate-100">
          <div v-if="user.email === 'admin@scm.corp'">
            <span class="text-[11px] text-slate-400 font-medium italic">
              Super Admin Sistem
            </span>
          </div>

          <template v-else>
            <!-- Delete Button (left aligned) -->
            <button
              type="button"
              :disabled="processingId === user.id"
              class="h-8 px-2.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition-colors cursor-pointer flex items-center gap-1.5 disabled:opacity-50"
              @click="promptDelete(user)"
              title="Hapus Akun"
            >
              <Trash2 class="w-3.5 h-3.5" />
              <span>Hapus</span>
            </button>

            <div class="flex items-center gap-2">
              <!-- Pending -> ACC -->
              <button
                v-if="user.status === 'pending'"
                type="button"
                :disabled="processingId === user.id"
                class="h-8 px-3.5 rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white text-xs font-semibold transition-colors cursor-pointer disabled:opacity-60 flex items-center gap-1.5 shadow-2xs"
                @click="handleApprove(user)"
              >
                <span v-if="processingId === user.id" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <Check v-else class="w-3.5 h-3.5" />
                <span>ACC Akun</span>
              </button>

              <!-- Active -> Nonaktifkan -->
              <button
                v-else-if="user.status === 'approved'"
                type="button"
                :disabled="processingId === user.id"
                class="h-8 px-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 text-xs font-medium transition-colors cursor-pointer disabled:opacity-50"
                @click="handleReject(user)"
              >
                Nonaktifkan
              </button>

              <!-- Inactive -> Aktifkan -->
              <button
                v-else
                type="button"
                :disabled="processingId === user.id"
                class="h-8 px-3.5 rounded-lg bg-[#135A46] hover:bg-[#0F4939] text-white border border-[#135A46] text-xs font-semibold transition-colors cursor-pointer disabled:opacity-50 flex items-center gap-1.5"
                @click="handleApprove(user)"
              >
                <Check class="w-3.5 h-3.5" />
                <span>Aktifkan</span>
              </button>
            </div>
          </template>
        </div>
      </div>

      <!-- Empty State Mobile -->
      <div
        v-if="filteredList.length === 0"
        class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-8 text-center text-slate-400"
      >
        <Users class="w-8 h-8 text-slate-300 mx-auto mb-2" />
        <p class="font-medium text-slate-600 text-xs">Tidak ada data pengguna yang sesuai.</p>
      </div>
    </div>

    <!-- Modal: + Tambah User Baru -->
    <Teleport to="body">
      <div
        v-if="isAddModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs"
        @click.self="isAddModalOpen = false"
      >
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden max-h-[92vh] flex flex-col">
          <!-- Modal Header -->
          <div class="px-5 sm:px-6 py-3.5 sm:py-4 border-b border-slate-200 flex items-center justify-between bg-white shrink-0">
            <div>
              <h3 class="text-base font-bold text-slate-900">
                Tambah User Baru
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">
                Tambahkan personel internal untuk akses SCM TaxVault
              </p>
            </div>
            <button
              type="button"
              class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
              @click="isAddModalOpen = false"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <!-- Form Body -->
          <form @submit.prevent="submitAddUser" class="p-5 sm:p-6 space-y-3.5 overflow-y-auto flex-1">
            <div
              v-if="addError"
              class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2"
            >
              <AlertCircle class="w-4 h-4 shrink-0" />
              <span>{{ addError }}</span>
            </div>

            <div class="space-y-1">
              <label class="block text-xs font-semibold text-slate-700">
                Nama Lengkap <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="addForm.name"
                type="text"
                placeholder="Contoh: Budi Santoso"
                required
                class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">
                  Email <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="addForm.email"
                  type="email"
                  placeholder="nama@perusahaan.com"
                  required
                  class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400"
                />
              </div>

              <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">
                  Nomor WhatsApp
                </label>
                <input
                  v-model="addForm.phone"
                  type="text"
                  placeholder="081234567890"
                  class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400 font-mono"
                />
              </div>
            </div>

            <div class="space-y-1">
              <label class="block text-xs font-semibold text-slate-700">
                Peran / Role <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="addForm.role"
                required
                class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all bg-white text-slate-800 font-medium"
              >
                <option value="Admin SCM">Admin SCM</option>
                <option value="Tim Pajak">Tim Pajak & Audit</option>
                <option value="Staf SCM">Staf SCM</option>
              </select>
            </div>

            <div class="space-y-1">
              <label class="block text-xs font-semibold text-slate-700">
                Kata Sandi <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="addForm.password"
                type="password"
                placeholder="Minimal 6 karakter"
                required
                minlength="6"
                class="w-full h-9 px-3 text-xs rounded-lg border border-slate-300 focus:border-[#135A46] focus:ring-2 focus:ring-[#135A46]/20 focus:outline-hidden transition-all placeholder:text-slate-400"
              />
            </div>

            <!-- Footer Buttons -->
            <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100 shrink-0">
              <button
                type="button"
                class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer"
                @click="isAddModalOpen = false"
              >
                Batal
              </button>

              <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white text-xs font-semibold transition-all cursor-pointer shadow-sm flex items-center gap-1.5"
                :disabled="isSubmitting"
              >
                <span v-if="isSubmitting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span v-else>Simpan & Buat Akun</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Custom In-App Confirmation Modal for Delete -->
    <Teleport to="body">
      <div
        v-if="userToDelete"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs"
        @click.self="userToDelete = null"
      >
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 p-5 sm:p-6 text-center space-y-4">
          <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center mx-auto shadow-2xs">
            <Trash2 class="w-6 h-6" />
          </div>

          <div class="space-y-1">
            <h3 class="text-base font-bold text-slate-900">
              Hapus Akun Pengguna?
            </h3>
            <p class="text-xs text-slate-500">
              Apakah Anda yakin ingin menghapus akun <strong class="text-slate-800">{{ userToDelete.name }}</strong>?
            </p>
          </div>

          <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-left text-xs space-y-1 text-slate-600">
            <div class="flex justify-between">
              <span class="text-slate-400">Email:</span>
              <span class="font-mono text-slate-800 text-[11px] truncate max-w-[180px]">{{ userToDelete.email }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400">Peran:</span>
              <span class="font-medium text-slate-800">{{ userToDelete.role }}</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2.5 pt-1">
            <button
              type="button"
              class="w-full py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors cursor-pointer"
              @click="userToDelete = null"
              :disabled="processingId === userToDelete.id"
            >
              Batal
            </button>

            <button
              type="button"
              class="w-full py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-1.5"
              @click="confirmDelete"
              :disabled="processingId === userToDelete.id"
            >
              <span v-if="processingId === userToDelete.id" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span v-else>Ya, Hapus</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal Konfirmasi Reset Data -->
    <Teleport to="body">
      <div
        v-if="isResetModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs"
        @click.self="!isResetting && (isResetModalOpen = false)"
      >
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
          <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100">
            <Trash2 class="w-6 h-6" :class="{ 'animate-spin': isResetting }" />
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
              @click="handleResetData"
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
import { ref, computed, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Plus, Search, Trash2, X, AlertCircle, Users, RotateCcw, Eye, EyeOff, Mail, Phone, Calendar, Check } from 'lucide-vue-next';
import { useTaxStore } from '../store/taxStore';

const router = useRouter();
const store = useTaxStore();

const showDemoAccounts = computed(() => store.showDemoAccounts.value);
const isResetting = computed(() => store.isResetting.value);
const isResetModalOpen = ref(false);

async function handleToggleDemoAccounts() {
  await store.setDemoAccountsVisibility(!showDemoAccounts.value);
}

async function handleResetData() {
  const res = await store.resetEntireSystemData();
  if (res && res.success) {
    isResetModalOpen.value = false;
  }
}

onMounted(() => {
  const role = store.currentUser.value?.role || '';
  const isAdmin = role === 'Admin SCM' || role.toLowerCase().includes('admin');
  if (!isAdmin) {
    router.replace('/dashboard');
  }
});

const activeFilter = ref('all');
const searchQuery = ref('');
const processingId = ref(null);
const userToDelete = ref(null);

const isAddModalOpen = ref(false);
const isSubmitting = ref(false);
const addError = ref('');

const addForm = reactive({
  name: '',
  email: '',
  phone: '',
  role: 'Tim Pajak',
  password: ''
});

const allUsers = computed(() => store.allUsers.value);
const pendingUsers = computed(() => store.pendingUsers.value);
const approvedUsers = computed(() => allUsers.value.filter(u => u.status === 'approved'));

const filteredList = computed(() => {
  let list = allUsers.value;

  if (activeFilter.value === 'pending') {
    list = list.filter(u => u.status === 'pending');
  } else if (activeFilter.value === 'approved') {
    list = list.filter(u => u.status === 'approved');
  }

  const query = searchQuery.value.trim().toLowerCase();
  if (query) {
    list = list.filter(u =>
      (u.name && u.name.toLowerCase().includes(query)) ||
      (u.email && u.email.toLowerCase().includes(query)) ||
      (u.role && u.role.toLowerCase().includes(query))
    );
  }

  return list;
});

function getAvatarColorClass(role) {
  if (role === 'Admin SCM') {
    return 'bg-blue-100 text-blue-800';
  } else if (role && role.includes('Pajak')) {
    return 'bg-sky-100 text-sky-800';
  } else {
    return 'bg-emerald-100 text-emerald-800';
  }
}

function getInitials(name) {
  if (!name) return 'U';
  const parts = name.trim().split(/\s+/);
  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

function getRoleBadgeClass(role) {
  if (role === 'Admin SCM') {
    return 'bg-blue-50 text-blue-700 border-blue-200/80';
  } else if (role && role.includes('Pajak')) {
    return 'bg-sky-50 text-sky-700 border-sky-200/80';
  } else {
    return 'bg-indigo-50 text-indigo-700 border-indigo-200/80';
  }
}

function formatDate(dateStr) {
  if (!dateStr || dateStr === '-') return '-';
  try {
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
  } catch (e) {
    return dateStr;
  }
}

async function handleApprove(user) {
  if (processingId.value) return;
  processingId.value = user.id;
  try {
    await store.approveUser(user.id);
  } finally {
    processingId.value = null;
  }
}

async function handleReject(user) {
  if (processingId.value) return;
  processingId.value = user.id;
  try {
    await store.rejectUser(user.id);
  } finally {
    processingId.value = null;
  }
}

function promptDelete(user) {
  if (processingId.value) return;
  userToDelete.value = user;
}

async function confirmDelete() {
  if (!userToDelete.value || processingId.value) return;
  const id = userToDelete.value.id;
  processingId.value = id;
  try {
    await store.deleteUser(id);
    userToDelete.value = null;
  } finally {
    processingId.value = null;
  }
}

function openAddModal() {
  addForm.name = '';
  addForm.email = '';
  addForm.phone = '';
  addForm.role = 'Tim Pajak';
  addForm.password = '';
  addError.value = '';
  isAddModalOpen.value = true;
}

async function submitAddUser() {
  if (isSubmitting.value) return;
  isSubmitting.value = true;
  addError.value = '';

  try {
    const res = await store.createUser({ ...addForm });
    if (res.success) {
      isAddModalOpen.value = false;
    } else {
      addError.value = res.message || 'Gagal menambahkan user.';
    }
  } catch (e) {
    addError.value = 'Terjadi kesalahan sistem.';
  } finally {
    isSubmitting.value = false;
  }
}
</script>
