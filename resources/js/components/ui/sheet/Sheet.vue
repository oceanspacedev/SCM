<script setup lang="ts">
import { DialogRoot, useForwardPropsEmits } from "reka-ui"

const props = defineProps<{
  open?: boolean
  defaultOpen?: boolean
  modal?: boolean
}>()
const emits = defineEmits<{
  (e: 'update:open', value: boolean): void
}>()

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <DialogRoot v-bind="forwarded">
    <slot />
  </DialogRoot>
</template>
