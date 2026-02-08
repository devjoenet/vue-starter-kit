import { cva } from "class-variance-authority";

export { default as Breadcrumb } from "./Breadcrumb.vue";
export { default as BreadcrumbEllipsis } from "./BreadcrumbEllipsis.vue";
export { default as BreadcrumbItem } from "./BreadcrumbItem.vue";
export { default as BreadcrumbLink } from "./BreadcrumbLink.vue";
export { default as BreadcrumbList } from "./BreadcrumbList.vue";
export { default as BreadcrumbPage } from "./BreadcrumbPage.vue";
export { default as BreadcrumbSeparator } from "./BreadcrumbSeparator.vue";

export const breadcrumbListVariants = cva("text-muted-foreground flex flex-wrap items-center gap-1.5 text-sm break-words sm:gap-2.5");
export const breadcrumbItemVariants = cva("inline-flex items-center gap-1.5");
export const breadcrumbLinkVariants = cva("hover:text-foreground transition-colors");
export const breadcrumbPageVariants = cva("text-foreground font-normal");
export const breadcrumbSeparatorVariants = cva("[&>svg]:size-3.5");
export const breadcrumbEllipsisVariants = cva("flex size-9 items-center justify-center");
