<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, index, store } from "@/routes/admin/users";
  import { type BreadcrumbItem } from "@/types";

  const { can } = useAbility();
  const canCreate = computed(() => can("users.create"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Users",
      href: index.url(),
    },
    {
      title: "Create",
      href: create.url(),
    },
  ];

  const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });

  const submit = () => {
    if (!canCreate.value) return;
    form.post(store.url());
  };
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 px-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Create user</h1>
      </div>

      <Card variant="default" class="px-6">
        <form class="space-y-4" @submit.prevent="submit">
          <Input id="create-user-name" v-model="form.name" name="name" label="Name" variant="outlined" :disabled="!canCreate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

          <Input id="create-user-email" v-model="form.email" type="email" name="email" label="Email" variant="outlined" :disabled="!canCreate" :state="form.errors.email ? 'error' : 'default'" :message="form.errors.email" />

          <Input id="create-user-password" v-model="form.password" type="password" name="password" label="Password" variant="outlined" :disabled="!canCreate" :state="form.errors.password ? 'error' : 'default'" :message="form.errors.password" />
          <Input
            id="create-user-password-confirmation"
            v-model="form.password_confirmation"
            type="password"
            name="password_confirmation"
            label="Confirm password"
            variant="outlined"
            :disabled="!canCreate"
            :state="form.errors.password_confirmation ? 'error' : 'default'"
            :message="form.errors.password_confirmation"
          />

          <div class="flex justify-end">
            <Button appearance="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
          </div>
        </form>
      </Card>
    </div>
  </AppLayout>
</template>
