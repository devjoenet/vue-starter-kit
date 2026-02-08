import type { VariantProps } from "class-variance-authority";
import type { HTMLAttributes } from "vue";
import { cva } from "class-variance-authority";

export interface SidebarProps {
  side?: "left" | "right";
  variant?: "sidebar" | "floating" | "inset";
  collapsible?: "offcanvas" | "icon" | "none";
  class?: HTMLAttributes["class"];
}

export { default as Sidebar } from "./Sidebar.vue";
export { default as SidebarContent } from "./SidebarContent.vue";
export { default as SidebarFooter } from "./SidebarFooter.vue";
export { default as SidebarGroup } from "./SidebarGroup.vue";
export { default as SidebarGroupAction } from "./SidebarGroupAction.vue";
export { default as SidebarGroupContent } from "./SidebarGroupContent.vue";
export { default as SidebarGroupLabel } from "./SidebarGroupLabel.vue";
export { default as SidebarHeader } from "./SidebarHeader.vue";
export { default as SidebarInput } from "./SidebarInput.vue";
export { default as SidebarInset } from "./SidebarInset.vue";
export { default as SidebarMenu } from "./SidebarMenu.vue";
export { default as SidebarMenuAction } from "./SidebarMenuAction.vue";
export { default as SidebarMenuBadge } from "./SidebarMenuBadge.vue";
export { default as SidebarMenuButton } from "./SidebarMenuButton.vue";
export { default as SidebarMenuItem } from "./SidebarMenuItem.vue";
export { default as SidebarMenuSkeleton } from "./SidebarMenuSkeleton.vue";
export { default as SidebarMenuSub } from "./SidebarMenuSub.vue";
export { default as SidebarMenuSubButton } from "./SidebarMenuSubButton.vue";
export { default as SidebarMenuSubItem } from "./SidebarMenuSubItem.vue";
export { default as SidebarProvider } from "./SidebarProvider.vue";
export { default as SidebarRail } from "./SidebarRail.vue";
export { default as SidebarSeparator } from "./SidebarSeparator.vue";
export { default as SidebarTrigger } from "./SidebarTrigger.vue";

export { useSidebar } from "./utils";

export const sidebarWrapperVariants = cva("group/sidebar-wrapper has-data-[variant=inset]:bg-sidebar flex min-h-svh w-full");
export const sidebarContentVariants = cva("flex min-h-0 flex-1 flex-col gap-2 overflow-auto group-data-[collapsible=icon]:overflow-hidden");
export const sidebarHeaderVariants = cva("flex flex-col gap-2 p-2");
export const sidebarFooterVariants = cva("flex flex-col gap-2 p-2");
export const sidebarGroupVariants = cva("relative flex w-full min-w-0 flex-col p-2");
export const sidebarGroupContentVariants = cva("w-full text-sm");
export const sidebarInputVariants = cva("bg-background h-8 w-full shadow-none");
export const sidebarMenuVariants = cva("flex w-full min-w-0 flex-col gap-1");
export const sidebarMenuItemVariants = cva("group/menu-item relative");
export const sidebarMenuSkeletonVariants = cva("flex h-8 items-center gap-2 rounded-md px-2");
export const sidebarMenuSubVariants = cva("border-sidebar-border mx-3.5 flex min-w-0 translate-x-px flex-col gap-1 border-l px-2.5 py-0.5");
export const sidebarMenuSubItemVariants = cva("group/menu-sub-item relative");
export const sidebarSeparatorVariants = cva("bg-sidebar-border mx-2 w-auto");
export const sidebarTriggerVariants = cva("h-7 w-7");
export const sidebarMenuSubButtonVariants = cva(
  "text-sidebar-foreground ring-sidebar-ring hover:bg-sidebar-accent hover:text-sidebar-accent-foreground active:bg-sidebar-accent active:text-sidebar-accent-foreground [&>svg]:text-sidebar-accent-foreground flex h-7 min-w-0 -translate-x-px items-center gap-2 overflow-hidden rounded-md px-2 outline-hidden focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-50 aria-disabled:pointer-events-none aria-disabled:opacity-50 [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0",
  {
    variants: {
      size: {
        sm: "text-xs",
        md: "text-sm",
      },
      active: {
        true: "bg-sidebar-accent text-sidebar-accent-foreground",
        false: "",
      },
    },
    defaultVariants: {
      size: "md",
      active: false,
    },
  },
);

export const sidebarContainerVariants = cva("bg-sidebar text-sidebar-foreground flex h-full w-(--sidebar-width) flex-col");
export const sidebarMobileContentVariants = cva("bg-sidebar text-sidebar-foreground w-(--sidebar-width) p-0 [&>button]:hidden");
export const sidebarDesktopGapVariants = cva("relative w-(--sidebar-width) bg-transparent transition-[width] duration-200 ease-linear");
export const sidebarDesktopContainerVariants = cva("fixed inset-y-0 z-10 hidden h-svh w-(--sidebar-width) transition-[left,right,width] duration-200 ease-linear md:flex");
export const sidebarDesktopPanelVariants = cva("bg-sidebar group-data-[variant=floating]:border-sidebar-border flex h-full w-full flex-col group-data-[variant=floating]:rounded-lg group-data-[variant=floating]:border group-data-[variant=floating]:shadow-sm");

export const sidebarMenuButtonVariants = cva(
  "peer/menu-button flex w-full items-center gap-2 overflow-hidden rounded-md p-2 text-left text-sm outline-hidden ring-sidebar-ring transition-[width,height,padding] hover:bg-sidebar-accent hover:text-sidebar-accent-foreground focus-visible:ring-2 active:bg-sidebar-accent active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 group-has-data-[sidebar=menu-action]/menu-item:pr-8 aria-disabled:pointer-events-none aria-disabled:opacity-50 data-[active=true]:bg-sidebar-accent data-[active=true]:font-medium data-[active=true]:text-sidebar-accent-foreground data-[state=open]:hover:bg-sidebar-accent data-[state=open]:hover:text-sidebar-accent-foreground group-data-[collapsible=icon]:size-8! group-data-[collapsible=icon]:p-2! [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0",
  {
    variants: {
      variant: {
        default: "hover:bg-sidebar-accent hover:text-sidebar-accent-foreground",
        outline: "bg-background shadow-[0_0_0_1px_hsl(var(--sidebar-border))] hover:bg-sidebar-accent hover:text-sidebar-accent-foreground hover:shadow-[0_0_0_1px_hsl(var(--sidebar-accent))]",
      },
      size: {
        default: "h-8 text-sm",
        sm: "h-7 text-xs",
        lg: "h-12 text-sm group-data-[collapsible=icon]:p-0!",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
);

export type SidebarMenuButtonVariants = VariantProps<typeof sidebarMenuButtonVariants>;
