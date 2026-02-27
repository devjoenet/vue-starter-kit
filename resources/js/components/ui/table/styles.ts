import { cva } from "class-variance-authority";

export const tableWrapperVariants = cva("relative w-full overflow-x-auto rounded-[var(--radius-sm)] border border-border/60 bg-background/50 shadow-(--elevation-1)");
export const tableVariants = cva("w-full min-w-[40rem] caption-bottom text-sm");
export const tableHeaderVariants = cva("bg-muted/35 [&_tr]:border-b [&_tr]:border-border/70");
export const tableBodyVariants = cva("[&_tr:last-child]:border-0");
export const tableRowVariants = cva("border-b border-border/55 transition-colors hover:bg-muted/35 data-[state=selected]:bg-muted/45");
export const tableHeadVariants = cva(
  "h-11 px-4 text-left align-middle text-xs font-semibold uppercase tracking-wide text-muted-foreground whitespace-nowrap [&_button]:text-inherit [&_button]:font-[inherit] [&_button]:tracking-[inherit] [&_button]:uppercase [&_button]:leading-[inherit]",
);
export const tableCellVariants = cva("p-4 align-middle whitespace-nowrap");
export const tableCaptionVariants = cva("mt-3 text-sm text-muted-foreground");
