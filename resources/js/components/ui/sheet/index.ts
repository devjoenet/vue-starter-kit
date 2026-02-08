import { cva } from "class-variance-authority";

export { default as Sheet } from "./Sheet.vue";
export { default as SheetClose } from "./SheetClose.vue";
export { default as SheetContent } from "./SheetContent.vue";
export { default as SheetDescription } from "./SheetDescription.vue";
export { default as SheetFooter } from "./SheetFooter.vue";
export { default as SheetHeader } from "./SheetHeader.vue";
export { default as SheetTitle } from "./SheetTitle.vue";
export { default as SheetTrigger } from "./SheetTrigger.vue";

export const sheetOverlayVariants = cva("data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 fixed inset-0 z-50 bg-black/80");
export const sheetContentVariants = cva("bg-background data-[state=open]:animate-in data-[state=closed]:animate-out fixed z-50 flex flex-col gap-4 shadow-lg transition ease-in-out data-[state=closed]:duration-300 data-[state=open]:duration-500", {
  variants: {
    side: {
      right: "data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right inset-y-0 right-0 h-full w-3/4 border-l sm:max-w-sm",
      left: "data-[state=closed]:slide-out-to-left data-[state=open]:slide-in-from-left inset-y-0 left-0 h-full w-3/4 border-r sm:max-w-sm",
      top: "data-[state=closed]:slide-out-to-top data-[state=open]:slide-in-from-top inset-x-0 top-0 h-auto border-b",
      bottom: "data-[state=closed]:slide-out-to-bottom data-[state=open]:slide-in-from-bottom inset-x-0 bottom-0 h-auto border-t",
    },
  },
  defaultVariants: {
    side: "right",
  },
});
export const sheetCloseVariants = cva("ring-offset-background focus:ring-ring data-[state=open]:bg-secondary absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none");
export const sheetHeaderVariants = cva("flex flex-col gap-1.5 p-4");
export const sheetFooterVariants = cva("mt-auto flex flex-col gap-2 p-4");
export const sheetTitleVariants = cva("text-foreground font-semibold");
export const sheetDescriptionVariants = cva("text-muted-foreground text-sm");
