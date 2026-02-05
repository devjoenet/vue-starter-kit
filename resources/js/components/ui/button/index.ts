import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export { default as Button } from "./Button.vue";

export const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-[background-color,box-shadow,color,transform] duration-150 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:ring-2 focus-visible:ring-[var(--field-focus)]",
  {
    variants: {
      variant: {
        default: "bg-[var(--sc-primary)] text-[#08111f] shadow-[var(--elevation-1)] hover:shadow-[var(--elevation-2)]",
        filled: "bg-[var(--sc-primary)] text-[#08111f] shadow-[var(--elevation-1)] hover:shadow-[var(--elevation-2)]",
        tonal: "bg-[var(--field-bg)] text-[var(--foreground)] shadow-[var(--elevation-1)] hover:shadow-[var(--elevation-2)]",
        outline: "border border-[color:var(--outline)] bg-transparent text-[var(--foreground)] hover:bg-[var(--field-bg)]",
        outlined: "border border-[color:var(--outline)] bg-transparent text-[var(--foreground)] hover:bg-[var(--field-bg)]",
        text: "bg-transparent text-[var(--foreground)] hover:bg-[var(--field-bg)]",
        elevated: "bg-[var(--surface)] text-[var(--foreground)] shadow-[var(--elevation-2)] hover:shadow-[var(--elevation-3)]",
        destructive: "bg-[var(--error)] text-white shadow-[var(--elevation-1)] hover:shadow-[var(--elevation-2)]",
        secondary: "bg-[var(--field-bg)] text-[var(--foreground)] shadow-[var(--elevation-1)] hover:shadow-[var(--elevation-2)]",
        ghost: "bg-transparent text-[var(--foreground)] hover:bg-[var(--field-bg)]",
        link: "bg-transparent text-[var(--foreground)] underline-offset-4 hover:underline",
      },
      size: {
        default: "h-10 px-4",
        sm: "h-9 px-3 text-sm",
        lg: "h-12 px-6 text-base",
        icon: "size-10",
        "icon-sm": "size-9",
        "icon-lg": "size-12",
      },
    },
    defaultVariants: {
      variant: "filled",
      size: "default",
    },
  },
);
export type ButtonVariants = VariantProps<typeof buttonVariants>;
