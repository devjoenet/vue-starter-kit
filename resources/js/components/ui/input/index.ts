import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export { default as Input } from "./Input.vue";

export const inputVariants = cva(
  "peer w-full min-w-0 rounded-[var(--radius-sm)] text-base leading-6 text-[var(--field-text)] transition-[border-color,box-shadow,background-color,color] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 focus-visible:border-[color:var(--field-focus)] focus-visible:ring-2 focus-visible:ring-[color:var(--field-focus)]",
  {
    variants: {
      variant: {
        filled: "border-b border-[color:var(--outline)] bg-[var(--field-bg)]",
        outlined: "border border-[color:var(--outline)] bg-transparent",
      },
      state: {
        default: "",
        error: "border-[color:var(--error)] ring-[color:var(--error)]",
        destructive: "border-destructive ring-destructive",
        info: "border-info ring-info",
        warning: "border-warning ring-warning",
        success: "border-success ring-success",
      },
      multiline: {
        true: "min-h-[3.5rem] resize-y",
        false: "h-14",
      },
      label: {
        true: "pt-6 pb-2",
        false: "py-4",
      },
    },
    defaultVariants: {
      variant: "filled",
      state: "default",
      multiline: false,
      label: false,
    },
  },
);

export const inputLabelVariants = cva(
  "absolute top-4 origin-left text-sm text-[var(--field-label)] transition-[transform,color,top,background-color] duration-150 peer-placeholder-shown:top-4 peer-placeholder-shown:scale-100 peer-focus:top-2 peer-focus:scale-90 peer-[&:not(:placeholder-shown)]:top-2 peer-[&:not(:placeholder-shown)]:scale-90 peer-focus:text-[var(--field-focus)] peer-disabled:opacity-60",
  {
    variants: {
      variant: {
        filled: "bg-[var(--field-bg)] px-1 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0",
        outlined: "bg-[var(--surface)] px-1 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0",
      },
      state: {
        default: "",
        error: "text-[var(--error)] peer-focus:text-[var(--error)]",
        destructive: "text-destructive peer-focus:text-destructive",
        info: "text-info peer-focus:text-info",
        warning: "text-warning peer-focus:text-warning",
        success: "text-success peer-focus:text-success",
      },
    },
    defaultVariants: {
      variant: "filled",
      state: "default",
    },
  },
);

export const inputAssistiveTextVariants = cva("text-xs", {
  variants: {
    state: {
      default: "text-[var(--field-label)]",
      error: "text-[var(--error)]",
      destructive: "text-destructive",
      info: "text-info",
      warning: "text-warning",
      success: "text-success",
    },
  },
  defaultVariants: {
    state: "default",
  },
});

export type InputVariants = VariantProps<typeof inputVariants>;
