import type { VariantProps } from "class-variance-authority";
import { cva } from "class-variance-authority";
import type { InputVariants } from "@/components/ui/input";

export { default as Signature } from "./Signature.vue";

export type SignatureDataUrlType = "png" | "jpg";

export type SignatureVariants = {
  variant?: InputVariants["variant"];
  state?: InputVariants["state"];
};

export const signatureSurfaceVariants = cva("relative w-full overflow-hidden rounded-[var(--radius-sm)] border bg-muted/20 transition-colors", {
  variants: {
    state: {
      default: "border-[color:var(--outline)]",
      error: "border-[var(--error)]",
      destructive: "border-destructive",
      info: "border-info",
      warning: "border-warning",
      success: "border-success",
    },
  },
  defaultVariants: {
    state: "default",
  },
});

export type SignatureSurfaceVariants = VariantProps<typeof signatureSurfaceVariants>;
