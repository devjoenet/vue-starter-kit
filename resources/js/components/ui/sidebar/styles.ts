import type { VariantProps } from 'class-variance-authority';
import { cva } from 'class-variance-authority';
import type { HTMLAttributes } from 'vue';

export interface SidebarProps {
  side?: 'left' | 'right';
  variant?: 'sidebar' | 'floating' | 'inset';
  collapsible?: 'offcanvas' | 'icon' | 'none';
  class?: HTMLAttributes['class'];
}

export { useSidebar } from './utils';

export const sidebarWrapperVariants = cva(
  'group/sidebar-wrapper flex min-h-svh w-full has-data-[variant=inset]:bg-sidebar',
);
export const sidebarContentVariants = cva(
  'flex min-h-0 flex-1 flex-col gap-2 overflow-auto group-data-[collapsible=icon]:overflow-hidden',
);
export const sidebarHeaderVariants = cva('flex flex-col gap-2 p-2');
export const sidebarFooterVariants = cva('flex flex-col gap-2 p-2');
export const sidebarGroupVariants = cva(
  'relative flex w-full min-w-0 flex-col p-2',
);
export const sidebarGroupContentVariants = cva('w-full text-sm');
export const sidebarInputVariants = cva('h-8 w-full bg-background shadow-none');
export const sidebarMenuVariants = cva('flex w-full min-w-0 flex-col gap-1');
export const sidebarMenuItemVariants = cva('group/menu-item relative');
export const sidebarMenuSkeletonVariants = cva(
  'flex h-8 items-center gap-2 rounded-md px-2',
);
export const sidebarMenuSubVariants = cva(
  'mx-3.5 flex min-w-0 translate-x-px flex-col gap-1 border-l border-sidebar-border px-2.5 py-0.5',
);
export const sidebarMenuSubItemVariants = cva('group/menu-sub-item relative');
export const sidebarSeparatorVariants = cva('mx-2 w-auto bg-sidebar-border');
export const sidebarTriggerVariants = cva('h-11 w-11');
export const sidebarMenuSubButtonVariants = cva(
  'flex h-7 min-w-0 -translate-x-px items-center gap-2 overflow-hidden rounded-md px-2 text-sidebar-foreground ring-sidebar-ring outline-hidden hover:bg-sidebar-accent hover:text-sidebar-accent-foreground focus-visible:ring-2 active:bg-sidebar-accent active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 aria-disabled:pointer-events-none aria-disabled:opacity-50 [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0 [&>svg]:text-sidebar-accent-foreground',
  {
    variants: {
      size: {
        sm: 'text-xs',
        md: 'text-sm',
      },
      active: {
        true: 'bg-sidebar-accent text-sidebar-accent-foreground',
        false: '',
      },
    },
    defaultVariants: {
      size: 'md',
      active: false,
    },
  },
);

export const sidebarContainerVariants = cva(
  'flex h-full w-(--sidebar-width) flex-col bg-sidebar text-sidebar-foreground',
);
export const sidebarMobileContentVariants = cva(
  'w-(--sidebar-width) bg-sidebar p-0 text-sidebar-foreground [&>button]:hidden',
);
export const sidebarDesktopGapVariants = cva(
  'relative w-(--sidebar-width) bg-transparent transition-[width] duration-200 ease-linear motion-reduce:transition-none',
);
export const sidebarDesktopContainerVariants = cva(
  'fixed inset-y-0 z-10 hidden h-svh w-(--sidebar-width) transition-[left,right,width] duration-200 ease-linear motion-reduce:transition-none md:flex',
);
export const sidebarDesktopPanelVariants = cva(
  'flex h-full w-full flex-col bg-sidebar group-data-[variant=floating]:rounded-lg group-data-[variant=floating]:border group-data-[variant=floating]:border-sidebar-border group-data-[variant=floating]:shadow-sm',
);

export const sidebarMenuButtonVariants = cva(
  'peer/menu-button flex w-full items-center gap-2 overflow-hidden rounded-md p-2 text-left text-sm ring-sidebar-ring outline-hidden transition-[background-color,color,box-shadow,transform] duration-200 group-has-data-[sidebar=menu-action]/menu-item:pr-8 group-data-[collapsible=icon]:size-8! group-data-[collapsible=icon]:p-2! hover:bg-sidebar-accent hover:text-sidebar-accent-foreground focus-visible:ring-2 active:bg-sidebar-accent active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 aria-disabled:pointer-events-none aria-disabled:opacity-50 data-[active=true]:bg-sidebar-accent data-[active=true]:font-medium data-[active=true]:text-sidebar-accent-foreground data-[state=open]:hover:bg-sidebar-accent data-[state=open]:hover:text-sidebar-accent-foreground motion-reduce:transition-none [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0',
  {
    variants: {
      variant: {
        default: 'hover:bg-sidebar-accent hover:text-sidebar-accent-foreground',
        outline:
          'bg-background shadow-[0_0_0_1px_hsl(var(--sidebar-border))] hover:bg-sidebar-accent hover:text-sidebar-accent-foreground hover:shadow-[0_0_0_1px_hsl(var(--sidebar-accent))]',
      },
      size: {
        default: 'h-8 text-sm',
        sm: 'h-7 text-xs',
        lg: 'h-12 text-sm group-data-[collapsible=icon]:p-0!',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  },
);

export type SidebarMenuButtonVariants = VariantProps<
  typeof sidebarMenuButtonVariants
>;
