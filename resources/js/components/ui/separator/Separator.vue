<script setup>
import { Separator } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps({
  orientation: {
    type: String,
    default: "horizontal"
  },
  decorative: {
    type: Boolean,
    default: true
  },
  class: {
    type: [String, Object, Array],
    default: ""
  },
  as: {
    type: [String, Object],
    default: "div"
  }
})
</script>

<template>
  <Separator
    :orientation="orientation"
    :decorative="decorative"
    :as="as"
    :class="
      cn(
        'shrink-0 bg-border',
        orientation === 'horizontal' ? 'h-px w-full' : 'w-px h-full',
        props.class
      )
    "
  />
</template>
