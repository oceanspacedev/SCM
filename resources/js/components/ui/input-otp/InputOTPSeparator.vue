<script setup>
import { Minus } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: [String, Object, Array], default: undefined },
})
</script>

<template>
  <div role="separator" :class="cn('flex items-center text-slate-400 px-1', props.class)">
    <Minus class="w-4 h-4 stroke-[2.5]" />
  </div>
</template>
