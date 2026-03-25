import { tv, type VariantProps } from 'tailwind-variants';
import type { InputVariants } from '@/components/ui/input/variants';

export type SignatureDataUrlType = 'png' | 'jpg';

export type SignatureVariants = {
  variant?: InputVariants['variant'];
  state?: InputVariants['state'];
};

export const signatureSurfaceVariants = tv({
  base: 'relative w-full overflow-hidden rounded-[var(--radius-sm)] border bg-muted/20 transition-colors',
  variants: {
    state: {
      default: 'border-border',
      primary: 'border-primary',
      error: 'border-destructive',
      destructive: 'border-destructive',
      info: 'border-info',
      warning: 'border-warning',
      success: 'border-success',
    },
  },
  defaultVariants: {
    state: 'default',
  },
});

export type SignatureSurfaceVariants = VariantProps<typeof signatureSurfaceVariants>;
