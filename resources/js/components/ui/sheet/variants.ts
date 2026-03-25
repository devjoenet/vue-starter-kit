import { tv } from 'tailwind-variants';

export const sheetOverlayVariants = tv({
  base: 'fixed inset-0 z-50 bg-[var(--overlay-backdrop)] data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:animate-in data-[state=open]:fade-in-0 motion-reduce:data-[state=closed]:animate-none motion-reduce:data-[state=open]:animate-none',
});
export const sheetContentVariants = tv({
  base: 'fixed z-50 flex flex-col gap-4 bg-background shadow-lg transition ease-in-out data-[state=closed]:animate-out data-[state=closed]:duration-300 data-[state=open]:animate-in data-[state=open]:duration-500 motion-reduce:transition-none motion-reduce:data-[state=closed]:animate-none motion-reduce:data-[state=open]:animate-none',
  variants: {
    side: {
      right: 'inset-y-0 right-0 h-full w-3/4 border-l data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right sm:max-w-sm',
      left: 'inset-y-0 left-0 h-full w-3/4 border-r data-[state=closed]:slide-out-to-left data-[state=open]:slide-in-from-left sm:max-w-sm',
      top: 'inset-x-0 top-0 h-auto border-b data-[state=closed]:slide-out-to-top data-[state=open]:slide-in-from-top',
      bottom: 'inset-x-0 bottom-0 h-auto border-t data-[state=closed]:slide-out-to-bottom data-[state=open]:slide-in-from-bottom',
    },
  },
  defaultVariants: {
    side: 'right',
  },
});
export const sheetCloseVariants = tv({
  base: 'absolute top-4 right-4 rounded-xs opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none data-[state=open]:bg-secondary',
});
export const sheetHeaderVariants = tv({ base: 'flex flex-col gap-1.5 p-4' });
export const sheetFooterVariants = tv({ base: 'mt-auto flex flex-col gap-2 p-4' });
export const sheetTitleVariants = tv({ base: 'font-semibold text-foreground' });
export const sheetDescriptionVariants = tv({ base: 'text-sm text-muted-foreground' });
