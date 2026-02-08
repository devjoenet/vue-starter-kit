import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export { default as Button } from "./Button.vue";

export const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-[var(--radius-lg)] text-sm font-medium transition-[background-color,box-shadow,color,transform] duration-150 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:ring-2 focus-visible:ring-[var(--field-focus)]",
  {
    variants: {
      variant: {
        default: "bg-primary text-primary-foreground shadow-[var(--elevation-1)] hover:bg-primary/85 hover:shadow-[var(--elevation-2)]",
        filled: "bg-primary text-primary-foreground shadow-[var(--elevation-1)] hover:bg-primary/85 hover:shadow-[var(--elevation-2)]",
        tonal: "bg-muted text-foreground shadow-[var(--elevation-1)] hover:bg-muted/80 hover:shadow-[var(--elevation-2)]",
        outline: "border border-border/70 bg-transparent text-foreground hover:bg-muted/60",
        outlined: "border border-border/70 bg-transparent text-foreground hover:bg-muted/60",
        text: "bg-transparent text-foreground hover:bg-muted/60",
        elevated: "bg-card text-foreground shadow-[var(--elevation-2)] hover:bg-card/90 hover:shadow-[var(--elevation-3)]",
        destructive: "bg-destructive text-destructive-foreground shadow-[var(--elevation-1)] hover:bg-destructive/85 hover:shadow-[var(--elevation-2)]",
        info: "bg-info text-info-foreground shadow-[var(--elevation-1)] hover:bg-info/85 hover:shadow-[var(--elevation-2)]",
        warning: "bg-warning text-warning-foreground shadow-[var(--elevation-1)] hover:bg-warning/85 hover:shadow-[var(--elevation-2)]",
        success: "bg-success text-success-foreground shadow-[var(--elevation-1)] hover:bg-success/85 hover:shadow-[var(--elevation-2)]",
        secondary: "bg-secondary text-secondary-foreground shadow-[var(--elevation-1)] hover:bg-secondary/85 hover:shadow-[var(--elevation-2)]",
        ghost: "bg-transparent text-foreground hover:bg-muted/60",
        link: "bg-transparent text-foreground underline-offset-4 hover:underline",
        glass:
          "relative overflow-hidden border border-[color:var(--glass-border)] bg-[color:var(--glass-surface)] text-[var(--foreground)] shadow-[var(--glass-shadow)] backdrop-blur-xl before:pointer-events-none before:absolute before:inset-0 before:rounded-[var(--radius-lg)] before:bg-[radial-gradient(120%_120%_at_50%_-20%,_var(--glass-highlight)_0%,_transparent_55%)] before:opacity-80 hover:bg-[color:var(--glass-surface-hover)] hover:shadow-[var(--elevation-2)]",
      },
      size: {
        default: "h-10 px-4",
        sm: "h-9 px-3 text-sm",
        lg: "h-12 px-6 text-base",
        icon: "size-10",
        iconSm: "size-9",
        iconLg: "size-12",
      },
    },
    defaultVariants: {
      variant: "filled",
      size: "default",
    },
  },
);
export type ButtonVariants = VariantProps<typeof buttonVariants>;
