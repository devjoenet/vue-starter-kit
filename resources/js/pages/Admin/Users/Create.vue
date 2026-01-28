<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useAbility } from '@/composables/useAbility';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { store } from '@/routes/admin/users';
import Button from '@/components/md3/Button.vue';
import Card from '@/components/md3/Card.vue';
import TextField from '@/components/md3/TextField.vue';

const { can } = useAbility();
const canCreate = computed(() => can('users.create'));

const form = useForm({
    name: '',
    email: '',
    password: '',
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

        <Card class="mt-6" :elevation="1">
            <form class="space-y-4" @submit.prevent="submit">
                <TextField
                    v-model="form.name"
                    label="Name"
                    :state="form.errors.name ? 'error' : 'default'"
                    :message="form.errors.name"
                    :disabled="!canCreate"
                    variant="outlined"
                />

                <TextField
                    v-model="form.email"
                    label="Email"
                    type="email"
                    :state="form.errors.email ? 'error' : 'default'"
                    :message="form.errors.email"
                    :disabled="!canCreate"
                    variant="outlined"
                />

                <TextField
                    v-model="form.password"
                    label="Password"
                    type="password"
                    :state="form.errors.password ? 'error' : 'default'"
                    :message="form.errors.password"
                    :disabled="!canCreate"
                    variant="outlined"
                />

                <div class="flex justify-end">
                    <Button
                        variant="filled"
                        type="submit"
                        :disabled="!canCreate || form.processing"
                    >
                        Create
                    </Button>
                </div>
            </form>
        </Card>
    </AdminLayout>
</template>
