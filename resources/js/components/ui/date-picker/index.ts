import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";
import type { InputVariants } from "@/components/ui/input";

export { default as DatePicker } from "./DatePicker.vue";

export type DatePickerVariants = {
  variant?: InputVariants["variant"];
  state?: InputVariants["state"];
};

export const datePickerDayVariants = cva("flex size-9 items-center justify-center rounded-full text-sm transition-colors duration-150 disabled:pointer-events-none disabled:opacity-45", {
  variants: {
    variant: {
      default: "text-foreground hover:bg-muted",
      muted: "text-muted-foreground hover:bg-muted/70",
      selected: "bg-primary text-primary-foreground hover:bg-primary/90",
      today: "border border-primary/50 text-primary hover:bg-primary/10",
    },
  },
  defaultVariants: {
    variant: "default",
  },
});

export type DatePickerDayVariants = VariantProps<typeof datePickerDayVariants>;
