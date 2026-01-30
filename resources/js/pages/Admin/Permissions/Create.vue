<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import Button from "@/components/md3/Button.vue";
  import Card from "@/components/md3/Card.vue";
  import TextField from "@/components/md3/TextField.vue";
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

    <Card class="mt-6" :elevation="1">
      <form class="space-y-4" @submit.prevent="submit">
        <TextField v-model="form.name" label="Permission name (e.g. users.view)" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" :disabled="!canCreate" variant="outlined" />

        <div class="space-y-1">
          <div class="text-sm font-medium opacity-80">Group</div>
          <select v-model="form.group" class="w-full rounded-xl border border-black/10 bg-(--md-surface) p-3 text-sm dark:border-white/10" :disabled="!canCreate">
            <option value="users">users</option>
            <option value="roles">roles</option>
            <option value="permissions">permissions</option>
          </select>
          <p v-if="form.errors.group" class="text-xs opacity-80">{{ form.errors.group }}</p>
        </div>

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
        </div>
      </form>
    </Card>
  </AdminLayout>
</template>
