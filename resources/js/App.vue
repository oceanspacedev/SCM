<template>
  <!-- Blank Full-page Layout (for Login) -->
  <div v-if="isFullPage" class="min-h-screen bg-[#F8FAFC]">
    <router-view />
    <ToastNotification />
  </div>

  <!-- Standard Layout: Full-height Dark Sidebar on left, Content on right -->
  <div v-else class="min-h-screen flex flex-row bg-[#F8FAFC] text-[#0F172A]">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Right Column: Main Body (Clean Top) -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
      <main class="flex-1 p-5 sm:p-6 lg:p-7">
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
import Sidebar from './components/layout/Sidebar.vue';
import ToastNotification from './components/ui/ToastNotification.vue';
import ImportExcelModal from './components/programs/ImportExcelModal.vue';
import UserApprovalModal from './components/auth/UserApprovalModal.vue';

const route = useRoute();
const isFullPage = computed(() => route.meta.layout === 'blank');
</script>
