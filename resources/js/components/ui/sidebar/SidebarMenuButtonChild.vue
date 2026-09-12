<script setup lang="ts">
import { Primitive } from "reka-ui"
import { cn } from "@/lib/utils"
import { sidebarMenuButtonVariants } from "."

export interface SidebarMenuButtonProps {
  variant?: "default" | "outline"
  size?: "default" | "sm" | "lg"
  isActive?: boolean
  class?: any
  as?: any
  asChild?: boolean
}

const props = withDefaults(defineProps<SidebarMenuButtonProps>(), {
  as: "button",
  variant: "default",
  size: "default",
})
</script>

<template>
  <Primitive
    data-sidebar="menu-button"
    :data-size="size"
    :data-active="isActive"
    :class="cn(sidebarMenuButtonVariants({ variant, size }), props.class)"
    :as="as"
    :as-child="asChild"
    v-bind="$attrs"
  >
    <slot />
  </Primitive>
</template>
