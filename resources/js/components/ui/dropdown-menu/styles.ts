import type { VariantProps } from 'class-variance-authority';
import { cva } from 'class-variance-authority';

export const dropdownMenuItemVariants = cva(
  "relative flex cursor-default items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
  {
    variants: {
      variant: {
        default:
          "focus:bg-accent focus:text-accent-foreground [&_svg:not([class*='text-'])]:text-muted-foreground",
        destructive:
          'text-destructive focus:bg-destructive/10 focus:text-destructive dark:focus:bg-destructive/20 [&_svg]:text-destructive',
      },
      inset: {
        true: 'pl-8',
        false: '',
      },
    },
    defaultVariants: {
      variant: 'default',
      inset: false,
    },
  },
);

export type DropdownMenuItemVariants = VariantProps<
  typeof dropdownMenuItemVariants
>;

export const dropdownMenuLabelVariants = cva(
  'px-2 py-1.5 text-sm font-medium',
  {
    variants: {
      inset: {
        true: 'pl-8',
        false: '',
      },
    },
    defaultVariants: {
      inset: false,
    },
  },
);
export const dropdownMenuSeparatorVariants = cva('-mx-1 my-1 h-px bg-border');
export const dropdownMenuShortcutVariants = cva(
  'ml-auto text-xs tracking-widest text-muted-foreground',
);
export const dropdownMenuSubTriggerVariants = cva(
  'flex cursor-default items-center rounded-sm px-2 py-1.5 text-sm outline-hidden select-none focus:bg-accent focus:text-accent-foreground data-[state=open]:bg-accent data-[state=open]:text-accent-foreground',
  {
    variants: {
      inset: {
        true: 'pl-8',
        false: '',
      },
    },
    defaultVariants: {
      inset: false,
    },
  },
);
