<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import { home } from '@/routes';

defineProps<{
  title?: string;
  description?: string;
}>();

const trustPoints = [
  'Built for secure business workflows.',
  'Designed to stay clear under pressure.',
  'Consistent with the Southeast Code product system.',
];

const accessNote =
  'Each screen is meant to keep the next step obvious: enter the right details, confirm access, and continue without guesswork.';
</script>

<template>
  <div class="relative min-h-svh overflow-hidden bg-background text-foreground">
    <div
      class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-linear-to-b from-primary/10 via-primary/4 to-transparent"
    />
    <div
      class="pointer-events-none absolute top-24 -left-20 size-72 rounded-full bg-secondary/10 blur-3xl"
    />
    <div
      class="pointer-events-none absolute -right-24 bottom-0 size-96 rounded-full bg-accent/10 blur-3xl"
    />

    <div
      class="relative mx-auto flex min-h-svh w-full max-w-7xl items-center p-6 md:p-10"
    >
      <div
        class="grid w-full gap-10 lg:grid-cols-[minmax(0,1fr)_26rem] lg:gap-14"
      >
        <section class="flex flex-col justify-center">
          <Link :href="home()" class="inline-flex w-fit items-center gap-3">
            <AppLogo />
          </Link>

          <div class="mt-10 max-w-xl space-y-6">
            <div class="space-y-3">
              <p
                class="text-xs font-semibold tracking-[0.18em] text-primary uppercase"
              >
                Secure access
              </p>
              <h1
                class="text-4xl font-semibold tracking-tight text-balance sm:text-5xl"
              >
                {{ title }}
              </h1>
              <p
                class="max-w-lg text-base leading-7 text-muted-foreground sm:text-lg"
              >
                {{ description }}
              </p>
            </div>

            <div
              class="grid gap-3 rounded-3xl border border-border/70 bg-card/80 p-5 shadow-(--elevation-1) backdrop-blur-sm sm:grid-cols-3 lg:grid-cols-1"
            >
              <div
                v-for="point in trustPoints"
                :key="point"
                class="rounded-2xl border border-border/60 bg-background/80 px-4 py-3 text-sm leading-6 text-muted-foreground"
              >
                {{ point }}
              </div>
            </div>

            <p class="text-sm leading-6 text-muted-foreground">
              {{ accessNote }}
            </p>
          </div>
        </section>

        <section class="flex items-center justify-center lg:justify-end">
          <div class="w-full max-w-md">
            <slot />
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
