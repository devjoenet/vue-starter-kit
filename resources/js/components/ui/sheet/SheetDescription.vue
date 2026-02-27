<script setup lang="ts">
  import type { DialogDescriptionProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import { reactiveOmit } from "@vueuse/core";
  import { DialogDescription } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { sheetDescriptionVariants } from "./styles";

  const props = defineProps<DialogDescriptionProps & { class?: HTMLAttributes["class"] }>();

  const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
  <DialogDescription data-slot="sheet-description" :class="cn(sheetDescriptionVariants(), props.class)" v-bind="delegatedProps">
    <slot />
  </DialogDescription>
</template>
