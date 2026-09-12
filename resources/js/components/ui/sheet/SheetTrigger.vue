<script setup lang="ts">
import { DialogTrigger } from "reka-ui"

const props = defineProps<{
  as?: any
  asChild?: boolean
}>()
</script>

<template>
  <DialogTrigger v-bind="props">
    <slot />
  </DialogTrigger>
</template>
