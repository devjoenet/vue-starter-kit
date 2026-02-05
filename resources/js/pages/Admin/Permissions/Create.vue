<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AdminLayout from "@/layouts/AdminLayout.vue";
  import { store } from "@/routes/admin/permissions";

  const { can } = useAbility();
  const canCreate = computed(() => can("permissions.create"));

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
  <AdminLayout title="Create Permission">
    <h1 class="text-2xl font-semibold">Create permission</h1>

    <Card class="mt-6 px-6">
      <form class="space-y-4" @submit.prevent="submit">
        <Input id="create-permission-name" v-model="form.name" name="name" label="Permission name (e.g. users.view)" variant="outlined" :disabled="!canCreate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

        <div class="space-y-1">
          <div class="text-sm font-medium opacity-80">Group</div>
          <select v-model="form.group" class="h-14 w-full rounded-[var(--radius-sm)] border border-[color:var(--outline)] bg-[var(--field-bg)] px-4 text-sm text-[var(--field-text)]" :disabled="!canCreate">
            <option value="users">users</option>
            <option value="roles">roles</option>
            <option value="permissions">permissions</option>
          </select>
          <p v-if="form.errors.group" class="text-xs text-[var(--error)]">
            {{ form.errors.group }}
          </p>
        </div>

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
        </div>
      </form>
    </Card>
  </AdminLayout>
</template>
