import { tv } from 'tailwind-variants';

export const inputOtpContainerVariants = tv({ base: 'flex items-center gap-2 has-disabled:opacity-50' });
export const inputOtpGroupVariants = tv({ base: 'flex items-center' });
export const inputOtpSlotVariants = tv({
  base: 'relative flex h-11 w-11 items-center justify-center border-y border-r border-input text-sm shadow-xs transition-[border-color,box-shadow,background-color,color] outline-none first:rounded-l-md first:border-l last:rounded-r-md aria-invalid:border-destructive data-[active=true]:z-10 data-[active=true]:border-ring data-[active=true]:ring-[3px] data-[active=true]:ring-ring/50 data-[active=true]:aria-invalid:border-destructive data-[active=true]:aria-invalid:ring-destructive/20 dark:bg-input/30 dark:data-[active=true]:aria-invalid:ring-destructive/40',
});
export const inputOtpSeparatorVariants = tv({ base: 'text-muted-foreground' });
