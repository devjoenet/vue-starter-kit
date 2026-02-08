import { cva } from "class-variance-authority";

export { default as Spinner } from "./Spinner.vue";

export const spinnerVariants = cva("size-4 animate-spin");
