<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import Button from '@/components/md3/Button.vue'
import Card from '@/components/md3/Card.vue'
import { useAbility } from '@/composables/useAbility'
import AdminLayout from '@/layouts/AdminLayout.vue'
import {create, edit, destroy} from '@/routes/admin/permissions'

const props = defineProps<{
  permissionsByGroup: Record<string, { id: number; name: string; group: string }[]>
}>()

const { can } = useAbility()
const canCreate = computed(() => can('permissions.create'))
const canUpdate = computed(() => can('permissions.update'))
const canDelete = computed(() => can('permissions.delete'))

const del = useForm({})

const destroyPermission = (id: number) => {
  if (!canDelete.value) return
  if (!confirm('Delete this permission?')) return
  del.delete(destroy.url(id))
}
</script>

<template>
  <AdminLayout title="Permissions">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Permissions</h1>

      <Link v-if="canCreate" :href="create.url()">
        <Button variant="filled">New permission</Button>
      </Link>
    </div>

    <div class="mt-6 space-y-6">
      <Card v-for="(items, group) in props.permissionsByGroup" :key="group" :elevation="1">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold capitalize">{{ group }}</h2>
        </div>

        <div class="mt-4 space-y-2">
          <div
            v-for="p in items"
            :key="p.id"
            class="flex items-center justify-between rounded-xl border border-black/5 p-3 dark:border-white/10"
          >
            <div class="text-sm font-medium">{{ p.name }}</div>

            <div class="flex items-center gap-2">
              <Link v-if="canUpdate" :href="edit.url(p.id)">
                <Button variant="text">Edit</Button>
              </Link>

              <Button v-if="canDelete" variant="text" @click="destroyPermission(p.id)">
                Delete
              </Button>
            </div>
          </div>
        </div>
      </Card>
    </div>
  </AdminLayout>
</template>
