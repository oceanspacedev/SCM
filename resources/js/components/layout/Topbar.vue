<template>
  <header class="h-16 border-b border-slate-200 bg-white px-6 sm:px-8 lg:px-10 flex items-center justify-between sticky top-0 z-20 select-none">
    <!-- Left: Clean Breadcrumb Indicator -->
    <div class="flex items-center gap-2.5">
      <span class="text-[11px] font-mono font-bold tracking-wider text-slate-400 uppercase">
        SCM TAXVAULT
      </span>
      <span class="text-slate-300 text-sm">/</span>
      <span class="text-sm font-bold text-slate-900 font-sans tracking-tight">
        {{ currentBreadcrumb }}
      </span>
    </div>

    <!-- Right: Status Badge, Notification, and Dedicated User Menu -->
    <div class="flex items-center gap-4 sm:gap-4.5">
      <!-- Status Badge -->
      <div class="hidden sm:inline-flex items-center gap-2 h-9 px-3.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-700">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="font-medium font-sans">{{ userRole }}</span>
      </div>

      <!-- Notification Bell with Red Badge -->
      <div class="relative" ref="notifRef">
        <button
          type="button"
          class="w-9 h-9 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors cursor-pointer relative"
          title="Notifikasi"
          @click="showNotifications = !showNotifications"
        >
          <Bell class="w-4 h-4" />
          <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- Notification Popover -->
        <div
          v-if="showNotifications"
          class="absolute right-0 mt-2 w-80 bg-white rounded-xl border border-slate-200 shadow-xl py-2 z-50 text-xs"
        >
          <div class="px-4 py-2.5 border-b border-slate-100 flex items-center justify-between">
            <span class="font-bold text-slate-900">Pemberitahuan Audit Pajak</span>
            <span class="text-[10px] text-emerald-700 font-semibold cursor-pointer" @click="showNotifications = false">Tandai Dibaca</span>
          </div>
          <div class="divide-y divide-slate-100">
            <div class="px-4 py-3 hover:bg-slate-50 cursor-pointer">
              <div class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5 shrink-0"></span>
                <div>
                  <p class="font-semibold text-slate-900">7 Program memerlukan Faktur Pajak</p>
                  <p class="text-slate-500 text-[11px] mt-0.5">Segera lengkapi sebelum batas waktu pelaporan SPT Masa.</p>
                  <span class="text-[10px] text-slate-400 mt-1 block">10 menit yang lalu</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Separator -->
      <div class="h-5 w-px bg-slate-200"></div>

      <!-- User Profile Menu with Person Icon -->
      <div class="relative" ref="userMenuRef">
        <button
          type="button"
          class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200/80 border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-all cursor-pointer shadow-2xs active:scale-95 focus:outline-hidden"
          title="Menu Pengguna"
          @click="showUserMenu = !showUserMenu"
        >
          <User class="w-4.5 h-4.5" />
        </button>

        <!-- User Dropdown Popover -->
        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <div
            v-if="showUserMenu"
            class="absolute right-0 mt-2 w-56 bg-white rounded-xl border border-slate-200 shadow-xl py-1.5 z-50 text-xs"
          >
            <!-- User Info Header -->
            <div class="px-3.5 py-2.5 border-b border-slate-100">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0">
                  {{ userInitials }}
                </div>
                <div class="overflow-hidden">
                  <p class="font-bold text-slate-900 truncate leading-tight">{{ userName }}</p>
                  <p class="text-[11px] text-slate-400 truncate leading-tight mt-0.5">{{ userEmail }}</p>
                </div>
              </div>
              <div class="mt-2.5 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-medium text-slate-600">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>{{ userRole }}</span>
              </div>
            </div>

            <!-- Menu Actions -->
            <div class="p-1 space-y-0.5">
              <button
                v-if="isAdmin"
                type="button"
                class="w-full text-left px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-lg flex items-center justify-between cursor-pointer transition-colors"
                @click="openApprovalModal"
              >
                <div class="flex items-center gap-2">
                  <UserCheck class="w-3.5 h-3.5 text-slate-500" />
                  <span>Persetujuan Akun</span>
                </div>
                <span
                  v-if="pendingCount > 0"
                  class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800"
                >
                  {{ pendingCount }}
                </span>
              </button>

              <button
                type="button"
                class="w-full text-left px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg flex items-center gap-2 cursor-pointer transition-colors"
                @click="handleLogout"
              >
                <LogOut class="w-3.5 h-3.5 text-rose-600" />
                <span>Keluar / Logout</span>
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Bell, LogOut, User, UserCheck } from 'lucide-vue-next';
import { useTaxStore } from '../../store/taxStore';

const route = useRoute();
const router = useRouter();
const store = useTaxStore();

const showNotifications = ref(false);
const showUserMenu = ref(false);
const userMenuRef = ref(null);
const notifRef = ref(null);

const userName = computed(() => store.currentUser.value?.name || 'Siti Rahmawati');
const userRole = computed(() => store.currentUser.value?.role || 'Tim Pajak');
const userEmail = computed(() => store.currentUser.value?.email || 'auditor@pajak.corp');
const userInitials = computed(() => store.currentUser.value?.initials || 'SR');
const isAdmin = computed(() => {
  const role = store.currentUser.value?.role;
  return role === 'Admin SCM' || (role && role.toLowerCase().includes('admin'));
});
const pendingCount = computed(() => store.pendingUsersCount.value);

function openApprovalModal() {
  showUserMenu.value = false;
  router.push('/users');
}

const currentBreadcrumb = computed(() => {
  if (route.name === 'programs') return 'Arsip Program';
  if (route.name === 'program-detail') return 'Arsip Program / Detail';
  if (route.name === 'users') return 'Manajemen User';
  if (route.name === 'settings') return 'Pengaturan';
  return 'Dashboard';
});

function handleLogout() {
  showUserMenu.value = false;
  store.logout();
  router.push('/login');
}

function handleClickOutside(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    showUserMenu.value = false;
  }
  if (notifRef.value && !notifRef.value.contains(e.target)) {
    showNotifications.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
