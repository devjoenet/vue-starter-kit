<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import TextLink from "@/components/TextLink.vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { Spinner } from "@/components/ui/spinner";
  import AuthBase from "@/layouts/AuthLayout.vue";
  import { login } from "@/routes";
  import { store } from "@/routes/register";
</script>

<template>
  <AuthBase title="Create an account" description="Enter your details below to create your account">
    <Head title="Register" />

    <Card variant="default" class="px-6">
      <Form v-bind="store.form()" :reset-on-success="['password', 'password_confirmation']" v-slot="{ errors, processing }" class="space-y-4">
        <Input id="name" type="text" name="name" label="Name" variant="outlined" required autofocus :tabindex="1" autocomplete="name" :state="errors.name ? 'error' : 'default'" :message="errors.name" />

        <Input id="email" type="email" name="email" label="Email address" variant="outlined" required :tabindex="2" autocomplete="email" :state="errors.email ? 'error' : 'default'" :message="errors.email" />

        <Input id="password" type="password" name="password" label="Password" variant="outlined" required :tabindex="3" autocomplete="new-password" :state="errors.password ? 'error' : 'default'" :message="errors.password" />

        <Input id="password_confirmation" type="password" name="password_confirmation" label="Confirm password" variant="outlined" required :tabindex="4" autocomplete="new-password" :state="errors.password_confirmation ? 'error' : 'default'" :message="errors.password_confirmation" />

        <Button type="submit" appearance="filled" class="w-full" tabindex="5" :disabled="processing" data-test="register-user-button">
          <Spinner v-if="processing" />
          Create account
        </Button>
      </Form>
    </Card>

    <div class="text-center text-sm text-muted-foreground">
      Already have an account?
      <TextLink :href="login()" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
    </div>
  </AuthBase>
</template>
