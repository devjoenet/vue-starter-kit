<script setup lang="ts">
  import { AlertCircle } from "lucide-vue-next";
  import { computed } from "vue";
  import { normalizeErrorMessages } from "@/lib/errors";
  import Alert from "@/components/ui/alert/Alert.vue";
  import AlertDescription from "@/components/ui/alert/AlertDescription.vue";
  import AlertTitle from "@/components/ui/alert/AlertTitle.vue";
  type Props = {
    errors?: unknown;
    title?: string;
  };

  const props = withDefaults(defineProps<Props>(), {
    title: "Something went wrong.",
  });

  const uniqueErrors = computed(() => normalizeErrorMessages(props.errors));
</script>

<template>
  <Alert v-if="uniqueErrors.length" variant="destructive">
    <AlertCircle class="size-4" />
    <AlertTitle>{{ title }}</AlertTitle>
    <AlertDescription>
      <p v-if="uniqueErrors.length === 1" class="text-sm">
        {{ uniqueErrors[0] }}
      </p>
      <ul v-else class="list-inside list-disc text-sm">
        <li v-for="(error, index) in uniqueErrors" :key="index">
          {{ error }}
        </li>
      </ul>
    </AlertDescription>
  </Alert>
</template>
