<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: [String, Object, Array], default: undefined },
})
</script>

<template>
  <div :class="cn('flex items-center', props.class)">
    <slot />
  </div>
</template>
