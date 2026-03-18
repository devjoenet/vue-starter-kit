<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import { dashboard } from '@/routes';

const props = defineProps<{
  status: number;
}>();

const content = computed(() => {
  switch (props.status) {
    case 403:
      return {
        title: 'Access denied',
        message: 'You do not have permission to view this page.',
      };
    case 404:
      return {
        title: 'Page not found',
        message: 'The page you requested could not be found.',
      };
    case 503:
      return {
        title: 'Service unavailable',
        message: 'The app is temporarily unavailable. Please try again shortly.',
      };
    default:
      return {
        title: 'Something went wrong',
        message: 'An unexpected error occurred while loading this page.',
      };
  }
});
</script>

<template>
  <Head :title="`${content.title} (${status})`" />

  <div id="error-page" class="flex min-h-screen items-center justify-center bg-linear-to-br from-background via-background to-muted/60 px-6 py-16">
    <div id="error-page-card" class="w-full max-w-2xl rounded-3xl border border-border/70 bg-card/95 p-8 shadow-(--elevation-2) sm:p-10">
      <div id="error-page-content" class="space-y-6">
        <div id="error-page-copy" class="space-y-3">
          <p class="text-sm font-semibold tracking-[0.2em] text-primary uppercase">Error {{ status }}</p>
          <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
              {{ content.title }}
            </h1>
            <p class="max-w-xl text-base text-muted-foreground sm:text-lg">
              {{ content.message }}
            </p>
          </div>
        </div>

        <div id="error-page-actions" class="flex flex-wrap items-center gap-3">
          <Button id="error-page-home-link" as-child appearance="filled">
            <Link href="/">Go home</Link>
          </Button>

          <Button id="error-page-dashboard-link" as-child appearance="outline">
            <Link :href="dashboard()">Open dashboard</Link>
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>
