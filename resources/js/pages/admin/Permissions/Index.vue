<script setup lang="ts">
  import { Link, useForm } from "@inertiajs/vue3";
  import { computed, ref } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { Select } from "@/components/ui/select";
  import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, destroy, edit, index } from "@/routes/admin/permissions/permissions";
  import { type BreadcrumbItem } from "@/types";
  import { TrashIcon, SquarePenIcon } from "lucide-vue-next";
  import { toTitleCase } from "@/lib/utils";

  const props = defineProps<{
    permissionsByGroup: Record<string, { id: number; name: string; group: string }[]>;
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("permissions.create"));
  const canUpdate = computed(() => can("permissions.update"));
  const canDelete = computed(() => can("permissions.delete"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Permissions",
      href: index.url(),
    },
  ];

  const del = useForm({});
  const search = ref("");
  const groupFilter = ref("");
  const sortDirections = ref<{
    group: "none" | "asc" | "desc";
    name: "none" | "asc" | "desc";
  }>({
    group: "asc",
    name: "asc",
  });

  const permissionRows = computed(() =>
    Object.entries(props.permissionsByGroup).flatMap(([group, items]) =>
      items.map((permission) => ({
        ...permission,
        group,
        suffix: permission.name.startsWith(`${group}.`) ? permission.name.slice(group.length + 1) : permission.name,
      })),
    ),
  );

  const groupOptions = computed(() => {
    const groups = Array.from(new Set(permissionRows.value.map((permission) => permission.group))).sort((left, right) => left.localeCompare(right));

    return [
      { value: "", label: "" },
      ...groups.map((group) => ({
        value: group,
        label: group.charAt(0).toUpperCase() + group.slice(1),
      })),
    ];
  });

  const filteredRows = computed(() => {
    const searchTerm = search.value.trim().toLowerCase();

    return permissionRows.value.filter((permission) => {
      const matchesGroup = !groupFilter.value || permission.group === groupFilter.value;

      if (!matchesGroup) {
        return false;
      }

      if (!searchTerm) {
        return true;
      }

      return permission.name.toLowerCase().includes(searchTerm) || permission.suffix.toLowerCase().includes(searchTerm) || permission.group.toLowerCase().includes(searchTerm);
    });
  });

  const sortedRows = computed(() =>
    [...filteredRows.value].sort((left, right) => {
      if (sortDirections.value.group !== "none") {
        const groupComparison = left.group.localeCompare(right.group);
        if (groupComparison !== 0) {
          return sortDirections.value.group === "asc" ? groupComparison : groupComparison * -1;
        }
      }

      if (sortDirections.value.name !== "none") {
        const nameComparison = left.suffix.localeCompare(right.suffix);
        if (nameComparison !== 0) {
          return sortDirections.value.name === "asc" ? nameComparison : nameComparison * -1;
        }
      }

      return 0;
    }),
  );

  const toggleSort = (column: "name" | "group") => {
    const current = sortDirections.value[column];

    if (current === "none") {
      sortDirections.value[column] = "asc";
      return;
    }

    if (current === "asc") {
      sortDirections.value[column] = "desc";
      return;
    }

    sortDirections.value[column] = "none";
  };

  const destroyPermission = (id: number) => {
    if (!canDelete.value) return;
    if (!confirm("Delete this permission?")) return;
    del.delete(destroy.url(id));
  };
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 px-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Permissions</h1>

        <Button v-if="canCreate" appearance="outline" as-child>
          <Link :href="create.url()">Create New Permission</Link>
        </Button>
      </div>

      <Card variant="default" class="overflow-hidden py-0">
        <div class="border-b border-border/60 px-4 py-4">
          <div class="grid gap-3 md:grid-cols-[1fr_16rem_auto] md:items-end">
            <Input id="permissions-search" v-model="search" placeholder="Search permission or group..." variant="outlined" />
            <Select id="permissions-group-filter" v-model="groupFilter" :options="groupOptions" label="Group" placeholder="" variant="outlined" />
            <div class="h-fit md:text-right self-center flex justify-items-center">
              <p class="text-lg font-semibold leading-tight">{{ sortedRows.length }} result{{ sortedRows.length === 1 ? "" : "s" }}</p>
            </div>
          </div>
        </div>

        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>
                <button type="button" class="inline-flex items-center gap-1" @click="toggleSort('group')">
                  Group
                  <span class="text-[10px]">
                    {{ sortDirections.group === "none" ? "⇅" : sortDirections.group === "asc" ? "▲" : "▼" }}
                  </span>
                </button>
              </TableHead>
              <TableHead>
                <button type="button" class="inline-flex items-center gap-1" @click="toggleSort('name')">
                  Permission
                  <span class="text-[10px]">
                    {{ sortDirections.name === "none" ? "⇅" : sortDirections.name === "asc" ? "▲" : "▼" }}
                  </span>
                </button>
              </TableHead>
              <TableHead>Permission to Check</TableHead>
              <TableHead class="w-[1%] text-left">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="permission in sortedRows" :key="permission.id">
              <TableCell class="text-muted-foreground">{{ toTitleCase(permission.group) }}</TableCell>
              <TableCell class="font-medium">{{ toTitleCase(permission.suffix) }}</TableCell>
              <TableCell class="text-muted-foreground">{{ permission.name }}</TableCell>
              <TableCell>
                <div class="flex items-center justify-end gap-2">
                  <Button v-if="canUpdate" appearance="outline" size="sm" as-child>
                    <Link :href="edit.url(permission.id)">
                      <SquarePenIcon />
                    </Link>
                  </Button>

                  <Button v-if="canDelete" appearance="outline" variant="destructive" size="sm" @click="destroyPermission(permission.id)">
                    <TrashIcon />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="!sortedRows.length">
              <TableCell colspan="4" class="text-muted-foreground text-center">No permissions match the current filters.</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </Card>
    </div>
  </AppLayout>
</template>
