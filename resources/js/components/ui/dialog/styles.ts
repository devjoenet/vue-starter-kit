import type { VariantProps } from 'class-variance-authority';
import { cva } from 'class-variance-authority';

const dialogFeedbackVariants = {
  default: 'border-border bg-background text-foreground',
  destructive: 'border-destructive/35 bg-card text-foreground [&_[data-slot=dialog-title]]:text-destructive [&_[data-slot=dialog-description]]:text-destructive/90',
  info: 'border-info/35 bg-card text-foreground [&_[data-slot=dialog-title]]:text-info [&_[data-slot=dialog-description]]:text-info/90',
  warning: 'border-warning/35 bg-card text-foreground [&_[data-slot=dialog-title]]:text-warning [&_[data-slot=dialog-description]]:text-warning',
  success: 'border-success/35 bg-card text-foreground [&_[data-slot=dialog-title]]:text-success [&_[data-slot=dialog-description]]:text-success/90',
} as const;

export const dialogOverlayVariants = cva(
  'fixed inset-0 z-50 bg-[var(--overlay-backdrop)] data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:animate-in data-[state=open]:fade-in-0 motion-reduce:data-[state=closed]:animate-none motion-reduce:data-[state=open]:animate-none',
);
export const dialogContentVariants = cva(
  'fixed top-[50%] left-[50%] z-50 grid w-full max-w-[calc(100%-2rem)] translate-x-[-50%] translate-y-[-50%] gap-4 rounded-lg border p-6 shadow-lg duration-200 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[state=open]:animate-in data-[state=open]:fade-in-0 data-[state=open]:zoom-in-95 motion-reduce:transition-none motion-reduce:data-[state=closed]:animate-none motion-reduce:data-[state=open]:animate-none sm:max-w-lg',
  {
    variants: {
      variant: dialogFeedbackVariants,
    },
    defaultVariants: {
      variant: 'default',
    },
  },
);
export const dialogCloseVariants = cva(
  "absolute top-4 right-4 rounded-xs opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
  {
    variants: {
      variant: {
        default: 'focus:ring-ring data-[state=open]:bg-accent data-[state=open]:text-muted-foreground',
        destructive: 'focus:ring-destructive/30 data-[state=open]:bg-destructive/12 data-[state=open]:text-destructive',
        info: 'focus:ring-info/30 data-[state=open]:bg-info/12 data-[state=open]:text-info',
        warning: 'focus:ring-warning/30 data-[state=open]:bg-warning/15 data-[state=open]:text-warning',
        success: 'focus:ring-success/30 data-[state=open]:bg-success/12 data-[state=open]:text-success',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
);
export const dialogHeaderVariants = cva('flex flex-col gap-2 text-center sm:text-left');
export const dialogFooterVariants = cva('flex flex-col-reverse gap-2 sm:flex-row sm:justify-end');
export const dialogTitleVariants = cva('text-lg leading-none font-semibold');
export const dialogDescriptionVariants = cva('text-sm text-muted-foreground');
export const dialogScrollOverlayVariants = cva(
  'fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-[var(--overlay-backdrop)] data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:animate-in data-[state=open]:fade-in-0 motion-reduce:data-[state=closed]:animate-none motion-reduce:data-[state=open]:animate-none',
);
export const dialogScrollContentVariants = cva('relative z-50 my-8 grid w-full max-w-lg gap-4 border p-6 shadow-lg duration-200 motion-reduce:transition-none sm:rounded-lg md:w-full', {
  variants: {
    variant: dialogFeedbackVariants,
  },
  defaultVariants: {
    variant: 'default',
  },
});
export const dialogScrollCloseVariants = cva('absolute top-4 right-4 rounded-md p-0.5 transition-colors', {
  variants: {
    variant: {
      default: 'hover:bg-secondary',
      destructive: 'text-destructive hover:bg-destructive/12',
      info: 'text-info hover:bg-info/12',
      warning: 'text-warning hover:bg-warning/15',
      success: 'text-success hover:bg-success/12',
    },
  },
  defaultVariants: {
    variant: 'default',
  },
});

export type DialogContentVariants = VariantProps<typeof dialogContentVariants>;
