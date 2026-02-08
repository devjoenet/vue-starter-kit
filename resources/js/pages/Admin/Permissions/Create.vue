<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import PermissionGroupSelect from "@/components/admin/PermissionGroupSelect.vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, index, store } from "@/routes/admin/permissions";
  import { type BreadcrumbItem } from "@/types";

  const { can } = useAbility();
  const canCreate = computed(() => can("permissions.create"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Permissions",
      href: index.url(),
    },
    {
      title: "Create",
      href: create.url(),
    },
  ];

  const form = useForm({
    name: "",
    group: "users",
  });

  const submit = () => {
    if (!canCreate.value) return;
    form.post(store.url());
  };
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Create permission</h1>
      </div>

      <Card variant="glass" class="px-6">
        <form class="space-y-4" @submit.prevent="submit">
          <Input id="create-permission-name" v-model="form.name" name="name" label="Permission name (e.g. users.view)" variant="outlined" :disabled="!canCreate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

          <PermissionGroupSelect id="create-permission-group" v-model="form.group" :disabled="!canCreate" :error="form.errors.group" />

          <div class="flex justify-end">
            <Button variant="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
          </div>
        </form>
      </Card>
    </div>
  </AppLayout>
</template>
