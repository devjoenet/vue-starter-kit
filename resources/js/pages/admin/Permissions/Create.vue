<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { h, computed, watch } from "vue";
  import PermissionGroupSelect from "@/components/admin/PermissionGroupSelect.vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Input from "@/components/ui/input/Input.vue";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { toCamelCase, toSnakeCase } from "@/lib/utils";
  import { dashboard } from "@/routes/admin";
  import { create, index, store } from "@/routes/admin/permissions";
  import type { App } from "@/wayfinder/types";
  defineOptions({
    layout: (_: unknown, page: unknown) =>
      h(
        AppLayout,
        {
          breadcrumbs: [
            { title: "Dashboard", href: dashboard.url() },
            { title: "Permissions", href: index.url() },
            { title: "Create", href: create.url() },
          ],
        },
        () => page,
      ),
  });
  const props = defineProps<{
    groups: string[];
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("permissions.create"));

  const form = useForm<App["Forms"]["Admin"]["Permissions"]["Store"]>({
    name: "users.",
    group: "users",
  });

  const prefixWithGroup = (group: string, actionSegment = "") => {
    const normalizedGroup = toSnakeCase(group);
    const normalizedAction = toCamelCase(actionSegment);
    return normalizedAction ? `${normalizedGroup}.${normalizedAction}` : `${normalizedGroup}.`;
  };

  const extractActionSegment = (permissionName: string, group: string) => {
    const normalizedGroup = toSnakeCase(group);
    const rawValue = permissionName.trim();

    if (!rawValue) {
      return "";
    }

    if (rawValue.startsWith(`${normalizedGroup}.`)) {
      return rawValue.slice(normalizedGroup.length + 1);
    }

    const segments = rawValue
      .split(".")
      .map((segment) => segment.trim())
      .filter(Boolean);
    if (segments.length > 1) {
      return segments[segments.length - 1];
    }

    return rawValue;
  };

  const normalizePermissionInput = (permissionName: string) => {
    const action = extractActionSegment(permissionName, form.group);
    return prefixWithGroup(form.group, action);
  };

  watch(
    () => form.group,
    (nextGroup, previousGroup) => {
      const normalizedGroup = toSnakeCase(nextGroup);
      if (normalizedGroup !== nextGroup) {
        form.group = normalizedGroup;
        return;
      }

      const action = extractActionSegment(form.name, previousGroup || normalizedGroup);
      form.name = prefixWithGroup(normalizedGroup, action);
    },
    { immediate: true },
  );

  const submit = () => {
    if (!canCreate.value) return;

    form.group = toSnakeCase(form.group);
    form.name = normalizePermissionInput(form.name);
    form.post(store.url());
  };
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Create permission</h1>
    </div>

    <Card variant="default" class="px-6">
      <form class="space-y-4" @submit.prevent="submit">
        <PermissionGroupSelect id="create-permission-group" v-model="form.group" :groups="props.groups" :disabled="!canCreate" :error="form.errors.group" />

        <Input id="create-permission-name" v-model="form.name" name="name" label="Permission Name" variant="outlined" :disabled="!canCreate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

        <div class="flex justify-end">
          <Button appearance="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
        </div>
      </form>
    </Card>
  </div>
</template>
