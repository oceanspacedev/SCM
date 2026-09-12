<script setup lang="ts">
import { TooltipTrigger } from "reka-ui"

const props = defineProps<{
  as?: any
  asChild?: boolean
}>()
</script>

<template>
  <TooltipTrigger v-bind="props">
    <slot />
  </TooltipTrigger>
</template>
