import { tv } from 'tailwind-variants';

export const avatarVariants = tv({ base: 'relative flex size-8 shrink-0 overflow-hidden rounded-full' });
export const avatarImageVariants = tv({ base: 'aspect-square size-full' });
export const avatarFallbackVariants = tv({ base: 'flex size-full items-center justify-center rounded-full bg-muted' });
