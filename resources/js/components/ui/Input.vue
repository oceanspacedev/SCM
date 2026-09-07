<template>
  <div class="relative w-full">
    <div
      v-if="$slots.prefix"
      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#66736F]"
    >
      <slot name="prefix" />
    </div>
    <input
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      :required="required"
      :class="[
        'w-full bg-white text-[#17201E] text-sm rounded-md border border-[#DDE4E1] shadow-2xs placeholder:text-[#94A3B8]',
        'focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition-all',
        'disabled:bg-[#F7F8F7] disabled:text-[#94A3B8] disabled:cursor-not-allowed',
        $slots.prefix ? 'pl-9' : 'pl-3',
        $slots.suffix ? 'pr-9' : 'pr-3',
        size === 'sm' ? 'h-8 text-xs' : 'h-9 text-sm',
        className
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
      @change="$emit('change', $event)"
      @keydown.enter="$emit('enter', $event)"
    />
    <div
      v-if="$slots.suffix"
      class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#66736F]"
    >
      <slot name="suffix" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
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

defineEmits(['update:modelValue', 'change', 'enter']);
</script>
