<script setup lang="ts">
  import { Form } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, index, store } from "@/routes/admin/roles";
  import { type BreadcrumbItem } from "@/types";

  const { can } = useAbility();
  const canCreate = computed(() => can("roles.create"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Roles",
      href: index.url(),
    },
    {
      title: "Create",
      href: create.url(),
    },
  ];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Create role</h1>
      </div>

      <Card variant="glass" class="px-6">
        <Form v-bind="store.form()" class="space-y-4" #default="{ errors, processing }">
          <Input id="create-role-name" name="name" label="Role name" variant="outlined" :disabled="!canCreate" :state="errors.name ? 'error' : 'default'" :message="errors.name" />

          <div class="flex justify-end">
            <Button appearance="filled" type="submit" :disabled="!canCreate || processing"> Create </Button>
          </div>
        </Form>
      </Card>
    </div>
  </AppLayout>
</template>
