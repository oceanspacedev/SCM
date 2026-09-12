<script setup>
import { OTPInput } from 'vue-input-otp'
import { cn } from '@/lib/utils'

const props = defineProps({
  modelValue: { type: String, default: '' },
  maxlength: { type: Number, default: 6 },
  containerClass: { type: String, default: undefined },
  class: { type: [String, Object, Array], default: undefined },
  disabled: { type: Boolean, default: false },
})

const emits = defineEmits(['update:modelValue', 'complete', 'change'])
</script>

<template>
  <OTPInput
    :model-value="props.modelValue"
    :maxlength="props.maxlength"
    :container-class="cn('flex items-center gap-2 has-disabled:opacity-50 justify-center', props.containerClass)"
    :class="cn('disabled:cursor-not-allowed', props.class)"
    :disabled="props.disabled"
    v-bind="$attrs"
    @update:model-value="emits('update:modelValue', $event)"
    @complete="emits('complete', $event)"
    @change="emits('change', $event)"
  >
    <template #default="slotProps">
      <slot v-bind="slotProps" />
    </template>
  </OTPInput>
</template>
