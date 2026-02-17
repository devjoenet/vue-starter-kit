import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";

export { default as Button } from "./Button.vue";

type ButtonAppearance = "filled" | "tonal" | "outline" | "elevated" | "text" | "ghost" | "link" | "glass";
type ButtonVariantName = "muted" | "primary" | "secondary" | "info" | "warning" | "success" | "destructive";

const buttonAppearanceVariantClasses: Record<ButtonAppearance, Record<ButtonVariantName, string>> = {
  filled: {
    muted: "bg-muted text-foreground hover:bg-muted/80",
    primary: "bg-primary text-primary-foreground hover:bg-primary/80",
    secondary: "bg-secondary text-secondary-foreground hover:bg-secondary/80",
    info: "bg-info text-info-foreground hover:bg-info/80",
    warning: "bg-warning text-warning-foreground hover:bg-warning/80",
    success: "bg-success text-success-foreground hover:bg-success/80",
    destructive: "bg-destructive text-destructive-foreground hover:bg-destructive/80",
  },
  tonal: {
    muted: "border-border/40 bg-muted/75 text-foreground hover:bg-muted",
    primary: "border-primary/30 bg-primary/15 text-primary hover:bg-primary/22",
    secondary: "border-secondary/30 bg-secondary/15 text-secondary hover:bg-secondary/22",
    info: "border-info/30 bg-info/15 text-info hover:bg-info/22",
    warning: "border-warning/30 bg-warning/15 text-warning hover:bg-warning/22",
    success: "border-success/30 bg-success/15 text-success hover:bg-success/22",
    destructive: "border-destructive/30 bg-destructive/15 text-destructive hover:bg-destructive/22",
  },
  outline: {
    muted: "border-border text-foreground hover:bg-muted hover:text-muted-foreground",
    primary: "border-primary text-primary hover:bg-primary hover:text-primary-foreground",
    secondary: "border-secondary text-secondary hover:bg-secondary hover:text-secondary-foreground",
    info: "border-info text-info hover:bg-info ",
    warning: "border-warning text-warning hover:bg-warning hover:text-warning-foreground",
    success: "border-success text-success hover:bg-success hover:text-success-foreground",
    destructive: "border-destructive text-destructive hover:bg-destructive hover:text-destructive-foreground",
  },
  elevated: {
    muted: "border-border/60 bg-card text-foreground hover:bg-muted/50",
    primary: "border-primary/35 bg-primary text-primary-foreground hover:bg-primary/65",
    secondary: "border-secondary/35 bg-secondary text-secondary-foreground hover:bg-secondary/65",
    info: "border-info/35 bg-info text-info-foreground hover:bg-info/65",
    warning: "border-warning/35 bg-warning text-warning-foreground hover:bg-warning/65",
    success: "border-success/35 bg-success text-success-foreground hover:bg-success/65",
    destructive: "border-destructive/35 bg-destructive text-destructive-foreground hover:bg-destructive/65",
  },
  text: {
    muted: "text-foreground hover:bg-muted/50",
    primary: "text-primary hover:bg-primary/10",
    secondary: "text-secondary hover:bg-secondary/10",
    info: "text-info hover:bg-info/10",
    warning: "text-warning hover:bg-warning/10",
    success: "text-success hover:bg-success/10",
    destructive: "text-destructive hover:bg-destructive/10",
  },
  ghost: {
    muted: "text-foreground hover:bg-muted/60",
    primary: "text-primary hover:bg-primary/14",
    secondary: "text-secondary hover:bg-secondary/14",
    info: "text-info hover:bg-info/14",
    warning: "text-warning hover:bg-warning/14",
    success: "text-success hover:bg-success/14",
    destructive: "text-destructive hover:bg-destructive/14",
  },
  link: {
    muted: "text-foreground hover:text-foreground/85",
    primary: "text-primary hover:text-primary/80",
    secondary: "text-secondary hover:text-secondary/80",
    info: "text-info hover:text-info/80",
    warning: "text-warning hover:text-warning/80",
    success: "text-success hover:text-success/80",
    destructive: "text-destructive hover:text-destructive/80",
  },
  glass: {
    muted: "border-[color:var(--glass-border)] bg-[color:var(--glass-surface)] text-foreground hover:bg-[color:var(--glass-surface-hover)]",
    primary: "border-primary bg-primary text-primary-foreground hover:bg-primary",
    secondary: "border-secondary bg-secondary text-secondary-foreground hover:bg-secondary",
    info: "border-info bg-info text-info-foreground hover:bg-info",
    warning: "border-warning bg-warning text-warning-foreground hover:bg-warning",
    success: "border-success bg-success text-success-foreground hover:bg-success",
    destructive: "border-destructive bg-destructive text-destructive-foreground hover:bg-destructive",
  },
};

export const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium transition-[background-color,box-shadow,color,transform] duration-300 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:ring-2 focus-visible:ring-[var(--field-focus)]",
  {
    variants: {
      appearance: {
        filled: "shadow-[var(--elevation-1)] hover:shadow-[var(--elevation-2)]",
        tonal: "border shadow-[var(--elevation-1)] hover:shadow-[var(--elevation-2)]",
        outline: "border bg-transparent",
        text: "border border-transparent bg-transparent shadow-none",
        elevated: "border shadow-[var(--elevation-2)] hover:shadow-[var(--elevation-3)]",
        ghost: "border border-transparent bg-transparent shadow-none",
        link: "border border-transparent bg-transparent shadow-none underline-offset-4 hover:underline",
        glass: "relative overflow-hidden backdrop-blur-xl shadow-[var(--glass-shadow)] before:pointer-events-none before:absolute before:inset-0 before:bg-[radial-gradient(120%_120%_at_50%_-20%,_var(--glass-highlight)_0%,_transparent_55%)] before:opacity-80",
      },
      variant: {
        muted: "",
        primary: "",
        secondary: "",
        info: "",
        warning: "",
        success: "",
        destructive: "",
      },
      size: {
        default: "h-10 px-4",
        sm: "h-9 px-3 text-sm",
        lg: "h-12 px-6 text-base",
        icon: "size-10",
        iconSm: "size-9",
        iconLg: "size-12",
      },
      rounded: {
        rounded: "rounded",
        xs: "rounded-xs",
        sm: "rounded-sm",
        md: "rounded-md",
        lg: "rounded-lg",
        xl: "rounded-xl",
        "2xl": "rounded-2xl",
        "3xl": "rounded-3xl",
        full: "rounded-full",
      },
    },
    compoundVariants: Object.entries(buttonAppearanceVariantClasses).flatMap(([appearance, variants]) =>
      Object.entries(variants).map(([variant, className]) => ({
        appearance: appearance as ButtonAppearance,
        variant: variant as ButtonVariantName,
        class: className,
      })),
    ),
    defaultVariants: {
      appearance: "filled",
      variant: "primary",
      size: "default",
      rounded: "md",
    },
  },
);
export type ButtonVariants = VariantProps<typeof buttonVariants>;
