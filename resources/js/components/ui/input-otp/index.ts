import { cva } from "class-variance-authority";

export { default as InputOTP } from "./InputOTP.vue";
export { default as InputOTPGroup } from "./InputOTPGroup.vue";
export { default as InputOTPSeparator } from "./InputOTPSeparator.vue";
export { default as InputOTPSlot } from "./InputOTPSlot.vue";

export const inputOtpContainerVariants = cva("flex items-center gap-2 has-disabled:opacity-50");
export const inputOtpGroupVariants = cva("flex items-center");
export const inputOtpSlotVariants = cva(
  "data-[active=true]:border-ring data-[active=true]:ring-ring/50 data-[active=true]:aria-invalid:ring-destructive/20 dark:data-[active=true]:aria-invalid:ring-destructive/40 aria-invalid:border-destructive data-[active=true]:aria-invalid:border-destructive dark:bg-input/30 border-input relative flex h-9 w-9 items-center justify-center border-y border-r text-sm shadow-xs transition-all outline-none first:rounded-l-md first:border-l last:rounded-r-md data-[active=true]:z-10 data-[active=true]:ring-[3px]",
);
export const inputOtpSeparatorVariants = cva("text-muted-foreground");
