<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import SurfaceBrandLockup from '@/components/SurfaceBrandLockup.vue';
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
        class="grid w-full gap-10 lg:grid-cols-[minmax(0,1.05fr)_24rem] lg:gap-14"
      >
        <section class="motion-stage flex flex-col justify-center">
          <Link
            :href="home()"
            class="motion-step inline-flex w-fit"
            style="--motion-order: 0"
          >
            <SurfaceBrandLockup />
          </Link>

          <div class="mt-10 max-w-xl space-y-6">
            <div class="motion-step space-y-3" style="--motion-order: 1">
              <p class="section-kicker">Secure access</p>
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
              class="surface-auth-callout motion-step rounded-[1.75rem] p-6"
              style="--motion-order: 2"
            >
              <p class="text-sm font-semibold tracking-tight text-foreground">
                Access should feel calm, capable, and specific.
              </p>

              <ul class="mt-5 space-y-4">
                <li
                  v-for="point in trustPoints"
                  :key="point"
                  class="flex items-start gap-3"
                >
                  <span
                    class="mt-2 size-2 rounded-full bg-secondary ring-4 ring-secondary/15"
                  />
                  <span class="text-sm leading-6 text-muted-foreground">
                    {{ point }}
                  </span>
                </li>
              </ul>

              <p
                class="mt-5 border-t border-border/60 pt-5 text-sm leading-6 text-muted-foreground"
              >
                {{ accessNote }}
              </p>
            </div>
          </div>
        </section>

        <section class="flex items-center justify-center lg:justify-end">
          <div class="motion-step w-full max-w-md" style="--motion-order: 3">
            <slot />
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
