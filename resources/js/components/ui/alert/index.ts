import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export { default as Alert } from "./Alert.vue";
export { default as AlertDescription } from "./AlertDescription.vue";
export { default as AlertTitle } from "./AlertTitle.vue";

export const alertVariants = cva("relative w-full rounded-lg border px-4 py-3 text-sm grid has-[>svg]:grid-cols-[calc(var(--spacing)*4)_1fr] grid-cols-[0_1fr] has-[>svg]:gap-x-3 gap-y-0.5 items-start [&>svg]:size-4 [&>svg]:translate-y-0.5 [&>svg]:text-current", {
  variants: {
    variant: {
      default: "border-border bg-card text-card-foreground",
      destructive: "border-destructive/35 bg-destructive/10 text-destructive [&>svg]:text-current *:data-[slot=alert-description]:text-destructive/90",
      info: "border-info/35 bg-info/10 text-info [&>svg]:text-current *:data-[slot=alert-description]:text-info/90",
      warning: "border-warning/35 bg-warning/12 text-warning [&>svg]:text-current *:data-[slot=alert-description]:text-warning/90",
      success: "border-success/35 bg-success/12 text-success [&>svg]:text-current *:data-[slot=alert-description]:text-success/90",
    },
  },
  defaultVariants: {
    variant: "default",
  },
});

export const alertTitleVariants = cva("col-start-2 line-clamp-1 min-h-4 font-medium tracking-tight");
export const alertDescriptionVariants = cva("text-muted-foreground col-start-2 grid justify-items-start gap-1 text-sm [&_p]:leading-relaxed");

export type AlertVariants = VariantProps<typeof alertVariants>;
