<script setup lang="ts">
import { TooltipProvider } from "reka-ui"

const props = defineProps<{
  delayDuration?: number
  skipDelayDuration?: number
  disableHoverableContent?: boolean
  disableClosingTrigger?: boolean
  disabled?: boolean
  ignoreNonKeyboardFocus?: boolean
}>()
</script>

<template>
  <TooltipProvider v-bind="props">
    <slot />
  </TooltipProvider>
</template>
