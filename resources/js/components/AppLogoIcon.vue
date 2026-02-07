<script setup lang="ts">
  import type { HTMLAttributes } from "vue";

  defineOptions({
    inheritAttrs: false,
  });

  type Props = {
    className?: HTMLAttributes["class"];
  };

  const props = defineProps<Props>();
</script>

<template>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 91 91">
    <path :class="props.className" fill-rule="evenodd" d="M47 91V71h44v20H47Zm24-44h20v19H71V47Zm0-23h20v19H71V24ZM47 0h44v19H47V0ZM0 0h43v19H0V0Zm19 43H0V24h19v19Zm0 23H0V47h19v19Zm5-42h42v42H24V24Zm19 67H0V71h43v20Z" />
  </svg>
</template>
