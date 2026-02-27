<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { AvatarRoot } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { avatarVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <AvatarRoot data-slot="avatar" :class="cn(avatarVariants(), props.class)">
    <slot />
  </AvatarRoot>
</template>
