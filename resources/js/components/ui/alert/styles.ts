import type { VariantProps } from 'class-variance-authority';
import { cva } from 'class-variance-authority';

export const alertVariants = cva(
  'relative grid w-full grid-cols-[0_1fr] items-start gap-y-0.5 rounded-lg border px-4 py-3 text-sm has-[>svg]:grid-cols-[calc(var(--spacing)*4)_1fr] has-[>svg]:gap-x-3 [&>svg]:size-4 [&>svg]:translate-y-0.5 [&>svg]:text-current',
  {
    variants: {
      variant: {
        default: 'border-border bg-card text-card-foreground',
        destructive:
          'border-destructive/35 bg-destructive/10 text-destructive *:data-[slot=alert-description]:text-destructive/90 [&>svg]:text-current',
        info: 'border-info/35 bg-info/10 text-info *:data-[slot=alert-description]:text-info/90 [&>svg]:text-current',
        warning:
          'border-warning/35 bg-warning/12 text-warning *:data-[slot=alert-description]:text-warning/90 [&>svg]:text-current',
        success:
          'border-success/35 bg-success/12 text-success *:data-[slot=alert-description]:text-success/90 [&>svg]:text-current',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
);

export const alertTitleVariants = cva(
  'col-start-2 line-clamp-1 min-h-4 font-medium tracking-tight',
);
export const alertDescriptionVariants = cva(
  'col-start-2 grid justify-items-start gap-1 text-sm text-muted-foreground [&_p]:leading-relaxed',
);

export type AlertVariants = VariantProps<typeof alertVariants>;
