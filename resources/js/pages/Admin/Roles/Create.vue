<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AdminLayout from "@/layouts/AdminLayout.vue";
  import { store } from "@/routes/admin/roles";

  const { can } = useAbility();
  const canCreate = computed(() => can("roles.create"));

  const form = useForm({ name: "" });

  const submit = () => {
    if (!canCreate.value) return;
    form.post(store.url());
  };
</script>

<template>
  <AdminLayout title="Create Role">
    <h1 class="text-2xl font-semibold">Create role</h1>

    <Card class="mt-6 px-6">
      <form class="space-y-4" @submit.prevent="submit">
        <Input id="create-role-name" v-model="form.name" name="name" label="Role name" variant="outlined" :disabled="!canCreate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
        </div>
      </form>
    </Card>
  </AdminLayout>
</template>
