<template>
  <!-- Mobile Backdrop Overlay -->
  <Teleport to="body">
    <div
      v-if="isMobileOpen"
      class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs md:hidden animate-in fade-in duration-150"
      @click="closeMobile"
    />
  </Teleport>

  <!-- Sidebar Container -->
  <aside
    :class="[
      'bg-[#F8FAFA] border-r border-slate-200 flex flex-col shrink-0 select-none text-slate-700 transition-all duration-200',
      // Desktop styling
      'md:sticky md:top-0 md:h-screen md:z-20 md:translate-x-0',
      isCollapsed ? 'md:w-16' : 'md:w-56',
      // Mobile styling (off-canvas drawer)
      'fixed inset-y-0 left-0 z-50 w-64 max-w-[85vw] h-full shadow-2xl md:shadow-none',
      isMobileOpen ? 'translate-x-0' : '-translate-x-full'
    ]"
  >
    <!-- Brand Header -->
    <div
      :class="[
        'border-b border-slate-200 bg-white flex items-center transition-all',
        isCollapsed ? 'md:h-16 md:justify-center px-4 md:px-2 py-3.5 md:py-0' : 'py-3.5 justify-between px-4'
      ]"
    >
      <div v-if="!isCollapsed || isMobileOpen" class="min-w-0 flex-1 pr-2">
        <router-link
          to="/dashboard"
          class="block overflow-hidden"
          @click="closeMobile"
        >
          <span class="text-base font-bold tracking-tight text-slate-900 block truncate leading-none">TaxVault</span>
          <p class="text-[10px] text-slate-400 font-medium leading-tight mt-1 truncate">
            Arsip Dokumen Pajak
          </p>
        </router-link>

        <!-- User Role Badge under title -->
        <div class="mt-2 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200/70 text-[10px] font-semibold text-emerald-800 select-none">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0 animate-pulse"></span>
          <span class="truncate">{{ userRole }}</span>
        </div>
      </div>

      <!-- Desktop Toggle Button -->
      <button
        type="button"
        class="hidden md:inline-flex p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer shrink-0 self-start mt-0.5"
        :title="isCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'"
        @click="toggleSidebar"
      >
        <component :is="isCollapsed ? PanelLeftOpen : PanelLeftClose" class="w-4 h-4" />
      </button>

      <!-- Mobile Close Button (X) -->
      <button
        type="button"
        class="md:hidden p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer shrink-0 self-start mt-0.5"
        title="Tutup Menu"
        @click="closeMobile"
      >
        <X class="w-4 h-4" />
      </button>
    </div>

    <!-- Navigation List -->
    <div class="px-2 py-4 flex-1 overflow-y-auto space-y-6">
      <!-- Main Nav -->
      <nav class="space-y-1">
        <!-- Dashboard Link -->
        <router-link
          to="/dashboard"
          v-slot="{ isActive }"
          @click="closeMobile"
        >
          <div
            :class="[
              'h-10 rounded-xl text-xs transition-all cursor-pointer font-medium flex items-center',
              isCollapsed ? 'md:justify-center md:px-0 justify-between px-3' : 'justify-between px-3',
              isActive
                ? 'bg-[#135A46] text-white shadow-sm font-semibold'
                : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900'
            ]"
            :title="isCollapsed ? 'Dashboard' : ''"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <LayoutDashboard class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-500'" />
              <span v-if="!isCollapsed || isMobileOpen" class="truncate">Dashboard</span>
            </div>
            <ChevronRight v-if="!isCollapsed || isMobileOpen" class="w-3.5 h-3.5 shrink-0" :class="isActive ? 'text-white' : 'text-slate-400'" />
          </div>
        </router-link>

        <!-- Arsip Program Link -->
        <router-link
          to="/programs"
          v-slot="{ isActive }"
          @click="closeMobile"
        >
          <div
            :class="[
              'h-10 rounded-xl text-xs transition-all cursor-pointer font-medium flex items-center',
              isCollapsed ? 'md:justify-center md:px-0 justify-between px-3' : 'justify-between px-3',
              isActive
                ? 'bg-[#135A46] text-white shadow-sm font-semibold'
                : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900'
            ]"
            :title="isCollapsed ? 'Arsip Program' : ''"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <FolderArchive class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-500'" />
              <span v-if="!isCollapsed || isMobileOpen" class="truncate">Arsip Program</span>
            </div>
            <ChevronRight v-if="!isCollapsed || isMobileOpen" class="w-3.5 h-3.5 shrink-0" :class="isActive ? 'text-white' : 'text-slate-400'" />
          </div>
        </router-link>

        <!-- Manajemen User Link (Khusus Admin SCM) -->
        <router-link
          v-if="isAdmin"
          to="/users"
          v-slot="{ isActive }"
          @click="closeMobile"
        >
          <div
            :class="[
              'h-10 rounded-xl text-xs transition-all cursor-pointer font-medium flex items-center',
              isCollapsed ? 'md:justify-center md:px-0 relative justify-between px-3' : 'justify-between px-3',
              isActive
                ? 'bg-[#135A46] text-white shadow-sm font-semibold'
                : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900'
            ]"
            :title="isCollapsed ? 'Manajemen User' : ''"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <Users class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-500'" />
              <span v-if="!isCollapsed || isMobileOpen" class="truncate">Manajemen User</span>
            </div>
            <div v-if="!isCollapsed || isMobileOpen" class="flex items-center gap-1.5">
              <span
                v-if="pendingCount > 0"
                class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
                :class="isActive ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900'"
              >
                {{ pendingCount }}
              </span>
              <ChevronRight class="w-3.5 h-3.5 shrink-0" :class="isActive ? 'text-white' : 'text-slate-400'" />
            </div>
            <span
              v-else-if="pendingCount > 0"
              class="absolute top-2 right-2 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"
            ></span>
          </div>
        </router-link>

        <!-- Pengaturan Link (Khusus Admin SCM) -->
        <router-link
          v-if="isAdmin"
          to="/settings"
          v-slot="{ isActive }"
          @click="closeMobile"
        >
          <div
            :class="[
              'h-10 rounded-xl text-xs transition-all cursor-pointer font-medium flex items-center',
              isCollapsed ? 'md:justify-center md:px-0 justify-between px-3' : 'justify-between px-3',
              isActive
                ? 'bg-[#135A46] text-white shadow-sm font-semibold'
                : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900'
            ]"
            :title="isCollapsed ? 'Pengaturan' : ''"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <Settings class="w-4 h-4 shrink-0" :class="isActive ? 'text-white' : 'text-slate-500'" />
              <span v-if="!isCollapsed || isMobileOpen" class="truncate">Pengaturan</span>
            </div>
            <ChevronRight v-if="!isCollapsed || isMobileOpen" class="w-3.5 h-3.5 shrink-0" :class="isActive ? 'text-white' : 'text-slate-400'" />
          </div>
        </router-link>

        <!-- Separator -->
        <div class="pt-2 my-1 border-t border-slate-200/70"></div>

        <!-- Logout Button -->
        <button
          type="button"
          @click="handleLogout"
          :class="[
            'w-full h-10 rounded-xl text-xs transition-all cursor-pointer font-medium flex items-center text-slate-600 hover:bg-slate-200/60 hover:text-slate-900 select-none',
            isCollapsed ? 'md:justify-center md:px-0 justify-between px-3' : 'justify-between px-3'
          ]"
          :title="isCollapsed ? 'Keluar' : ''"
        >
          <div class="flex items-center gap-2.5 min-w-0">
            <Power class="w-4 h-4 shrink-0 text-slate-500" />
            <span v-if="!isCollapsed || isMobileOpen" class="truncate">Keluar</span>
          </div>
          <ChevronRight v-if="!isCollapsed || isMobileOpen" class="w-3.5 h-3.5 shrink-0 text-slate-400" />
        </button>
      </nav>
    </div>

    <!-- Desktop Bottom Collapse Toggle -->
    <div class="hidden md:block p-2 border-t border-slate-200/70">
      <button
        type="button"
        class="w-full h-9 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200/60 flex items-center justify-center gap-2 text-xs transition-colors cursor-pointer"
        :title="isCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'"
        @click="toggleSidebar"
      >
        <component :is="isCollapsed ? PanelLeftOpen : PanelLeftClose" class="w-4 h-4" />
        <span v-if="!isCollapsed" class="truncate">Ciutkan Menu</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  LayoutDashboard,
  FolderArchive,
  ChevronRight,
  Users,
  Settings,
  PanelLeftClose,
  PanelLeftOpen,
  Power,
  X
} from 'lucide-vue-next';
import { useTaxStore } from '../../store/taxStore';

const router = useRouter();
const store = useTaxStore();

const isCollapsed = ref(localStorage.getItem('scm_sidebar_collapsed') === 'true');
const isMobileOpen = computed(() => store.isMobileSidebarOpen.value);

function toggleSidebar() {
  isCollapsed.value = !isCollapsed.value;
  localStorage.setItem('scm_sidebar_collapsed', isCollapsed.value ? 'true' : 'false');
}

function closeMobile() {
  store.closeMobileSidebar();
}

function handleLogout() {
  closeMobile();
  store.logout();
  router.push('/login');
}

const isAdmin = computed(() => {
  const role = store.currentUser.value?.role;
  return role === 'Admin SCM' || (role && role.toLowerCase().includes('admin'));
});
const pendingCount = computed(() => store.pendingUsersCount.value);
const userRole = computed(() => store.currentUser.value?.role || 'Admin SCM');
</script>
