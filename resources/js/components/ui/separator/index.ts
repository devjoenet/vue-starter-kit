import { cva } from "class-variance-authority";

export { default as Separator } from "./Separator.vue";

export const separatorVariants = cva("bg-border shrink-0 data-[orientation=horizontal]:h-px data-[orientation=horizontal]:w-full data-[orientation=vertical]:h-full data-[orientation=vertical]:w-px");
