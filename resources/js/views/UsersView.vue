<template>
  <div class="space-y-6">
    <!-- Page Header (Matches screenshot) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
          Manajemen User
        </h1>
        <p class="text-xs text-slate-500 mt-1">
          Kelola akses personel divisi SCM dan Tim Pajak
        </p>
      </div>

      <!-- Action: + Tambah User Button -->
      <div class="flex items-center gap-3">
        <button
          type="button"
          class="h-9 px-4 rounded-lg bg-[#135A46] hover:bg-[#0F4939] active:bg-[#0C3B2E] text-white font-semibold text-xs transition-all cursor-pointer flex items-center gap-1.5 shadow-sm shadow-[#135A46]/20"
          @click="openAddModal"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah User</span>
        </button>
      </div>
    </div>

    <!-- Filter Tabs & Stats -->
    <div class="flex items-center justify-between border-b border-slate-200 text-xs">
      <div class="flex gap-6">
        <button
          type="button"
          class="pb-3 border-b-2 font-semibold transition-all cursor-pointer flex items-center gap-2"
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
          class="pb-3 border-b-2 font-semibold transition-all cursor-pointer flex items-center gap-2"
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
        </button>

        <button
          type="button"
          class="pb-3 border-b-2 font-semibold transition-all cursor-pointer flex items-center gap-2"
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
      <div class="relative w-64 pb-2">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama atau email..."
          class="w-full h-9 pl-9 pr-3 text-xs rounded-lg border border-slate-200 focus:border-[#135A46] focus:ring-1 focus:ring-[#135A46] focus:outline-hidden transition-all placeholder:text-slate-400 bg-white"
        />
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" />
      </div>
    </div>

    <!-- Users Table (Matches screenshot layout) -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
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
                {{ user.name }}
              </td>

              <!-- EMAIL -->
              <td class="py-3.5 px-5 font-mono text-slate-600">
                {{ user.email }}
              </td>

              <!-- PERAN -->
              <td class="py-3.5 px-5">
                <span
                  class="px-2.5 py-1 rounded-full text-[11px] font-medium border"
                  :class="getRoleBadgeClass(user.role)"
                >
                  {{ user.role }}
                </span>
              </td>

              <!-- DIBUAT -->
              <td class="py-3.5 px-5 text-slate-500">
                {{ formatDate(user.registered_at) }}
              </td>

              <!-- STATUS -->
              <td class="py-3.5 px-5">
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

              <!-- AKSI (Matches screenshot buttons) -->
              <td class="py-3.5 px-5 text-right">
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

            <!-- Empty State -->
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

    <!-- Modal: + Tambah User Baru -->
    <Teleport to="body">
      <div
        v-if="isAddModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
        @click.self="isAddModalOpen = false"
      >
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
          <!-- Modal Header -->
          <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-white">
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
          <form @submit.prevent="submitAddUser" class="p-6 space-y-3.5">
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
            <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
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
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
        @click.self="userToDelete = null"
      >
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 text-center space-y-4">
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
              <span class="font-mono text-slate-800">{{ userToDelete.email }}</span>
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
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import { Plus, Search, Trash2, X, AlertCircle, Users } from 'lucide-vue-next';
import { useTaxStore } from '../store/taxStore';

const store = useTaxStore();

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

function getRoleBadgeClass(role) {
  if (role === 'Admin SCM') {
    return 'bg-blue-50 text-blue-700 border-blue-200/80';
  } else if (role.includes('Pajak')) {
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
