<script setup lang="ts">
  import { Form } from "@inertiajs/vue3";
  import { Eye, EyeOff, LockKeyhole, RefreshCw } from "lucide-vue-next";
  import { computed, nextTick, onMounted, ref, useTemplateRef } from "vue";
  import Alert from "@/components/ui/alert/Alert.vue";
  import AlertDescription from "@/components/ui/alert/AlertDescription.vue";
  import AlertTitle from "@/components/ui/alert/AlertTitle.vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import CardContent from "@/components/ui/card/CardContent.vue";
  import CardDescription from "@/components/ui/card/CardDescription.vue";
  import CardHeader from "@/components/ui/card/CardHeader.vue";
  import CardTitle from "@/components/ui/card/CardTitle.vue";
  import { useTwoFactorAuth } from "@/composables/useTwoFactorAuth";
  import { normalizeErrorMessages } from "@/lib/errors";
  import { regenerateRecoveryCodes } from "@/routes/two-factor";

  const { recoveryCodesList, fetchRecoveryCodes, errors } = useTwoFactorAuth();
  const isRecoveryCodesVisible = ref<boolean>(false);
  const recoveryCodeSectionRef = useTemplateRef("recoveryCodeSectionRef");
  const errorMessages = computed(() => normalizeErrorMessages(errors.value));

  const toggleRecoveryCodesVisibility = async () => {
    if (!isRecoveryCodesVisible.value && !recoveryCodesList.value.length) {
      await fetchRecoveryCodes();
    }

    isRecoveryCodesVisible.value = !isRecoveryCodesVisible.value;

    if (isRecoveryCodesVisible.value) {
      await nextTick();
      recoveryCodeSectionRef.value?.scrollIntoView({ behavior: "smooth" });
    }
  };

  onMounted(async () => {
    if (!recoveryCodesList.value.length) {
      await fetchRecoveryCodes();
    }
  });
</script>

<template>
  <Card class="w-full">
    <CardHeader>
      <CardTitle class="flex gap-3"> <LockKeyhole class="size-4" />2FA Recovery Codes </CardTitle>
      <CardDescription> Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager. </CardDescription>
    </CardHeader>
    <CardContent>
      <div class="flex flex-col gap-3 select-none sm:flex-row sm:items-center sm:justify-between">
        <Button @click="toggleRecoveryCodesVisibility" class="w-fit">
          <component :is="isRecoveryCodesVisible ? EyeOff : Eye" class="size-4" />
          {{ isRecoveryCodesVisible ? "Hide" : "View" }} Recovery Codes
        </Button>

        <Form v-if="isRecoveryCodesVisible && recoveryCodesList.length" v-bind="regenerateRecoveryCodes.form()" method="post" :options="{ preserveScroll: true }" @success="fetchRecoveryCodes" #default="{ processing }">
          <Button variant="secondary" type="submit" :disabled="processing"> <RefreshCw /> Regenerate Codes </Button>
        </Form>
      </div>
      <div :class="['relative overflow-hidden transition-all duration-300', isRecoveryCodesVisible ? 'h-auto opacity-100' : 'h-0 opacity-0']">
        <div v-if="errorMessages.length" class="mt-6">
          <Alert variant="destructive">
            <LockKeyhole class="size-4" />
            <AlertTitle>Unable to load recovery codes</AlertTitle>
            <AlertDescription>
              <p v-if="errorMessages.length === 1" class="text-sm">{{ errorMessages[0] }}</p>
              <ul v-else class="list-inside list-disc text-sm">
                <li v-for="(message, index) in errorMessages" :key="`${message}-${index}`">{{ message }}</li>
              </ul>
            </AlertDescription>
          </Alert>
        </div>
        <div v-else class="mt-3 space-y-3">
          <div ref="recoveryCodeSectionRef" class="grid gap-1 rounded-lg bg-muted p-4 font-mono text-sm">
            <div v-if="!recoveryCodesList.length" class="space-y-2">
              <div v-for="n in 8" :key="n" class="h-4 animate-pulse rounded bg-muted-foreground/20"></div>
            </div>
            <div v-else v-for="(code, index) in recoveryCodesList" :key="index">
              {{ code }}
            </div>
          </div>
          <p class="text-xs text-muted-foreground select-none">
            Each recovery code can be used once to access your account and will be removed after use. If you need more, click
            <span class="font-bold">Regenerate Codes</span> above.
          </p>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
