<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
  >
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"
      @click="store.closeApprovalModal()"
    ></div>

    <!-- Modal Dialog -->
    <div class="relative bg-white rounded-xl shadow-2xl border border-slate-200/90 w-full max-w-2xl overflow-hidden z-10 flex flex-col max-h-[85vh] animate-in fade-in zoom-in-98 duration-150 font-sans text-slate-800">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-slate-200 bg-white flex items-center justify-between">
        <div>
          <h3 class="text-base font-bold text-slate-900 leading-tight">
            Persetujuan Akun Pengguna (ACC Admin)
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Verifikasi dan berikan izin akses untuk pengajuan akun baru
          </p>
        </div>
        <button
          type="button"
          class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-colors cursor-pointer"
          @click="store.closeApprovalModal()"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Navigation Tabs -->
      <div class="px-6 pt-3 border-b border-slate-200 bg-slate-50/50 flex gap-6 text-xs font-semibold">
        <button
          type="button"
          class="pb-2.5 border-b-2 transition-colors flex items-center gap-2 cursor-pointer"
          :class="activeTab === 'pending' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
          @click="activeTab = 'pending'"
        >
          <span>Menunggu Persetujuan</span>
          <span
            v-if="pendingUsers.length > 0"
            class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900"
          >
            {{ pendingUsers.length }}
          </span>
        </button>

        <button
          type="button"
          class="pb-2.5 border-b-2 transition-colors flex items-center gap-2 cursor-pointer"
          :class="activeTab === 'all' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
          @click="activeTab = 'all'"
        >
          <span>Semua Akun</span>
          <span class="text-slate-400 font-normal">({{ allUsers.length }})</span>
        </button>
      </div>

      <!-- Body Content -->
      <div class="p-6 overflow-y-auto flex-1 text-xs">
        <!-- Tab 1: Menunggu Persetujuan (Pending) -->
        <div v-if="activeTab === 'pending'" class="space-y-3">
          <div v-if="pendingUsers.length === 0" class="py-12 text-center text-slate-400 space-y-2">
            <CheckCircle2 class="w-9 h-9 text-slate-300 mx-auto" />
            <p class="font-semibold text-slate-800 text-sm">Tidak Ada Pengajuan Tertunda</p>
            <p class="text-xs max-w-sm mx-auto text-slate-500">
              Semua pendaftaran akun baru telah diverifikasi dan diproses.
            </p>
          </div>

          <div
            v-for="user in pendingUsers"
            :key="user.id"
            class="p-3.5 rounded-lg border border-slate-200 hover:border-slate-300 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 transition-all shadow-2xs"
          >
            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-bold text-slate-900 text-sm">{{ user.name }}</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                  {{ user.role }}
                </span>
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                  <span>Menunggu ACC</span>
                </span>
              </div>

              <div class="text-slate-500 text-[11px] flex flex-wrap items-center gap-x-3 gap-y-0.5 font-mono">
                <span>{{ user.email }}</span>
                <span>•</span>
                <span>{{ user.phone }}</span>
                <span>•</span>
                <span class="font-sans text-slate-400">{{ user.registered_at }}</span>
              </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
              <button
                type="button"
                :disabled="processingId === user.id"
                class="px-2.5 py-1.5 rounded-md text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 hover:border-rose-600 font-medium text-xs transition-colors cursor-pointer disabled:opacity-50 flex items-center gap-1"
                @click="handleDelete(user)"
                title="Hapus Pengajuan Akun"
              >
                <Trash2 class="w-3.5 h-3.5" />
                <span>Hapus</span>
              </button>

              <button
                type="button"
                :disabled="processingId === user.id"
                class="px-3 py-1.5 rounded-md text-slate-600 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 font-medium text-xs transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                @click="rejectUser(user.id)"
              >
                Tolak
              </button>

              <button
                type="button"
                :disabled="processingId === user.id"
                class="px-3.5 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-xs transition-colors cursor-pointer shadow-2xs flex items-center gap-1.5 disabled:opacity-75 disabled:cursor-wait"
                @click="approveUser(user.id)"
              >
                <span v-if="processingId === user.id" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <Check v-else class="w-3.5 h-3.5" />
                <span>{{ processingId === user.id ? 'Memproses...' : 'Setujui (ACC)' }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Tab 2: Semua Pengguna -->
        <div v-else class="border border-slate-200 rounded-lg overflow-x-auto">
          <table class="w-full text-left text-xs min-w-[500px]">
            <thead class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400">
              <tr>
                <th class="py-2.5 px-3.5">Nama & Kontak</th>
                <th class="py-2.5 px-3.5">Role</th>
                <th class="py-2.5 px-3.5 text-center">Status</th>
                <th class="py-2.5 px-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="user in allUsers" :key="user.id" class="hover:bg-slate-50/70">
                <td class="py-2.5 px-3.5">
                  <div class="font-semibold text-slate-900">{{ user.name }}</div>
                  <div class="text-slate-400 text-[11px] font-mono">{{ user.email }} • {{ user.phone }}</div>
                </td>
                <td class="py-2.5 px-3.5">
                  <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ user.role }}
                  </span>
                </td>
                <td class="py-2.5 px-3.5 text-center">
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                    :class="{
                      'bg-slate-100 text-slate-700 border border-slate-200': user.status === 'approved',
                      'bg-amber-50 text-amber-800 border border-amber-200': user.status === 'pending',
                      'bg-rose-50 text-rose-700 border border-rose-200': user.status === 'rejected'
                    }"
                  >
                    {{ user.status === 'approved' ? 'Disetujui' : user.status === 'pending' ? 'Menunggu' : 'Ditolak' }}
                  </span>
                </td>
                <td class="py-2.5 px-3.5 text-right">
                  <div class="inline-flex items-center gap-1.5" v-if="user.email !== 'admin@scm.corp'">
                    <button
                      v-if="user.status !== 'approved'"
                      type="button"
                      :disabled="processingId === user.id"
                      class="px-2.5 py-1 rounded bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-[10px] font-semibold transition-colors cursor-pointer disabled:opacity-60 flex items-center gap-1"
                      @click="approveUser(user.id)"
                      title="Setujui Akun"
                    >
                      <span v-if="processingId === user.id" class="w-2.5 h-2.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                      <span>{{ processingId === user.id ? '...' : 'ACC' }}</span>
                    </button>
                    <button
                      v-if="user.status !== 'rejected'"
                      type="button"
                      :disabled="processingId === user.id"
                      class="px-2 py-1 rounded text-slate-500 hover:text-rose-600 hover:bg-rose-50 text-[10px] font-medium border border-slate-200 transition-colors cursor-pointer disabled:opacity-50"
                      @click="rejectUser(user.id)"
                      title="Tolak Akun"
                    >
                      Tolak
                    </button>
                    <button
                      type="button"
                      :disabled="processingId === user.id"
                      class="px-2 py-1 rounded text-rose-600 hover:text-white hover:bg-rose-600 text-[10px] font-medium border border-rose-200 hover:border-rose-600 transition-colors cursor-pointer disabled:opacity-50 flex items-center gap-1"
                      @click="handleDelete(user)"
                      title="Hapus Akun Pengguna"
                    >
                      <Trash2 class="w-3 h-3" />
                      <span>Hapus</span>
                    </button>
                  </div>
                  <span v-else class="text-[10px] text-slate-400">Super Admin</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-3 border-t border-slate-200 bg-slate-50/70 flex items-center justify-between">
        <span class="text-slate-500 text-xs font-medium">
          {{ pendingUsers.length }} akun menunggu persetujuan
        </span>
        <button
          type="button"
          class="px-3.5 py-1.5 rounded-md bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
          @click="store.closeApprovalModal()"
        >
          Tutup
        </button>
      </div>
    </div>

    <!-- Custom In-App Confirmation Modal (No native browser alert) -->
    <Teleport to="body">
      <div
        v-if="userToDelete"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-all"
        @click.self="cancelDelete"
      >
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all p-6 text-center space-y-4">
          <!-- Danger Icon -->
          <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center mx-auto shadow-2xs">
            <Trash2 class="w-6 h-6" />
          </div>

          <!-- Header Text -->
          <div class="space-y-1">
            <h3 class="text-base font-bold text-slate-900">
              Hapus Akun Pengguna?
            </h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Apakah Anda yakin ingin menghapus akun <strong class="text-slate-800 font-semibold">{{ userToDelete.name }}</strong>?
            </p>
          </div>

          <!-- User Details Box -->
          <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-left text-xs space-y-1.5 text-slate-600">
            <div class="flex justify-between items-center">
              <span class="text-slate-400 text-[11px]">Email:</span>
              <span class="font-mono text-slate-900 font-medium">{{ userToDelete.email }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-slate-400 text-[11px]">Role:</span>
              <span class="font-medium text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded text-[10px] border border-emerald-200">{{ userToDelete.role }}</span>
            </div>
            <div class="flex justify-between items-center" v-if="userToDelete.phone">
              <span class="text-slate-400 text-[11px]">WhatsApp:</span>
              <span class="font-mono text-slate-900">{{ userToDelete.phone }}</span>
            </div>
          </div>

          <p class="text-[11px] text-rose-700 bg-rose-50/80 border border-rose-200 rounded-lg p-2 leading-relaxed">
            Data akun ini akan dihapus secara permanen dari sistem dan tidak dapat dipulihkan.
          </p>

          <!-- Actions -->
          <div class="grid grid-cols-2 gap-2.5 pt-1">
            <button
              type="button"
              class="w-full py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors cursor-pointer"
              @click="cancelDelete"
              :disabled="processingId === userToDelete.id"
            >
              Batal
            </button>

            <button
              type="button"
              class="w-full py-2 rounded-lg bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-semibold text-xs transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-1.5 disabled:opacity-75"
              @click="executeDelete"
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
import { ref, computed } from 'vue';
import { X, Check, CheckCircle2, Trash2 } from 'lucide-vue-next';
import { useTaxStore } from '../../store/taxStore';

const store = useTaxStore();

const isOpen = computed(() => store.isApprovalModalOpen.value);
const activeTab = ref('pending');
const processingId = ref(null);
const userToDelete = ref(null);

const allUsers = computed(() => store.allUsers.value);
const pendingUsers = computed(() => store.pendingUsers.value);

async function approveUser(id) {
  if (processingId.value) return;
  processingId.value = id;
  try {
    await store.approveUser(id);
  } finally {
    processingId.value = null;
  }
}

async function rejectUser(id) {
  if (processingId.value) return;
  processingId.value = id;
  try {
    await store.rejectUser(id);
  } finally {
    processingId.value = null;
  }
}

function handleDelete(user) {
  if (processingId.value) return;
  userToDelete.value = user;
}

function cancelDelete() {
  if (processingId.value) return;
  userToDelete.value = null;
}

async function executeDelete() {
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
</script>
