<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { nextTick, useTemplateRef } from 'vue';
import SettingsActionRow from '@/components/SettingsActionRow.vue';
import SettingsSectionCard from '@/components/SettingsSectionCard.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import Input from '@/components/ui/input/Input.vue';
import { destroy } from '@/routes/profile';

const passwordInput = useTemplateRef<{ focus: () => void }>('passwordInput');

const focusPasswordInput = async (): Promise<void> => {
  await nextTick();

  passwordInput.value?.focus();
};
</script>

<template>
  <SettingsSectionCard appearance="tinted" variant="error" title="Delete account" description="Permanently remove this account and the data tied to it." content-class="space-y-3">
    <p class="text-sm text-muted-foreground">Use this only when the account should no longer exist in this starter or any related environments.</p>

    <template #footer>
      <SettingsActionRow status="This action cannot be undone." status-tone="destructive">
        <Dialog>
          <DialogTrigger as-child>
            <Button id="settings-profile-delete-account-trigger-button" appearance="filled" variant="destructive" data-test="delete-user-button">Delete account</Button>
          </DialogTrigger>

          <DialogContent id="settings-profile-delete-account-dialog">
            <Form
              id="settings-profile-delete-account-form"
              v-bind="destroy.form()"
              reset-on-success
              @error="focusPasswordInput"
              :options="{
                preserveScroll: true,
              }"
              class="space-y-4"
              v-slot="{ errors, processing, reset, clearErrors }"
            >
              <DialogHeader class="space-y-3">
                <DialogTitle>Are you sure you want to delete your account?</DialogTitle>
                <DialogDescription> This removes the account and every resource that depends on it. Enter your password only if you intend to complete that deletion now. </DialogDescription>
              </DialogHeader>

              <div class="rounded-[1rem] border border-destructive/20 bg-destructive/10 px-4 py-3 text-sm leading-6 text-muted-foreground">This action takes effect immediately after confirmation and cannot be recovered from this workspace.</div>

              <Input id="settings-profile-delete-account-password" ref="passwordInput" type="password" name="password" label="Password" variant="outlined" :state="errors.password ? 'error' : 'default'" :message="errors.password" />

              <DialogFooter class="gap-2">
                <DialogClose as-child>
                  <Button
                    id="settings-profile-delete-account-cancel-button"
                    appearance="outline"
                    variant="muted"
                    data-test="cancel-delete-user-button"
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

                <Button id="settings-profile-delete-account-confirm-button" type="submit" appearance="filled" variant="destructive" :disabled="processing" data-test="confirm-delete-user-button"> Delete account </Button>
              </DialogFooter>
            </Form>
          </DialogContent>
        </Dialog>
      </SettingsActionRow>
    </template>
  </SettingsSectionCard>
</template>
