import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export { default as Dialog } from "./Dialog.vue";
export { default as DialogClose } from "./DialogClose.vue";
export { default as DialogContent } from "./DialogContent.vue";
export { default as DialogDescription } from "./DialogDescription.vue";
export { default as DialogFooter } from "./DialogFooter.vue";
export { default as DialogHeader } from "./DialogHeader.vue";
export { default as DialogOverlay } from "./DialogOverlay.vue";
export { default as DialogScrollContent } from "./DialogScrollContent.vue";
export { default as DialogTitle } from "./DialogTitle.vue";
export { default as DialogTrigger } from "./DialogTrigger.vue";

const dialogFeedbackVariants = {
  default: "border-border bg-background text-foreground",
  destructive: "border-destructive/35 bg-card text-foreground [&_[data-slot=dialog-title]]:text-destructive [&_[data-slot=dialog-description]]:text-destructive/90",
  info: "border-info/35 bg-card text-foreground [&_[data-slot=dialog-title]]:text-info [&_[data-slot=dialog-description]]:text-info/90",
  warning: "border-warning/35 bg-card text-foreground [&_[data-slot=dialog-title]]:text-warning [&_[data-slot=dialog-description]]:text-warning",
  success: "border-success/35 bg-card text-foreground [&_[data-slot=dialog-title]]:text-success [&_[data-slot=dialog-description]]:text-success/90",
} as const;

export const dialogOverlayVariants = cva("data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 fixed inset-0 z-50 bg-black/80");
export const dialogContentVariants = cva(
  "data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 fixed top-[50%] left-[50%] z-50 grid w-full max-w-[calc(100%-2rem)] translate-x-[-50%] translate-y-[-50%] gap-4 rounded-lg border p-6 shadow-lg duration-200 sm:max-w-lg",
  {
    variants: {
      variant: dialogFeedbackVariants,
    },
    defaultVariants: {
      variant: "default",
    },
  },
);
export const dialogCloseVariants = cva(
  "ring-offset-background absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
  {
    variants: {
      variant: {
        default: "focus:ring-ring data-[state=open]:bg-accent data-[state=open]:text-muted-foreground",
        destructive: "focus:ring-destructive/30 data-[state=open]:bg-destructive/12 data-[state=open]:text-destructive",
        info: "focus:ring-info/30 data-[state=open]:bg-info/12 data-[state=open]:text-info",
        warning: "focus:ring-warning/30 data-[state=open]:bg-warning/15 data-[state=open]:text-warning",
        success: "focus:ring-success/30 data-[state=open]:bg-success/12 data-[state=open]:text-success",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  },
);
export const dialogHeaderVariants = cva("flex flex-col gap-2 text-center sm:text-left");
export const dialogFooterVariants = cva("flex flex-col-reverse gap-2 sm:flex-row sm:justify-end");
export const dialogTitleVariants = cva("text-lg leading-none font-semibold");
export const dialogDescriptionVariants = cva("text-muted-foreground text-sm");
export const dialogScrollOverlayVariants = cva("fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0");
export const dialogScrollContentVariants = cva("relative z-50 grid w-full max-w-lg my-8 gap-4 border p-6 shadow-lg duration-200 sm:rounded-lg md:w-full", {
  variants: {
    variant: dialogFeedbackVariants,
  },
  defaultVariants: {
    variant: "default",
  },
});
export const dialogScrollCloseVariants = cva("absolute top-4 right-4 p-0.5 transition-colors rounded-md", {
  variants: {
    variant: {
      default: "hover:bg-secondary",
      destructive: "text-destructive hover:bg-destructive/12",
      info: "text-info hover:bg-info/12",
      warning: "text-warning hover:bg-warning/15",
      success: "text-success hover:bg-success/12",
    },
  },
  defaultVariants: {
    variant: "default",
  },
});

export type DialogContentVariants = VariantProps<typeof dialogContentVariants>;
