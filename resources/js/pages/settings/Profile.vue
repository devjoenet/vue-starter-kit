<script setup lang="ts">
  import { Form, Head, Link, usePage } from "@inertiajs/vue3";
  import { computed } from "vue";
  import ProfileController from "@/actions/App/Http/Controllers/Settings/ProfileController";
  import DeleteUser from "@/components/DeleteUser.vue";
  import Heading from "@/components/Heading.vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import AppLayout from "@/layouts/AppLayout.vue";
  import SettingsLayout from "@/layouts/settings/Layout.vue";
  import { edit } from "@/routes/profile";
  import { send } from "@/routes/verification";
  import { type BreadcrumbItem } from "@/types";

  type Props = {
    mustVerifyEmail: boolean;
    status?: string;
  };

  defineProps<Props>();

  const breadcrumbItems: BreadcrumbItem[] = [
    {
      title: "Profile settings",
      href: edit().url,
    },
  ];

  const page = usePage();
  const user = computed(() => page.props.auth?.user ?? null);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile Settings</h1>

    <SettingsLayout>
      <div v-if="user" class="space-y-6">
        <Card variant="glass" class="px-6">
          <div class="space-y-4">
            <Heading variant="small" title="Profile information" description="Update your name and email address" />

            <Form v-bind="ProfileController.update.form()" class="space-y-4" v-slot="{ errors, processing, recentlySuccessful }">
              <Input id="name" name="name" label="Name" variant="outlined" :default-value="user.name" required autocomplete="name" :state="errors.name ? 'error' : 'default'" :message="errors.name" />

              <Input id="email" type="email" name="email" label="Email address" variant="outlined" :default-value="user.email" required autocomplete="username" :state="errors.email ? 'error' : 'default'" :message="errors.email" />

              <div v-if="mustVerifyEmail && !user.email_verified_at">
                <p class="text-sm text-muted-foreground">
                  Your email address is unverified.
                  <Link :href="send()" as="button" class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"> Click here to resend the verification email. </Link>
                </p>

                <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-success">A new verification link has been sent to your email address.</div>
              </div>

              <div class="flex items-center gap-4">
                <Button appearance="filled" :disabled="processing" data-test="update-profile-button">Save</Button>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                  <p v-show="recentlySuccessful" class="text-sm text-success">Saved.</p>
                </Transition>
              </div>
            </Form>
          </div>
        </Card>

        <DeleteUser />
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
