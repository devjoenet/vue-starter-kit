import { tv } from 'tailwind-variants';

export const breadcrumbListVariants = tv({ base: 'flex flex-wrap items-center gap-1.5 text-sm break-words text-muted-foreground sm:gap-2.5' });
export const breadcrumbItemVariants = tv({ base: 'inline-flex items-center gap-1.5' });
export const breadcrumbLinkVariants = tv({ base: 'transition-colors hover:text-foreground' });
export const breadcrumbPageVariants = tv({ base: 'font-normal text-foreground' });
export const breadcrumbSeparatorVariants = tv({ base: '[&>svg]:size-3.5' });
export const breadcrumbEllipsisVariants = tv({ base: 'flex size-9 items-center justify-center' });
