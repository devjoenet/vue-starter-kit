<script setup lang="ts">
  import { Link } from "@inertiajs/vue3";
  import { h, computed } from "vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Table from "@/components/ui/table/Table.vue";
  import TableBody from "@/components/ui/table/TableBody.vue";
  import TableCell from "@/components/ui/table/TableCell.vue";
  import TableHead from "@/components/ui/table/TableHead.vue";
  import TableHeader from "@/components/ui/table/TableHeader.vue";
  import TableRow from "@/components/ui/table/TableRow.vue";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { toTitleCase } from "@/lib/utils";
  import { dashboard } from "@/routes/admin";
  import { create, edit, index } from "@/routes/admin/roles";
  import { SquarePenIcon } from "lucide-vue-next";
  defineOptions({
    layout: (page: unknown) =>
      h(
        AppLayout,
        {
          breadcrumbs: [
            { title: "Dashboard", href: dashboard.url() },
            { title: "Roles", href: index.url() },
          ],
        },
        () => page,
      ),
  });

  const props = defineProps<{
    roles: { id: number; name: string; users_count: number }[];
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("roles.create"));
  const canUpdate = computed(() => can("roles.update"));
</script>

<template>
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
</template>
