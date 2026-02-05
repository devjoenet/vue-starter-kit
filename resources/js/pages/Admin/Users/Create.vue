<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AdminLayout from "@/layouts/AdminLayout.vue";
  import { store } from "@/routes/admin/users";

  const { can } = useAbility();
  const canCreate = computed(() => can("users.create"));

  const form = useForm({
    name: "",
    email: "",
    password: "",
  });

  const submit = () => {
    if (!canCreate.value) return;
    form.post(store.url());
  };
</script>

<template>
  <AdminLayout title="Create User">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Create user</h1>
    </div>

    <Card class="mt-6 px-6">
      <form class="space-y-4" @submit.prevent="submit">
        <Input id="create-user-name" v-model="form.name" name="name" label="Name" variant="outlined" :disabled="!canCreate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

        <Input id="create-user-email" v-model="form.email" type="email" name="email" label="Email" variant="outlined" :disabled="!canCreate" :state="form.errors.email ? 'error' : 'default'" :message="form.errors.email" />

        <Input id="create-user-password" v-model="form.password" type="password" name="password" label="Password" variant="outlined" :disabled="!canCreate" :state="form.errors.password ? 'error' : 'default'" :message="form.errors.password" />

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
        </div>
      </form>
    </Card>
  </AdminLayout>
</template>
