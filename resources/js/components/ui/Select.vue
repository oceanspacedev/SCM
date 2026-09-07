<template>
  <div class="relative w-full">
    <select
      :value="modelValue"
      :disabled="disabled"
      :required="required"
      :class="[
        'w-full bg-white text-[#17201E] text-sm rounded-md border border-[#DDE4E1] shadow-2xs appearance-none pl-3 pr-8 cursor-pointer',
        'focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition-all',
        'disabled:bg-[#F7F8F7] disabled:text-[#94A3B8] disabled:cursor-not-allowed',
        size === 'sm' ? 'h-8 text-xs' : 'h-9 text-sm',
        className
      ]"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <slot />
    </select>
    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[#66736F]">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'default' // sm, default
  },
  className: {
    type: String,
    default: ''
  }
});

defineEmits(['update:modelValue']);
</script>
