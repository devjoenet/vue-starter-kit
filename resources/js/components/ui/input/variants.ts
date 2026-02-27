import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export const inputVariants = cva(
  "peer w-full min-w-0 rounded-[var(--radius-sm)] text-base leading-6 text-[var(--field-text)] transition-[border-color,box-shadow,background-color,color] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-55 focus-visible:ring-2 focus-visible:ring-[color:var(--field-focus)]/30",
  {
    variants: {
      variant: {
        filled: "border border-transparent border-b-[color:var(--outline)] rounded-t-[var(--radius-sm)] rounded-b-none bg-[var(--field-bg)]/90 hover:bg-[var(--field-bg)] focus-visible:border-b-[color:var(--field-focus)]",
        outlined: "border border-[color:var(--outline)] bg-transparent hover:border-[color:var(--field-focus)]/50 focus-visible:border-[color:var(--field-focus)]",
      },
      state: {
        default: "",
        error: "border-[color:var(--error)] ring-[color:var(--error)]/30",
        destructive: "border-destructive ring-destructive/30",
        info: "border-info ring-info/30",
        warning: "border-warning ring-warning/30",
        success: "border-success ring-success/30",
      },
      multiline: {
        true: "min-h-[3.5rem] py-4",
        false: "h-14",
      },
      label: {
        true: "pt-6 pb-2 placeholder:text-transparent",
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
  "pointer-events-none absolute top-4 origin-left text-sm text-[var(--field-label)] transition-[transform,color,top] duration-150 peer-placeholder-shown:top-4 peer-placeholder-shown:scale-100 peer-focus:top-2 peer-focus:scale-90 peer-[&:not(:placeholder-shown)]:top-2 peer-[&:not(:placeholder-shown)]:scale-90 peer-focus:text-[var(--field-focus)] peer-disabled:opacity-60",
  {
    variants: {
      variant: {
        filled: "bg-transparent px-0",
        outlined: "bg-transparent px-0",
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
