<script setup lang="ts">
  import { Form } from "@inertiajs/vue3";
  import { useTemplateRef } from "vue";
  import ProfileController from "@/actions/App/Http/Controllers/Settings/ProfileController";
  import Heading from "@/components/Heading.vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Dialog from "@/components/ui/dialog/Dialog.vue";
  import DialogClose from "@/components/ui/dialog/DialogClose.vue";
  import DialogContent from "@/components/ui/dialog/DialogContent.vue";
  import DialogDescription from "@/components/ui/dialog/DialogDescription.vue";
  import DialogFooter from "@/components/ui/dialog/DialogFooter.vue";
  import DialogHeader from "@/components/ui/dialog/DialogHeader.vue";
  import DialogTitle from "@/components/ui/dialog/DialogTitle.vue";
  import DialogTrigger from "@/components/ui/dialog/DialogTrigger.vue";
  import Input from "@/components/ui/input/Input.vue";
  const passwordInput = useTemplateRef("passwordInput");
</script>

<template>
  <Card variant="destructive" class="px-6">
    <div class="space-y-4">
      <Heading variant="small" title="Delete account" description="Delete your account and all of its resources" />

      <p class="text-sm text-destructive">Please proceed with caution. This action cannot be undone.</p>

      <Dialog>
        <DialogTrigger as-child>
          <Button appearance="filled" variant="destructive" data-test="delete-user-button">Delete account</Button>
        </DialogTrigger>

        <DialogContent>
          <Form
            v-bind="ProfileController.destroy.form()"
            reset-on-success
            @error="() => passwordInput?.$el?.focus()"
            :options="{
              preserveScroll: true,
            }"
            class="space-y-4"
            v-slot="{ errors, processing, reset, clearErrors }"
          >
            <DialogHeader class="space-y-3">
              <DialogTitle>Are you sure you want to delete your account?</DialogTitle>
              <DialogDescription> Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. </DialogDescription>
            </DialogHeader>

            <Input id="password" ref="passwordInput" type="password" name="password" label="Password" variant="outlined" placeholder="Password" :state="errors.password ? 'error' : 'default'" :message="errors.password" />

            <DialogFooter class="gap-2">
              <DialogClose as-child>
                <Button
                  appearance="outline"
                  variant="muted"
                  @click="
                    () => {
                      clearErrors();
                      reset();
                    }
                  "
                >
                  Cancel
                </Button>
              </DialogClose>

              <Button type="submit" appearance="filled" variant="destructive" :disabled="processing" data-test="confirm-delete-user-button"> Delete account </Button>
            </DialogFooter>
          </Form>
        </DialogContent>
      </Dialog>
    </div>
  </Card>
</template>
