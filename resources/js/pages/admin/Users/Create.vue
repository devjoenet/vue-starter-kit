<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { h, computed } from "vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Input from "@/components/ui/input/Input.vue";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin";
  import { create, index, store } from "@/routes/admin/users";
  import type { App } from "@/wayfinder/types";
  defineOptions({
    layout: (_: unknown, page: unknown) =>
      h(
        AppLayout,
        {
          breadcrumbs: [
            { title: "Dashboard", href: dashboard.url() },
            { title: "Users", href: index.url() },
            { title: "Create", href: create.url() },
          ],
        },
        () => page,
      ),
  });
  const { can } = useAbility();
  const canCreate = computed(() => can("users.create"));

  const form = useForm<App["Forms"]["Admin"]["Users"]["Store"]>({
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
</template>
