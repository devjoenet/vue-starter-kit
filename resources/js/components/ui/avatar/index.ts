import { cva } from "class-variance-authority";

export { default as Avatar } from "./Avatar.vue";
export { default as AvatarFallback } from "./AvatarFallback.vue";
export { default as AvatarImage } from "./AvatarImage.vue";

export const avatarVariants = cva("relative flex size-8 shrink-0 overflow-hidden rounded-full");
export const avatarImageVariants = cva("aspect-square size-full");
export const avatarFallbackVariants = cva("bg-muted flex size-full items-center justify-center rounded-full");
