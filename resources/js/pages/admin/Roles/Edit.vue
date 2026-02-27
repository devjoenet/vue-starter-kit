<script setup lang="ts">
  import { router, useForm } from "@inertiajs/vue3";
  import { ChevronDown } from "lucide-vue-next";
  import { h, computed, watch } from "vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Checkbox from "@/components/ui/checkbox/Checkbox.vue";
  import Collapsible from "@/components/ui/collapsible/Collapsible.vue";
  import CollapsibleContent from "@/components/ui/collapsible/CollapsibleContent.vue";
  import CollapsibleTrigger from "@/components/ui/collapsible/CollapsibleTrigger.vue";
  import Input from "@/components/ui/input/Input.vue";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { toKebabCase, toTitleCase } from "@/lib/utils";
  import { dashboard } from "@/routes/admin";
  import { destroy, index, update } from "@/routes/admin/roles";
  import { sync } from "@/routes/admin/roles/permissions";
  import type { App } from "@/wayfinder/types";
  defineOptions({
    layout: (page: unknown) =>
      h(
        AppLayout,
        {
          breadcrumbs: [{ title: "Dashboard", href: dashboard.url() }, { title: "Roles", href: index.url() }, { title: "Edit" }],
        },
        () => page,
      ),
  });
  const props = defineProps<{
    roleId: number;
    roleName: string;
    permissionsByGroup: Record<string, { id: number; name: string; group: string }[]>;
    rolePermissions: string[];
  }>();

  const { can } = useAbility();
  const canUpdate = computed(() => can("roles.update"));
  const canDelete = computed(() => can("roles.delete"));
  const canAssign = computed(() => can("roles.assignPermissions"));

  const roleForm = useForm<App["Forms"]["Admin"]["Roles"]["Update"]>({ name: props.roleName });
  const permsForm = useForm<App["Forms"]["Admin"]["Roles"]["SyncPermissions"]>({ permissions: [...props.rolePermissions] });

  watch(
    () => props.roleName,
    (roleName) => {
      roleForm.name = roleName;
    },
    { immediate: true },
  );

  const updateRole = () => {
    if (!canUpdate.value) return;

    roleForm.name = toKebabCase(roleForm.name);
    roleForm.put(update.url(props.roleId), { preserveScroll: true });
  };

  const syncPermissions = () => {
    if (!canAssign.value) return;
    permsForm.put(sync.url(props.roleId), { preserveScroll: true });
  };

  const destroyRole = () => {
    if (!canDelete.value) return;
    if (!confirm("Delete this role?")) return;
    router.delete(destroy.url(props.roleId));
  };

  const togglePermission = (name: string) => {
    const idx = permsForm.permissions.indexOf(name);
    if (idx >= 0) permsForm.permissions.splice(idx, 1);
    else permsForm.permissions.push(name);
  };
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Edit {{ toTitleCase(props.roleName) }}</h1>
      <Button appearance="outline" variant="destructive" :disabled="!canDelete" @click="destroyRole">Delete Role</Button>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
      <Card variant="default" class="px-6">
        <h2 class="text-lg font-semibold">Role Details</h2>

        <form class="mt-4 space-y-4" @submit.prevent="updateRole">
          <Input id="edit-role-name" :default-value="roleForm.name" v-model="roleForm.name" name="name" label="Role Name" variant="outlined" :disabled="!canUpdate" :state="roleForm.errors.name ? 'error' : 'default'" :message="roleForm.errors.name" />

          <div class="flex justify-end">
            <Button appearance="filled" type="submit" :disabled="!canUpdate || roleForm.processing">Save Role</Button>
          </div>
        </form>
      </Card>

      <Card variant="default" class="px-6">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Permissions</h2>
          <Button appearance="filled" :disabled="!canAssign || permsForm.processing" @click="syncPermissions">Refresh Permissions</Button>
        </div>

        <div class="mt-4 space-y-3">
          <Collapsible v-for="(items, group) in permissionsByGroup" :key="group" v-slot="{ open }" :default-open="false" class="rounded-xl border border-black/5 px-3 py-2 dark:border-white/10">
            <div class="flex items-center justify-between gap-3">
              <div class="text-sm font-semibold capitalize">{{ group }}</div>

              <CollapsibleTrigger as-child>
                <Button appearance="text" size="sm" class="gap-2">
                  <span>{{ open ? "Collapse" : "Expand" }}</span>
                  <ChevronDown class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" />
                </Button>
              </CollapsibleTrigger>
            </div>

            <CollapsibleContent class="mt-2">
              <div class="space-y-2">
                <label v-for="p in items" :key="p.id" class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10" :class="!canAssign ? 'opacity-60' : ''">
                  <Checkbox :disabled="!canAssign" :model-value="permsForm.permissions.includes(p.name)" @update:model-value="() => togglePermission(p.name)" />
                  <span class="text-sm">{{ p.name }}</span>
                </label>
              </div>
            </CollapsibleContent>
          </Collapsible>
        </div>
      </Card>
    </div>
  </div>
</template>
