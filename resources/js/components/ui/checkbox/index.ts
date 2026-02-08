import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export { default as Checkbox } from "./Checkbox.vue";

export const checkboxVariants = cva(
  "peer border-input focus-visible:border-ring aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive size-4 shrink-0 rounded-[4px] border shadow-xs transition-shadow outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50",
  {
    variants: {
      variant: {
        default: "data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:border-primary focus-visible:ring-ring/50",
        destructive: "data-[state=checked]:bg-destructive data-[state=checked]:text-destructive-foreground data-[state=checked]:border-destructive focus-visible:ring-destructive/30",
        info: "data-[state=checked]:bg-info data-[state=checked]:text-info-foreground data-[state=checked]:border-info focus-visible:ring-info/30",
        warning: "data-[state=checked]:bg-warning data-[state=checked]:text-warning-foreground data-[state=checked]:border-warning focus-visible:ring-warning/30",
        success: "data-[state=checked]:bg-success data-[state=checked]:text-success-foreground data-[state=checked]:border-success focus-visible:ring-success/30",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  },
);

export type CheckboxVariants = VariantProps<typeof checkboxVariants>;
