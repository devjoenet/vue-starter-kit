import type { VariantProps } from 'class-variance-authority';
import { cva } from 'class-variance-authority';

export const alertVariants = cva(
  'relative grid w-full grid-cols-[0_1fr] items-start gap-y-1 rounded-[1rem] border px-4 py-3.5 text-sm shadow-[var(--elevation-1)] has-[>svg]:grid-cols-[calc(var(--spacing)*4)_1fr] has-[>svg]:gap-x-3 [&>svg]:size-4 [&>svg]:translate-y-0.5',
  {
    variants: {
      variant: {
        default:
          'border-border bg-card text-foreground [&>svg]:text-muted-foreground',
        destructive:
          'border-destructive/35 bg-destructive/10 text-foreground *:data-[slot=alert-description]:text-muted-foreground [&>svg]:text-destructive',
        info: 'border-info/35 bg-info/10 text-foreground *:data-[slot=alert-description]:text-muted-foreground [&>svg]:text-info',
        warning:
          'border-warning/35 bg-warning/12 text-foreground *:data-[slot=alert-description]:text-muted-foreground [&>svg]:text-warning',
        success:
          'border-success/35 bg-success/12 text-foreground *:data-[slot=alert-description]:text-muted-foreground [&>svg]:text-success',
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
