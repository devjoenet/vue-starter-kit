<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import SurfaceBrandLockup from '@/components/SurfaceBrandLockup.vue';
import Button from '@/components/ui/button/Button.vue';
import { home } from '@/routes';

defineProps<{
  title?: string;
  description?: string;
}>();

const trustPoints = ['Secure sign-in for demos, client portals, and internal tools.', 'Clear recovery, verification, and confirmation steps when access changes.', 'The same visual system clients and teams see after they sign in.'];
</script>

<template>
  <div class="auth-page-theme relative isolate min-h-svh overflow-hidden text-foreground">
    <div class="relative mx-auto flex min-h-svh w-full max-w-7xl flex-col px-5 py-6 sm:px-6 lg:px-8">
      <header id="auth-page-header" class="flex flex-col items-start gap-3 pb-8 text-sm sm:flex-row sm:items-start sm:justify-between sm:gap-4 sm:pb-10">
        <Link :href="home()" id="auth-page-brand" class="motion-step inline-flex shrink-0" style="--motion-order: 0">
          <SurfaceBrandLockup />
        </Link>

        <Button
          id="auth-page-home-link"
          as-child
          appearance="text"
          variant="muted"
          size="sm"
          rounded="full"
          class="motion-step border border-border/45 bg-background/22 px-4 shadow-(--elevation-1) backdrop-blur-xl hover:bg-background/40 hover:text-foreground"
          style="--motion-order: 1"
        >
          <Link :href="home()">Back home</Link>
        </Button>
      </header>

      <main class="relative flex flex-1 items-center pb-10 sm:pb-12 lg:pb-16">
        <section class="surface-auth-stage motion-stage relative w-full overflow-hidden rounded-[2rem] p-4 sm:p-5 lg:p-6">
          <div class="surface-auth-grid relative z-10 grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(22rem,27rem)] lg:grid-rows-[auto_1fr] lg:gap-x-10 lg:gap-y-8 xl:grid-cols-[minmax(0,1fr)_minmax(24rem,29rem)] xl:gap-x-14">
            <section class="surface-auth-copy max-w-[38rem] px-3 py-3 sm:px-4 lg:px-6 lg:py-5">
              <div class="motion-step" style="--motion-order: 2">
                <p class="font-handwritten text-xl font-semibold text-primary sm:text-2xl">Secure access</p>
              </div>

              <div class="motion-step mt-5 space-y-5" style="--motion-order: 3">
                <h1 class="max-w-[11ch] text-[clamp(2.9rem,5.2vw,5.2rem)] leading-[0.93] font-semibold tracking-[-0.055em] text-balance">
                  {{ title }}
                </h1>
                <p class="max-w-xl border-t border-border/45 pt-5 text-base leading-7 text-muted-foreground sm:text-lg">
                  {{ description }}
                </p>
              </div>
            </section>

            <section class="surface-auth-form-slot motion-step flex items-start lg:col-start-2 lg:row-span-2" style="--motion-order: 4">
              <div class="w-full max-w-xl lg:max-w-none">
                <slot />
              </div>
            </section>

            <aside class="surface-auth-callout motion-step px-5 py-5 sm:px-6 sm:py-6 lg:row-start-2 lg:max-w-[34rem]" style="--motion-order: 5">
              <p class="text-[0.72rem] font-semibold tracking-[0.18em] text-muted-foreground uppercase">Built for real teams</p>
              <p class="mt-3 max-w-md text-sm leading-6 text-muted-foreground sm:text-[0.96rem] sm:leading-7">Every auth step should feel like part of the same system clients and teams use after they sign in.</p>

              <ul class="mt-5 space-y-3.5">
                <li v-for="point in trustPoints" :key="point" class="surface-auth-trust-point">
                  <span class="surface-auth-trust-point-bullet" />
                  <span class="text-sm leading-6 text-muted-foreground sm:text-[0.96rem]">
                    {{ point }}
                  </span>
                </li>
              </ul>
            </aside>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>
