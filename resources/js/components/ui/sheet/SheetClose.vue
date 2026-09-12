<script setup lang="ts">
import { DialogClose } from "reka-ui"

const props = defineProps<{
  as?: any
  asChild?: boolean
}>()
</script>

<template>
  <DialogClose v-bind="props">
    <slot />
  </DialogClose>
</template>
