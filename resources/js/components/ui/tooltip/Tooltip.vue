<script setup lang="ts">
import { TooltipRoot, useForwardPropsEmits } from "reka-ui"

const props = defineProps<{
  defaultOpen?: boolean
  open?: boolean
  delayDuration?: number
  disableHoverableContent?: boolean
  disableClosingTrigger?: boolean
  disabled?: boolean
  ignoreNonKeyboardFocus?: boolean
}>()
const emits = defineEmits<{
  (e: 'update:open', value: boolean): void
}>()

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <TooltipRoot v-bind="forwarded">
    <slot />
  </TooltipRoot>
</template>
