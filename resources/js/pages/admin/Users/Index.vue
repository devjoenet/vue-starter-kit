<script setup lang="ts">
  import { Link, usePage } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Badge } from "@/components/ui/badge";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, destroy, edit, index } from "@/routes/admin/users";
  import { type BreadcrumbItem } from "@/types";
  import { PenBoxIcon, TrashIcon } from "lucide-vue-next";

  const props = defineProps<{
    users: any;
  }>();

  const { can } = useAbility();
  const page = usePage();

  const canCreate = computed(() => can("users.create"));
  const canUpdate = computed(() => can("users.update"));
  const canDelete = computed(() => can("users.delete"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Users",
      href: index.url(),
    },
  ];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 px-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Users</h1>

        <Button v-if="canCreate" appearance="outline" as-child>
          <Link :href="create.url()">Create New User</Link>
        </Button>
      </div>

      <Card variant="default" class="overflow-hidden py-0">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Name</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Roles</TableHead>
              <TableHead class="w-[1%] text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="user in users.data" :key="user.id">
              <TableCell class="font-medium">{{ user.name }}</TableCell>
              <TableCell class="text-muted-foreground">{{ user.email }}</TableCell>
              <TableCell>
                <div class="flex flex-wrap gap-1.5">
                  <Badge v-for="role in user.roles" :key="`${user.id}-${role}`" variant="secondary">
                    {{ role }}
                  </Badge>
                  <span v-if="!user.roles?.length" class="text-muted-foreground text-xs">No roles</span>
                </div>
              </TableCell>
              <TableCell>
                <div class="flex items-center justify-end gap-2">
                  <Button v-if="canUpdate" appearance="outline" size="sm" as-child>
                    <Link :href="edit.url(user.id)">
                      <PenBoxIcon />
                    </Link>
                  </Button>

                  <form v-if="canDelete" class="inline-flex" method="post" :action="destroy.url(user.id)">
                    <input type="hidden" name="_method" value="delete" />
                    <input type="hidden" name="_token" :value="page.props.csrf_token" />
                    <Button type="submit" variant="destructive" appearance="outline" size="sm">
                      <TrashIcon />
                    </Button>
                  </form>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </Card>
    </div>
  </AppLayout>
</template>
