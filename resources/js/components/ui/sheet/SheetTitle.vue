<script setup lang="ts">
  import type { DialogTitleProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import { reactiveOmit } from "@vueuse/core";
  import { DialogTitle } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { sheetTitleVariants } from "./styles";

  const props = defineProps<DialogTitleProps & { class?: HTMLAttributes["class"] }>();

  const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
  <DialogTitle data-slot="sheet-title" :class="cn(sheetTitleVariants(), props.class)" v-bind="delegatedProps">
    <slot />
  </DialogTitle>
</template>
