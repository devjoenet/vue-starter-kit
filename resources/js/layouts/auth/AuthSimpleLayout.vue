<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import SurfaceBrandLockup from '@/components/SurfaceBrandLockup.vue';
import { home } from '@/routes';

defineProps<{
  title?: string;
  description?: string;
}>();

const trustPoints = ['Secure sign-in for demos, client portals, and internal tools.', 'Plain-language flows that keep the next step obvious.', 'A stronger product feel without decorative noise.'];
</script>

<template>
  <div class="surface-auth-stage relative min-h-svh overflow-hidden text-foreground">
    <div class="relative mx-auto flex min-h-svh w-full max-w-7xl items-center px-6 py-10 md:px-10">
      <div class="grid w-full gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(22rem,25rem)] lg:gap-16">
        <section class="motion-stage flex flex-col justify-center lg:pr-6">
          <Link :href="home()" class="motion-step inline-flex w-fit" style="--motion-order: 0">
            <SurfaceBrandLockup />
          </Link>

          <div class="mt-10 max-w-xl space-y-8">
            <div class="motion-step space-y-4" style="--motion-order: 1">
              <p class="section-kicker">Secure access</p>
              <h1 class="max-w-xl text-[clamp(2.5rem,4.7vw,4.25rem)] leading-[0.96] font-semibold tracking-[-0.04em] text-balance">
                {{ title }}
              </h1>
              <p class="max-w-md text-base leading-7 text-muted-foreground sm:text-lg">
                {{ description }}
              </p>
            </div>

            <div class="motion-step border-t border-border/55 pt-6" style="--motion-order: 2">
              <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-secondary uppercase">Built for real teams</p>

              <ul class="mt-4 space-y-3">
                <li v-for="point in trustPoints" :key="point" class="flex items-start gap-3">
                  <span class="mt-2 size-2 rounded-full bg-secondary ring-4 ring-secondary/18" />
                  <span class="text-sm leading-6 text-muted-foreground">
                    {{ point }}
                  </span>
                </li>
              </ul>
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
