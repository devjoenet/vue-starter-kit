<script setup lang="ts">
  import { Link } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { toTitleCase } from "@/lib/utils";
  import { dashboard } from "@/routes/admin/index";
  import { create, edit, index } from "@/routes/admin/roles";
  import { type BreadcrumbItem } from "@/types";
  import { SquarePenIcon } from "lucide-vue-next";

  const props = defineProps<{
    roles: { id: number; name: string; users_count: number }[];
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("roles.create"));
  const canUpdate = computed(() => can("roles.update"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Roles",
      href: index.url(),
    },
  ];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 px-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Roles</h1>

        <Button v-if="canCreate" appearance="outline" as-child>
          <Link :href="create.url()">Create New Role</Link>
        </Button>
      </div>

      <Card variant="default" class="overflow-hidden py-0">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Display Name</TableHead>
              <TableHead>Slug</TableHead>
              <TableHead>Users</TableHead>
              <TableHead class="w-[1%] text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="role in props.roles" :key="role.id">
              <TableCell class="font-medium">{{ toTitleCase(role.name) }}</TableCell>
              <TableCell class="text-muted-foreground text-xs font-medium italic">{{ role.name }}</TableCell>
              <TableCell class="text-muted-foreground">{{ role.users_count }}</TableCell>
              <TableCell class="text-right">
                <Button v-if="canUpdate" appearance="outline" size="sm" as-child>
                  <Link :href="edit.url(role.id)">
                    <SquarePenIcon />
                  </Link>
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </Card>
    </div>
  </AppLayout>
</template>
