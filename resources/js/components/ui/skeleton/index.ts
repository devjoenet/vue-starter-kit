import { cva } from "class-variance-authority";

export { default as Skeleton } from "./Skeleton.vue";

export const skeletonVariants = cva("animate-pulse rounded-md bg-primary/10");
