<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SurfaceBrandLockup from '@/components/SurfaceBrandLockup.vue';
import Button from '@/components/ui/button/Button.vue';
import WelcomeHeroIllustration from '@/components/WelcomeHeroIllustration.vue';
import { dashboard, home, login, register } from '@/routes';

withDefaults(
  defineProps<{
    canRegister: boolean;
  }>(),
  {
    canRegister: true,
  },
);

const buildTargets = [
  {
    title: 'Marketing sites',
    detail: 'Narrative-first pages that make the offer legible before the user has to work for it.',
    markerClass: 'bg-primary/70',
  },
  {
    title: 'Demo-ready products',
    detail: 'Polished application shells that show capability without defaulting to fake SaaS theater.',
    markerClass: 'bg-secondary/70',
  },
  {
    title: 'Client portals',
    detail: 'Shared navigation, trust cues, and account surfaces that feel deliberate instead of bolted on.',
    markerClass: 'bg-accent/65',
  },
  {
    title: 'Internal systems',
    detail: 'Operational views that inherit the same visual language instead of starting from scratch.',
    markerClass: 'bg-foreground/35',
  },
] as const;

const heroSignals = [
  {
    title: 'Lead with one clear message',
    detail: 'Give the headline, proof, and CTA separate jobs so the page reads fast and stays calm.',
  },
  {
    title: 'Carry the same tone forward',
    detail: 'Marketing, auth, and workspace surfaces should feel related from the first click onward.',
  },
  {
    title: 'Stay useful after launch',
    detail: 'The system needs enough discipline to support demos, portals, and internal workflows later.',
  },
] as const;
</script>

