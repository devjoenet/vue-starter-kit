import { cva } from "class-variance-authority";

export const breadcrumbListVariants = cva("text-muted-foreground flex flex-wrap items-center gap-1.5 text-sm break-words sm:gap-2.5");
export const breadcrumbItemVariants = cva("inline-flex items-center gap-1.5");
export const breadcrumbLinkVariants = cva("hover:text-foreground transition-colors");
export const breadcrumbPageVariants = cva("text-foreground font-normal");
export const breadcrumbSeparatorVariants = cva("[&>svg]:size-3.5");
export const breadcrumbEllipsisVariants = cva("flex size-9 items-center justify-center");
