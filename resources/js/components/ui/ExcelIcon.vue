<template>
  <svg
    :class="className || 'w-4 h-4 shrink-0'"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <!-- Background Sheet / Table -->
    <path
      d="M7 2.5H19C20.1046 2.5 21 3.39543 21 4.5V19.5C21 20.6046 20.1046 21.5 19 21.5H7"
      fill="#185C37"
    />
    <!-- Table Grid Lines -->
    <path
      d="M11.5 2.5V21.5M16.5 2.5V21.5M7 7.25H21M7 12H21M7 16.75H21"
      stroke="#33C481"
      stroke-width="0.8"
      opacity="0.5"
    />
    <!-- Front Tile -->
    <rect
      x="2.5"
      y="5.5"
      width="11"
      height="13"
      rx="1.75"
      fill="#107C41"
    />
    <!-- Excel "X" Letter -->
    <path
      d="M5.5 8.75L10.5 15.25M10.5 8.75L5.5 15.25"
      stroke="white"
      stroke-width="1.8"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>

<script setup>
defineProps({
  className: {
    type: String,
    default: 'w-4 h-4 shrink-0'
  }
});
</script>
