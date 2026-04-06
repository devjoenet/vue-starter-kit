export type AdminIndexQueryData = {
sort: string;
direction: string;
filters: Array<any>;
};
export type AssignableUserData = {
id: number;
name: string;
email: string;
};
export type AuthenticatedUserData = {
id: number;
name: string;
email: string;
email_verified_at?: string;
};
export type DashboardMetricSourceData = {
count: number;
};
export type DashboardOverviewSourceData = {
users: number;
roles: number;
permissions: number;
};
export type DashboardSourcesData = {
overview: DashboardOverviewSourceData;
users: DashboardMetricSourceData;
roles: DashboardMetricSourceData;
permissions: DashboardMetricSourceData;
};
export type EditableRoleData = {
id: number;
name: string;
};
export type EditableUserData = {
id: number;
name: string;
email: string;
};
export type PasswordUpdateRequest = {
    current_password: string;
    password: string;
};
export type PermissionGroupOptionData = {
slug: string;
label: string;
description?: string;
permissions_count: number;
};
export type PermissionIndexFilterOptionsData = {
group: Array<any>;
permission: Array<any>;
permission_check: Array<any>;
};
export type PermissionIndexItemData = {
id: number;
group: string;
group_label: string;
group_description?: string;
name: string;
label: string;
description?: string;
suffix: string;
};
export type PermissionItemData = {
id: number;
name: string;
label: string;
description?: string;
group: string;
group_label: string;
group_description?: string;
};
export type ProfileDeleteRequest = {
    password: string;
};
export type ProfileUpdateRequest = {
    name: string;
    email: string;
};
export type RoleIndexFilterOptionsData = {
display_name: Array<any>;
slug: Array<any>;
users: Array<any>;
};
export type RoleListItemData = {
id: number;
name: string;
users_count: number;
};
export type RoleOptionData = {
id: number;
name: string;
};
export type SharedAuthData = {
user?: AuthenticatedUserData;
roles: Array<any>;
permissions: Array<any>;
};
export type StorePermissionRequest = {
    name: string;
    label: string;
    description?: string;
    group: string;
    group_label: string;
    group_description?: string;
};
export type StoreRoleRequest = {
    name: string;
    user_ids?: Array<number>;
};
export type StoreUserRequest = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
};
export type SyncRolePermissionsRequest = {
    permissions?: Array<string>;
};
export type SyncUserRolesRequest = {
    roles?: Array<string>;
};
export type TwoFactorAuthenticationRequest = Record<string, never>;
export type UpdatePermissionRequest = {
    name: '""';
    label: string;
    description?: string;
    group: string;
    group_label: string;
    group_description?: string;
};
export type UpdateRoleRequest = {
    name: string;
};
export type UpdateUserRequest = {
    name: string;
    email: string;
    password?: string;
    password_confirmation?: string;
};
export type UserIndexFilterOptionsData = {
name: Array<any>;
email: Array<any>;
roles: Array<any>;
};
export type UserListItemData = {
id: number;
name: string;
email: string;
roles: Array<any>;
};
