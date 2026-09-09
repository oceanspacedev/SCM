<template>
  <!-- Blank Full-page Layout (for Login) -->
  <div v-if="isFullPage" class="min-h-screen bg-[#F8FAFC]">
    <router-view />
    <ToastNotification />
  </div>

  <!-- Standard Layout: Full-height Sidebar on left, Content on right -->
  <div v-else class="min-h-screen flex flex-col md:flex-row bg-[#F8FAFC] text-[#0F172A]">
    <!-- Sidebar (Drawer on mobile, docked on desktop) -->
    <Sidebar />

    <!-- Right Column: Main Body (Clean Top) -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
      <!-- Mobile Topbar Navbar (visible only on mobile screens < md) -->
      <header class="md:hidden sticky top-0 z-30 h-14 bg-white border-b border-slate-200 px-3.5 sm:px-4 flex items-center justify-between shadow-2xs shrink-0 select-none">
        <div class="flex items-center gap-2.5">
          <button
            type="button"
            class="p-2 -ml-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 active:bg-slate-200 transition-colors cursor-pointer"
            aria-label="Buka Menu Navigasi"
            @click="store.toggleMobileSidebar"
          >
            <Menu class="w-5 h-5" />
          </button>
          <router-link to="/dashboard" class="flex items-center gap-1.5">
            <span class="text-base font-bold tracking-tight text-slate-900 leading-none">TaxVault</span>
          </router-link>
        </div>

        <div class="flex items-center gap-2">
          <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200/70 text-[10px] font-semibold text-emerald-800">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="truncate max-w-[95px]">{{ userRole }}</span>
          </div>
          <div class="w-7 h-7 rounded-full bg-[#135A46] text-white font-bold flex items-center justify-center text-[10px] shrink-0">
            {{ userInitials }}
          </div>
        </div>
      </header>

      <main class="flex-1 p-3.5 sm:p-5 lg:p-7">
        <div class="w-full max-w-[1600px] mx-auto">
          <router-view v-slot="{ Component }">
            <Transition
              mode="out-in"
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="opacity-0 translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 translate-y-1"
            >
              <component :is="Component" />
            </Transition>
          </router-view>
        </div>
      </main>
    </div>

    <!-- Notification Toast -->
    <ToastNotification />

    <!-- Global Import Excel Modal -->
    <ImportExcelModal />

    <!-- Admin User Approval Modal -->
    <UserApprovalModal />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { Menu } from 'lucide-vue-next';
import Sidebar from './components/layout/Sidebar.vue';
import ToastNotification from './components/ui/ToastNotification.vue';
import ImportExcelModal from './components/programs/ImportExcelModal.vue';
import UserApprovalModal from './components/auth/UserApprovalModal.vue';
import { useTaxStore } from './store/taxStore';

const route = useRoute();
const store = useTaxStore();

const isFullPage = computed(() => route.meta.layout === 'blank');
const userRole = computed(() => store.currentUser.value?.role || 'Admin SCM');
const userInitials = computed(() => store.currentUser.value?.initials || 'BS');
</script>
