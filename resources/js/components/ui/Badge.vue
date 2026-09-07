<template>
  <span
    :class="[
      'inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium border tracking-tight',
      variantClasses[variant] || variantClasses.neutral,
      className
    ]"
  >
    <span v-if="dot" :class="['w-1.5 h-1.5 rounded-full', dotClasses[variant] || dotClasses.neutral]"></span>
    <slot />
  </span>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'neutral' // success, warning, danger, neutral, primary
  },
  dot: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
});

const variantClasses = {
  success: 'bg-[#F0FDF4] text-[#15803D] border-[#BBF7D0]',
  warning: 'bg-[#FFFBEB] text-[#B45309] border-[#FDE68A]',
  danger: 'bg-[#FEF2F2] text-[#B91C1C] border-[#FECACA]',
  neutral: 'bg-[#F4F6F5] text-[#66736F] border-[#DDE4E1]',
  primary: 'bg-[#F0FDFA] text-[#0F766E] border-[#CCFBF1]',
};

const dotClasses = {
  success: 'bg-[#15803D]',
  warning: 'bg-[#B45309]',
  danger: 'bg-[#B91C1C]',
  neutral: 'bg-[#66736F]',
  primary: 'bg-[#0F766E]',
};
</script>
