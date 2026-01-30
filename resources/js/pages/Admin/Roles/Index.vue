<script setup lang="ts">
  import { Link } from "@inertiajs/vue3";
  import { computed } from "vue";
  import Button from "@/components/md3/Button.vue";
  import Card from "@/components/md3/Card.vue";
  import { useAbility } from "@/composables/useAbility";
  import AdminLayout from "@/layouts/AdminLayout.vue";
  import { create, edit } from "@/routes/admin/roles";

  const props = defineProps<{
    roles: { id: number; name: string }[];
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("roles.create"));
  const canUpdate = computed(() => can("roles.update"));
</script>

<template>
  <AdminLayout title="Roles">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Roles</h1>

      <Link v-if="canCreate" :href="create.url()">
        <Button variant="filled">New role</Button>
      </Link>
    </div>

    <Card class="mt-6" :elevation="1">
      <div class="space-y-2">
        <div v-for="r in props.roles" :key="r.id" class="flex items-center justify-between rounded-xl border border-black/5 p-3 dark:border-white/10">
          <div class="text-sm font-medium">{{ r.name }}</div>

          <Link v-if="canUpdate" :href="edit.url(r.id)">
            <Button variant="text">Edit</Button>
          </Link>
        </div>
      </div>
    </Card>
  </AdminLayout>
</template>
