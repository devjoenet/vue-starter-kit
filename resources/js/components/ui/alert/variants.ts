import { cx, tv, type VariantProps } from 'tailwind-variants';

export const alert = tv({
  base: 'relative grid w-full grid-cols-[0_1fr] items-start gap-y-1 rounded-2xl border px-4 py-3.5 text-sm shadow-(--elevation-1) has-[>svg]:grid-cols-[--spacing(4)_1fr] has-[>svg]:gap-x-3 [&>svg]:size-4 [&>svg]:translate-y-0.5',
  variants: {
    variant: {
      primary: [],
      info: ['border-info/35', 'bg-info/10', 'text-foreground', '*:data-[slot=alert-description]:text-muted-foreground', '[&>svg]:text-info'],
      warning: ['border-warning/35', 'bg-warning/12', 'text-foreground', '*:data-[slot=alert-description]:text-muted-foreground', '[&>svg]:text-warning'],
      success: ['border-success/35', 'bg-success/12', 'text-foreground *:data-[slot=alert-description]:text-muted-foreground', '[&>svg]:text-success'],
      destructive: ['border-destructive/35', 'bg-destructive/10', 'text-destructive-foreground', '*:data-[slot=alert-description]:text-muted-foreground', '[&>svg]:text-destructive'],
    },
  },
  defaultVariants: {
    variant: 'primary',
  },
});

export const alertTitle = cx(['col-start-2', 'line-clamp-1', 'min-h-4', 'font-medium', 'tracking-tight']);
export const alertDescription = cx(['col-start-2', 'grid', 'justify-items-start', 'gap-1', 'text-sm', 'text-muted-foreground', '[&_p]:leading-relaxed']);

export type AlertVariants = VariantProps<typeof alert>;
