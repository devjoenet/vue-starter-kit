import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

const tooltipFeedbackVariants = {
  default: "bg-foreground text-background",
  destructive: "bg-destructive text-destructive-foreground",
  info: "bg-info text-info-foreground",
  warning: "bg-warning text-warning-foreground",
  success: "bg-success text-success-foreground",
} as const;

export const tooltipContentVariants = cva(
  "animate-in fade-in-0 zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 w-fit rounded-md px-3 py-1.5 text-xs text-balance",
  {
    variants: {
      variant: tooltipFeedbackVariants,
    },
    defaultVariants: {
      variant: "default",
    },
  },
);
export const tooltipArrowVariants = cva("z-50 size-2.5 translate-y-[calc(-50%_-_2px)] rotate-45 rounded-[2px]", {
  variants: {
    variant: {
      default: "bg-foreground fill-foreground",
      destructive: "bg-destructive fill-destructive",
      info: "bg-info fill-info",
      warning: "bg-warning fill-warning",
      success: "bg-success fill-success",
    },
  },
  defaultVariants: {
    variant: "default",
  },
});

export type TooltipContentVariants = VariantProps<typeof tooltipContentVariants>;
