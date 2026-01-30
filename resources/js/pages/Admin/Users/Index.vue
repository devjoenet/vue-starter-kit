<script setup lang="ts">
  import { Link, usePage } from "@inertiajs/vue3";
  import { useAbility } from "@/composables/useAbility";
  import AdminLayout from "@/layouts/AdminLayout.vue";
  import { create, edit, destroy } from "@/routes/admin/users";

  defineProps<{ users: any }>();

  const { can } = useAbility();
  const page = usePage();
</script>

<template>
  <AdminLayout title="Users">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Users</h1>

      <Link v-if="can('users.create')" :href="create.url()" class="md3-btn md3-btn--filled" v-ripple> New user </Link>
    </div>

    <div class="md3-card mt-6">
      <table class="w-full text-sm">
        <thead class="opacity-70">
          <tr>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Email</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users.data" :key="u.id" class="border-t border-black/5 dark:border-white/10">
            <td class="p-3">{{ u.name }}</td>
            <td class="p-3">{{ u.email }}</td>
            <td class="p-3 text-right">
              <Link v-if="can('users.update')" :href="edit.url(u.id)" class="md3-btn md3-btn--text" v-ripple> Edit </Link>

              <form v-if="can('users.delete')" class="inline" method="post" :action="destroy.url(u.id)">
                <input type="hidden" name="_method" value="delete" />
                <input type="hidden" name="_token" :value="page.props.csrf_token" />
                <button type="submit" class="md3-btn md3-btn--text" v-ripple>Delete</button>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
