<script setup lang="ts">
import { AlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import { normalizeErrorMessages } from '@/lib/errors';
type Props = {
  errors?: unknown;
  title?: string;
};

const props = withDefaults(defineProps<Props>(), {
  title: 'Check this request.',
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
      <div v-else class="space-y-2 text-sm">
        <p>Resolve the following before you continue:</p>
        <ul class="list-inside list-disc space-y-1">
          <li v-for="(error, index) in uniqueErrors" :key="index">
            {{ error }}
          </li>
        </ul>
      </div>
    </AlertDescription>
  </Alert>
</template>
