import { tv, type VariantProps } from 'tailwind-variants';

export const checkboxVariants = tv({
  base: 'peer size-4 shrink-0 rounded-[4px] border border-input shadow-xs transition-shadow outline-none focus-visible:border-ring focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40',
  variants: {
    variant: {
      default: 'focus-visible:ring-ring/50 data-[state=checked]:border-primary data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground',
      destructive: 'focus-visible:ring-destructive/30 data-[state=checked]:border-destructive data-[state=checked]:bg-destructive data-[state=checked]:text-destructive-foreground',
      info: 'focus-visible:ring-info/30 data-[state=checked]:border-info data-[state=checked]:bg-info data-[state=checked]:text-info-foreground',
      warning: 'focus-visible:ring-warning/30 data-[state=checked]:border-warning data-[state=checked]:bg-warning data-[state=checked]:text-warning-foreground',
      success: 'focus-visible:ring-success/30 data-[state=checked]:border-success data-[state=checked]:bg-success data-[state=checked]:text-success-foreground',
    },
  },
  defaultVariants: {
    variant: 'default',
  },
});

export type CheckboxVariants = VariantProps<typeof checkboxVariants>;
