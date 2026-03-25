import { tv, type VariantProps } from 'tailwind-variants';

export const cardVariants = tv({
  base: 'flex flex-col gap-6 rounded-[var(--radius-lg)] py-6 text-[var(--foreground)]',
  variants: {
    variant: {
      default: 'border border-border bg-card shadow-[var(--elevation-1)]',
      destructive: 'border-destructive/35 bg-destructive/8 shadow-[var(--elevation-1)]',
      info: 'border-info/35 bg-info/8 shadow-[var(--elevation-1)]',
      warning: 'border-warning/35 bg-warning/10 shadow-[var(--elevation-1)]',
      success: 'border-success/35 bg-success/8 shadow-[var(--elevation-1)]',
    },
  },
  defaultVariants: {
    variant: 'default',
  },
});

export const cardHeaderVariants = tv({ base: '@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6' });
export const cardTitleVariants = tv({ base: 'leading-none font-semibold' });
export const cardDescriptionVariants = tv({ base: 'text-sm text-muted-foreground' });
export const cardContentVariants = tv({ base: 'px-6' });
export const cardFooterVariants = tv({ base: 'flex items-center px-6 [.border-t]:pt-6' });
export const cardActionVariants = tv({ base: 'col-start-2 row-span-2 row-start-1 self-start justify-self-end' });

export type CardVariants = VariantProps<typeof cardVariants>;