<template>
  <Head title="Southeast Code">
    <meta head-key="description" name="description" content="Southeast Code builds custom systems, marketing sites, portals, and operational tools that help real teams work better." />
  </Head>

  <div id="welcome-page" class="welcome-page-theme relative isolate min-h-dvh overflow-hidden text-foreground">
    <div class="relative mx-auto flex min-h-dvh w-full max-w-7xl flex-col px-5 py-6 sm:px-6 lg:px-8">
      <header id="welcome-page-header" class="flex items-start justify-between gap-4 pb-8 text-sm sm:pb-10">
        <div id="welcome-page-brand" class="motion-step shrink-0" style="--motion-order: 0">
          <Link :href="home.url()" aria-label="Homepage" class="inline-flex rounded-[1.5rem] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
            <SurfaceBrandLockup />
          </Link>
        </div>

        <div class="motion-step flex items-center gap-3" style="--motion-order: 1">
          <Button
            id="welcome-dashboard-link"
            v-if="$page.props.auth?.user"
            as-child
            appearance="text"
            variant="muted"
            size="sm"
            rounded="full"
            class="border border-border/45 bg-background/22 px-4 shadow-(--elevation-1) backdrop-blur-xl hover:bg-background/40 hover:text-foreground"
          >
            <Link :href="dashboard.url()">Open Dashboard</Link>
          </Button>
          <template v-else>
            <Button
              id="welcome-login-link"
              as-child
              appearance="text"
              variant="muted"
              size="sm"
              rounded="full"
              class="border border-border/45 bg-background/22 px-4 shadow-(--elevation-1) backdrop-blur-xl hover:bg-background/40 hover:text-foreground"
            >
              <Link :href="login.url()">Sign In</Link>
            </Button>
          </template>
        </div>
      </header>

      <main class="relative flex flex-1 flex-col gap-12 pb-10 sm:gap-14 sm:pb-12 lg:gap-16 lg:pb-16">
        <section id="welcome-page-hero" class="welcome-hero-shell motion-stage relative flex flex-1 items-center overflow-hidden p-6 sm:px-5 sm:py-5 lg:min-h-152 lg:px-6 lg:py-6" aria-labelledby="welcome-page-heading">
          <div class="relative z-10 grid w-full items-center gap-10 lg:grid-cols-[minmax(0,33rem)_minmax(0,1fr)] lg:gap-8 xl:grid-cols-[minmax(0,35rem)_minmax(0,1fr)] xl:gap-12">
            <section id="welcome-page-content" class="relative z-[2] max-w-140 px-6 py-3 sm:py-5 lg:py-8">
              <p class="motion-step font-handwritten text-2xl font-semibold text-primary" style="--motion-order: 2">Build for marketing, CRM, or clients</p>
              <h1 id="welcome-page-heading" class="motion-step mt-6 max-w-[10ch] text-[clamp(3.15rem,6vw,6rem)] font-semibold tracking-tight text-balance" style="--motion-order: 3">
                Custom systems that help real teams operate <span class="font-handwritten font-semibold text-primary underline">better</span>
              </h1>
              <p class="motion-step mt-6 max-w-[32ch] border-t border-border/55 pt-6 text-base font-semibold text-pretty text-muted-foreground sm:text-lg" style="--motion-order: 4">
                Southeast Code builds marketing sites, demos, portals, and internal tools that share one visual system and stay useful after launch.
              </p>

              <div id="welcome-page-actions" class="motion-step mt-8 flex flex-col gap-3 sm:flex-row" style="--motion-order: 5">
                <Button v-if="$page.props.auth?.user" as-child appearance="filled" variant="primary" size="lg" rounded="full" class="motion-sheen min-w-44">
                  <Link :href="dashboard.url()">Open Dashboard</Link>
                </Button>
                <template v-else>
                  <Button v-if="canRegister" as-child appearance="filled" variant="primary" size="lg" rounded="full" class="motion-sheen min-w-44">
                    <Link :href="register.url()">Get Started</Link>
                  </Button>
                  <Button
                    as-child
                    :appearance="canRegister ? 'text' : 'filled'"
                    :variant="canRegister ? 'muted' : 'primary'"
                    size="lg"
                    rounded="full"
                    :class="canRegister ? 'min-w-44 border border-border/45 bg-transparent hover:bg-background/16 hover:text-foreground' : 'min-w-44'"
                  >
                    <Link :href="login.url()">Sign In</Link>
                  </Button>
                </template>
              </div>

              <ul role="list" class="motion-step mt-8 grid gap-3 border-t border-border/55 pt-6 sm:grid-cols-3" style="--motion-order: 6">
                <li v-for="heroSignal in heroSignals" :key="heroSignal.title" class="welcome-signal-card flex flex-col gap-3 rounded-[1.4rem] p-4 sm:p-5">
                  <p class="text-base font-semibold text-foreground">{{ heroSignal.title }}</p>
                  <p class="text-base text-pretty text-muted-foreground">{{ heroSignal.detail }}</p>
                </li>
              </ul>
            </section>

            <section id="welcome-page-visual" class="welcome-hero-visual motion-step relative min-h-72 sm:min-h-88 lg:min-h-136" style="--motion-order: 7">
              <div class="welcome-hero-media">
                <WelcomeHeroIllustration class="welcome-hero-art" />
              </div>
            </section>
          </div>
        </section>

        <section id="welcome-page-build-targets" class="welcome-foundation-shell motion-stage relative overflow-hidden rounded-4xl px-6 py-6 sm:px-8 sm:py-8 lg:px-10 lg:py-10">
          <div class="relative z-10 grid items-start gap-8 lg:grid-cols-[minmax(0,23rem)_minmax(0,1fr)] lg:gap-12">
            <aside id="welcome-page-proof-panel" class="motion-step relative z-[2] max-w-[23rem] border-t [border-top-color:color-mix(in_oklab,var(--border)_60%,transparent)] pt-4" style="--motion-order: 0">
              <p class="font-handwritten text-2xl font-semibold text-primary">One starter. Many surfaces.</p>
              <p class="mt-4 max-w-[24ch] text-lg font-semibold text-pretty text-foreground sm:text-[1.35rem]">Marketing pages, demos, client portals, and internal tools should feel like one system, not separate products.</p>
              <dl class="mt-8 grid gap-5 border-t border-border/55 pt-5">
                <div class="grid gap-2">
                  <dt class="text-base font-semibold text-foreground">Public-facing confidence</dt>
                  <dd class="text-base text-pretty text-muted-foreground">Clear hierarchy, integrated media, and proof that sits close to the claim instead of hiding in a pile of cards.</dd>
                </div>
                <div class="grid gap-2">
                  <dt class="text-base font-semibold text-foreground">Operational follow-through</dt>
                  <dd class="text-base text-pretty text-muted-foreground">The same palette, motion, and layout logic should still work when the interface becomes a tool, not just a pitch.</dd>
                </div>
              </dl>
            </aside>

            <section class="motion-step border-t border-border/55 pt-5 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-8" style="--motion-order: 1">
              <p class="font-handwritten text-2xl font-semibold text-primary">From one starting point</p>
              <p class="mt-3 max-w-[46ch] text-base text-pretty text-muted-foreground">The same starter should flex from first impression to daily operations without feeling stitched together.</p>
              <ul role="list" class="mt-6 grid gap-3 sm:grid-cols-2">
                <li v-for="buildTarget in buildTargets" :key="buildTarget.title" class="welcome-target-card flex min-h-40 w-full flex-col gap-3 rounded-[1.25rem] px-4 py-4 sm:px-5 sm:py-5">
                  <div class="flex items-center gap-3">
                    <span :class="buildTarget.markerClass" class="size-2.5 shrink-0 rounded-full" />
                    <p class="text-base font-semibold text-foreground">{{ buildTarget.title }}</p>
                  </div>
                  <p class="text-base text-pretty text-muted-foreground">{{ buildTarget.detail }}</p>
                </li>
              </ul>
            </section>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>
