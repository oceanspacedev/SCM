<template>
  <Teleport to="body">
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm pointer-events-none">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
      >
        <div
          v-if="notification"
          :class="[
            'pointer-events-auto flex items-start gap-3 p-3.5 bg-white rounded-lg border shadow-lg text-sm transition-all',
            borderClass
          ]"
        >
          <div :class="['p-1 rounded-full shrink-0', iconBgClass]">
            <Check v-if="notification.type === 'success'" class="w-3.5 h-3.5 text-[#15803D]" />
            <AlertTriangle v-else-if="notification.type === 'warning'" class="w-3.5 h-3.5 text-[#B45309]" />
            <Info v-else class="w-3.5 h-3.5 text-[#0F766E]" />
          </div>

          <div class="flex-1 pr-2">
            <p class="font-medium text-[#17201E] leading-snug">
              {{ notification.message }}
            </p>
          </div>

          <button
            type="button"
            class="text-[#66736F] hover:text-[#17201E] p-0.5 rounded cursor-pointer"
            @click="dismiss"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>
      </Transition>
    </div>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { Check, AlertTriangle, Info, X } from 'lucide-vue-next';
import { useTaxStore } from '../../store/taxStore';

const store = useTaxStore();
const notification = computed(() => store.state.activeNotification);

const borderClass = computed(() => {
  if (!notification.value) return 'border-[#DDE4E1]';
  if (notification.value.type === 'success') return 'border-[#BBF7D0]';
  if (notification.value.type === 'warning') return 'border-[#FDE68A]';
  return 'border-[#CCFBF1]';
});

const iconBgClass = computed(() => {
  if (!notification.value) return 'bg-[#F4F6F5]';
  if (notification.value.type === 'success') return 'bg-[#F0FDF4]';
  if (notification.value.type === 'warning') return 'bg-[#FFFBEB]';
  return 'bg-[#F0FDFA]';
});

function dismiss() {
  store.state.activeNotification = null;
}
</script>
