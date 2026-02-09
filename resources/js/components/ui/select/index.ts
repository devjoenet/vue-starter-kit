import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";
import type { InputVariants } from "@/components/ui/input";

export { default as Select } from "./Select.vue";

export type SelectOption = {
  value: string;
  label: string;
  disabled?: boolean;
};

export const selectLabelVariants = cva(
  "absolute top-4 origin-left text-sm text-[var(--field-label)] transition-[transform,color,top,background-color] duration-150 peer-focus:top-2 peer-focus:scale-90 peer-data-[open=true]:top-2 peer-data-[open=true]:scale-90 peer-data-[filled=true]:top-2 peer-data-[filled=true]:scale-90 peer-focus:text-[var(--field-focus)] peer-data-[open=true]:text-[var(--field-focus)] peer-disabled:opacity-60",
  {
    variants: {
      variant: {
        filled: "bg-transparent px-0",
        outlined: "bg-transparent px-0",
      },
      state: {
        default: "",
        error: "text-[var(--error)] peer-focus:text-[var(--error)] peer-data-[open=true]:text-[var(--error)]",
        destructive: "text-destructive peer-focus:text-destructive peer-data-[open=true]:text-destructive",
        info: "text-info peer-focus:text-info peer-data-[open=true]:text-info",
        warning: "text-warning peer-focus:text-warning peer-data-[open=true]:text-warning",
        success: "text-success peer-focus:text-success peer-data-[open=true]:text-success",
      },
    },
    defaultVariants: {
      variant: "filled",
      state: "default",
    },
  },
);

export const selectOptionVariants = cva("data-[state=checked]:bg-muted/65 data-[state=checked]:text-foreground", {
  variants: {
    variant: {
      muted: "hover:bg-muted/80 hover:text-foreground focus:bg-muted/80 focus:text-foreground",
      primary: "hover:bg-primary/12 hover:text-primary focus:bg-primary/12 focus:text-primary",
      secondary: "hover:bg-secondary/12 hover:text-secondary focus:bg-secondary/12 focus:text-secondary",
      info: "hover:bg-info/12 hover:text-info focus:bg-info/12 focus:text-info",
      warning: "hover:bg-warning/12 hover:text-warning focus:bg-warning/12 focus:text-warning",
      success: "hover:bg-success/12 hover:text-success focus:bg-success/12 focus:text-success",
      destructive: "hover:bg-destructive/12 hover:text-destructive focus:bg-destructive/12 focus:text-destructive",
    },
  },
  defaultVariants: {
    variant: "primary",
  },
});

export type SelectVariants = {
  variant?: InputVariants["variant"];
  state?: InputVariants["state"];
};

export type SelectLabelVariants = VariantProps<typeof selectLabelVariants>;
export type SelectOptionVariants = VariantProps<typeof selectOptionVariants>;
export type SelectOptionVariant = NonNullable<SelectOptionVariants["variant"]>;
