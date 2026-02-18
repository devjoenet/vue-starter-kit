import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export { default as Card } from "./Card.vue";
export { default as CardAction } from "./CardAction.vue";
export { default as CardContent } from "./CardContent.vue";
export { default as CardDescription } from "./CardDescription.vue";
export { default as CardFooter } from "./CardFooter.vue";
export { default as CardHeader } from "./CardHeader.vue";
export { default as CardTitle } from "./CardTitle.vue";

export const cardVariants = cva("flex flex-col gap-6 rounded-[var(--radius-lg)] py-6 text-[var(--foreground)]", {
  variants: {
    variant: {
      default: "border border-[color:var(--outline)] bg-[var(--surface)] shadow-[var(--elevation-1)]",
      destructive: "border-destructive/35 bg-destructive/8 shadow-[var(--elevation-1)]",
      info: "border-info/35 bg-info/8 shadow-[var(--elevation-1)]",
      warning: "border-warning/35 bg-warning/10 shadow-[var(--elevation-1)]",
      success: "border-success/35 bg-success/8 shadow-[var(--elevation-1)]",
      glass: "liquid-glass liquid-glass-hover",
    },
  },
  defaultVariants: {
    variant: "default",
  },
});

export const cardHeaderVariants = cva("@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6");
export const cardTitleVariants = cva("leading-none font-semibold");
export const cardDescriptionVariants = cva("text-muted-foreground text-sm");
export const cardContentVariants = cva("px-6");
export const cardFooterVariants = cva("flex items-center px-6 [.border-t]:pt-6");
export const cardActionVariants = cva("col-start-2 row-span-2 row-start-1 self-start justify-self-end");

export type CardVariants = VariantProps<typeof cardVariants>;
