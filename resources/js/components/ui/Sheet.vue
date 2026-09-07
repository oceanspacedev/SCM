<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="open"
        class="fixed inset-0 z-50 bg-[#17201E]/40 backdrop-blur-[2px] flex justify-end"
        @click.self="handleBackdropClick"
      >
        <Transition
          enter-active-class="transition transform duration-250 ease-out"
          enter-from-class="translate-x-full"
          enter-to-class="translate-x-0"
          leave-active-class="transition transform duration-200 ease-in"
          leave-from-class="translate-x-0"
          leave-to-class="translate-x-full"
        >
          <div
            :class="[
              'w-full bg-white h-full border-l border-[#DDE4E1] shadow-2xl flex flex-col',
              widthClass
            ]"
          >
            <!-- Header -->
            <div class="px-6 py-4 border-b border-[#DDE4E1] flex items-center justify-between bg-white shrink-0">
              <div>
                <h3 class="text-base font-semibold text-[#17201E]">
                  {{ title }}
                </h3>
                <p v-if="description" class="text-xs text-[#66736F] mt-0.5">
                  {{ description }}
                </p>
              </div>
              <div class="flex items-center gap-2">
                <slot name="headerActions" />
                <button
                  type="button"
                  class="text-[#66736F] hover:text-[#17201E] p-1.5 rounded-md hover:bg-[#F4F6F5] transition-colors cursor-pointer"
                  @click="close"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-5 overflow-y-auto flex-1">
              <slot />
            </div>

            <!-- Footer -->
            <div
              v-if="$slots.footer"
              class="px-6 py-3.5 border-t border-[#DDE4E1] bg-[#FAFBFA] flex items-center justify-end gap-2.5 shrink-0"
            >
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  open: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  width: {
    type: String,
    default: 'max-w-md' // max-w-sm, max-w-md, max-w-lg, max-w-xl, max-w-2xl, max-w-3xl, max-w-4xl
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:open', 'close']);

const widthClass = computed(() => props.width);

function close() {
  emit('update:open', false);
  emit('close');
}

function handleBackdropClick() {
  if (props.closeOnBackdrop) {
    close();
  }
}

function handleKeydown(e) {
  if (e.key === 'Escape' && props.open) {
    close();
  }
}

watch(() => props.open, (val) => {
  if (val) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
  document.body.style.overflow = '';
});
</script>
