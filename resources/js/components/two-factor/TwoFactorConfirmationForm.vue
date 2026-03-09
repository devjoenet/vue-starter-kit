<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { nextTick, onMounted, ref, useTemplateRef } from 'vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import Button from '@/components/ui/button/Button.vue';
import InputOTP from '@/components/ui/input-otp/InputOTP.vue';
import InputOTPGroup from '@/components/ui/input-otp/InputOTPGroup.vue';
import InputOTPSlot from '@/components/ui/input-otp/InputOTPSlot.vue';
import { confirm } from '@/routes/two-factor';

const emit = defineEmits<{
  (event: 'back'): void;
  (event: 'success'): void;
}>();

const code = ref('');
const pinInputContainerRef = useTemplateRef('pinInputContainerRef');

onMounted(async () => {
  await nextTick();
  pinInputContainerRef.value?.querySelector('input')?.focus();
});
</script>

<template>
  <Form
    v-bind="confirm.form()"
    reset-on-error
    :options="{
      only: ['twoFactorEnabled', 'flash'],
      preserveScroll: true,
    }"
    @finish="code = ''"
    @success="$emit('success')"
    v-slot="{ errors, processing }"
  >
    <input type="hidden" name="code" :value="code" />
    <div ref="pinInputContainerRef" class="relative w-full space-y-3">
      <div class="flex w-full flex-col items-center justify-center space-y-3 py-2">
        <InputOTP id="otp" v-model="code" :maxlength="6" :disabled="processing">
          <InputOTPGroup>
            <InputOTPSlot
              v-for="index in 6"
              :key="index"
              :index="index - 1"
            />
          </InputOTPGroup>
        </InputOTP>
        <Alert v-if="errors.code" variant="destructive" class="w-full">
          <AlertDescription class="w-full">
            <p class="text-sm">{{ errors.code }}</p>
          </AlertDescription>
        </Alert>
      </div>

      <div class="flex w-full items-center space-x-5">
        <Button
          type="button"
          appearance="outline"
          class="w-auto flex-1"
          :disabled="processing"
          @click="$emit('back')"
        >
          Back
        </Button>
        <Button
          type="submit"
          class="w-auto flex-1"
          :disabled="processing || code.length < 6"
        >
          Confirm
        </Button>
      </div>
    </div>
  </Form>
</template>
