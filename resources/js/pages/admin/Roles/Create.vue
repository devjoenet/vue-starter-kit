<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { h, computed } from "vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Checkbox from "@/components/ui/checkbox/Checkbox.vue";
  import Input from "@/components/ui/input/Input.vue";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { toKebabCase } from "@/lib/utils";
  import { dashboard } from "@/routes/admin";
  import { create, index, store } from "@/routes/admin/roles";
  import type { App } from "@/wayfinder/types";
  defineOptions({
    layout: (page: unknown) =>
      h(
        AppLayout,
        {
          breadcrumbs: [
            { title: "Dashboard", href: dashboard.url() },
            { title: "Roles", href: index.url() },
            { title: "Create", href: create.url() },
          ],
        },
        () => page,
      ),
  });
  const props = defineProps<{
    users: { id: number; name: string; email: string }[];
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("roles.create"));

  const form = useForm<App["Forms"]["Admin"]["Roles"]["Store"]>({
    name: "",
    user_ids: [] as number[],
  });

  const submit = () => {
    if (!canCreate.value) return;

    form.name = toKebabCase(form.name);
    form.post(store.url());
  };

  const toggleUser = (userId: number) => {
    const index = form.user_ids.indexOf(userId);
    if (index >= 0) {
      form.user_ids.splice(index, 1);
      return;
    }

    form.user_ids.push(userId);
  };
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Create role</h1>
    </div>

    <form class="space-y-6" @submit.prevent="submit">
      <div class="grid gap-6 lg:grid-cols-2">
        <Card variant="default" class="px-6">
          <h2 class="text-lg font-semibold">Role Details</h2>

          <div class="mt-4 space-y-4">
            <Input id="create-role-name" v-model="form.name" name="name" label="Role name" variant="outlined" :disabled="!canCreate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />
          </div>
        </Card>

        <Card variant="default" class="px-6">
          <h2 class="text-lg font-semibold">Assign Users</h2>

          <div class="mt-4 space-y-2 -mx-3 max-h-72 overflow-y-auto px-3">
            <label v-for="user in props.users" :key="user.id" class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10" :class="!canCreate ? 'opacity-60' : ''">
              <Checkbox :disabled="!canCreate" :model-value="form.user_ids.includes(user.id)" @update:model-value="() => toggleUser(user.id)" />

              <span class="min-w-0">
                <span class="block truncate text-sm font-medium">{{ user.name }}</span>
                <span class="block truncate text-xs opacity-70">{{ user.email }}</span>
              </span>
            </label>
          </div>

          <p v-if="form.errors.user_ids" class="mt-2 text-xs opacity-80">
            {{ form.errors.user_ids }}
          </p>
        </Card>
      </div>

      <div class="flex justify-end">
        <Button appearance="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
      </div>
    </form>
  </div>
</template>
