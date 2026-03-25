import { tv } from 'tailwind-variants';

export const tableWrapperVariants = tv({ base: 'relative w-full overflow-x-auto rounded-[var(--radius-sm)] border border-border/60 bg-background/50 shadow-(--elevation-1)' });
export const tableVariants = tv({ base: 'w-full min-w-full caption-bottom text-sm' });
export const tableHeaderVariants = tv({ base: 'bg-muted/35 [&_tr]:border-b [&_tr]:border-border/70' });
export const tableBodyVariants = tv({ base: '[&_tr:last-child]:border-0' });
export const tableRowVariants = tv({ base: 'border-b border-border/55 transition-colors even:bg-muted/20 hover:bg-muted/35 data-[state=selected]:bg-muted/45 dark:even:bg-muted/24 dark:hover:bg-muted/40 dark:data-[state=selected]:bg-muted/50' });
export const tableHeadVariants = tv({
  base: 'px-4 py-3 text-left align-top text-xs font-semibold tracking-wide text-muted-foreground uppercase md:align-middle md:whitespace-nowrap [&_button]:font-[inherit] [&_button]:leading-[inherit] [&_button]:tracking-[inherit] [&_button]:text-inherit [&_button]:uppercase',
});
export const tableCellVariants = tv({ base: 'px-4 py-3 align-top text-sm leading-6 break-words md:align-middle md:whitespace-nowrap' });
export const tableCaptionVariants = tv({ base: 'mt-3 text-sm text-muted-foreground' });
