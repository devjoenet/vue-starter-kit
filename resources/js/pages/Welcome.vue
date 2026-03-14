<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import Button from '@/components/ui/button/Button.vue';
import { dashboard, login, register } from '@/routes';

withDefaults(
  defineProps<{
    canRegister: boolean;
  }>(),
  {
    canRegister: true,
  },
);
</script>

<template>
  <Head title="Southeast Code" />

  <div
    id="welcome-page"
    class="relative min-h-screen overflow-hidden bg-background text-foreground"
  >
    <div
      class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-linear-to-b from-primary/10 via-primary/4 to-transparent"
    />
    <div
      class="pointer-events-none absolute -top-24 right-0 size-80 rounded-full bg-accent/10 blur-3xl"
    />
    <div
      class="pointer-events-none absolute -bottom-16 left-0 size-96 rounded-full bg-secondary/12 blur-3xl"
    />

    <div
      class="relative mx-auto flex min-h-screen w-full max-w-7xl flex-col p-6 lg:p-8"
    >
      <header
        id="welcome-page-header"
        class="flex items-center justify-end gap-3 pb-10 text-sm"
      >
        <Button
          id="welcome-dashboard-link"
          v-if="$page.props.auth?.user"
          as="a"
          :href="dashboard().url"
          appearance="outline"
        >
          Open Dashboard
        </Button>
        <template v-else>
          <TextLink id="welcome-login-link" :href="login().url">
            Sign in
          </TextLink>
        </template>
      </header>

      <main
        id="welcome-page-hero"
        class="grid flex-1 items-center gap-8 rounded-3xl border border-border/70 bg-card/90 p-6 shadow-(--elevation-2) backdrop-blur-sm sm:p-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(19rem,0.85fr)] lg:p-12"
      >
        <section id="welcome-page-content" class="max-w-2xl">
          <p
            class="mb-4 inline-flex items-center rounded-full border border-primary/20 bg-primary/8 px-3 py-1 text-xs font-semibold tracking-[0.18em] text-primary uppercase"
          >
            Southeast Code
          </p>
          <h1
            class="max-w-xl text-4xl font-semibold tracking-tight text-balance sm:text-5xl"
          >
            Modern websites and web applications built for real business use.
          </h1>
          <p
            class="mt-5 max-w-2xl text-base leading-7 text-muted-foreground sm:text-lg"
          >
            Southeast Code designs and builds polished marketing sites, client
            portals, internal tools, and demo-ready products that feel
            trustworthy on first contact and stay practical once teams rely on
            them.
          </p>

          <div
            id="welcome-page-actions"
            class="mt-8 flex flex-col gap-3 sm:flex-row"
          >
            <Button
              v-if="$page.props.auth?.user"
              as="a"
              :href="dashboard().url"
              appearance="filled"
              variant="primary"
              class="min-w-40"
            >
              Go to Dashboard
            </Button>
            <template v-else>
              <Button
                v-if="canRegister"
                as="a"
                :href="register().url"
                appearance="filled"
                variant="primary"
                class="min-w-40"
              >
                Start a Demo
              </Button>
              <Button
                as="a"
                :href="login().url"
                appearance="outline"
                variant="primary"
                class="min-w-40"
              >
                Sign In
              </Button>
            </template>
          </div>

          <dl class="mt-10 grid gap-4 sm:grid-cols-3">
            <div
              class="rounded-2xl border border-border/60 bg-background/70 p-4"
            >
              <dt
                class="text-xs font-semibold tracking-[0.14em] text-primary uppercase"
              >
                Service clarity
              </dt>
              <dd class="mt-2 text-sm leading-6 text-muted-foreground">
                Clear offers for websites, portals, internal tools, and custom
                application work.
              </dd>
            </div>
            <div
              class="rounded-2xl border border-border/60 bg-background/70 p-4"
            >
              <dt
                class="text-xs font-semibold tracking-[0.14em] text-primary uppercase"
              >
                Technical depth
              </dt>
              <dd class="mt-2 text-sm leading-6 text-muted-foreground">
                Laravel, Inertia, Vue, and reusable systems shaped for
                production instead of one-off screens.
              </dd>
            </div>
            <div
              class="rounded-2xl border border-border/60 bg-background/70 p-4"
            >
              <dt
                class="text-xs font-semibold tracking-[0.14em] text-primary uppercase"
              >
                Outcome-focused
              </dt>
              <dd class="mt-2 text-sm leading-6 text-muted-foreground">
                Interfaces designed to build trust, reduce friction, and move
                businesses toward a clear next step.
              </dd>
            </div>
          </dl>
        </section>

        <aside
          id="welcome-page-proof-panel"
          class="relative overflow-hidden rounded-3xl border border-border/70 bg-background/80 p-6 shadow-(--elevation-1)"
        >
          <div
            class="pointer-events-none absolute inset-x-8 top-0 h-px bg-linear-to-r from-transparent via-primary/40 to-transparent"
          />
          <div class="space-y-6">
            <section class="space-y-3">
              <p
                class="text-xs font-semibold tracking-[0.16em] text-primary uppercase"
              >
                What clients should understand fast
              </p>
              <h2 class="text-2xl font-semibold tracking-tight text-balance">
                Professional execution without generic starter-kit baggage.
              </h2>
              <p class="text-sm leading-6 text-muted-foreground">
                The work should feel capable, grounded, and refined before a
                prospect reads every detail.
              </p>
            </section>

            <section class="border-t border-border/60 pt-5">
              <p class="text-sm font-medium text-foreground">
                Typical engagement areas
              </p>
              <ul class="mt-3 space-y-3 text-sm text-muted-foreground">
                <li class="flex items-start justify-between gap-4">
                  <span>Custom marketing sites</span>
                  <span class="text-foreground">Strategy + conversion</span>
                </li>
                <li class="flex items-start justify-between gap-4">
                  <span>Business web applications</span>
                  <span class="text-foreground">Workflow + clarity</span>
                </li>
                <li class="flex items-start justify-between gap-4">
                  <span>Client portals and internal tools</span>
                  <span class="text-foreground">Structure + reliability</span>
                </li>
              </ul>
            </section>

            <section class="border-t border-border/60 pt-5">
              <p class="text-sm font-medium text-foreground">
                Delivery priorities
              </p>
              <ul
                class="mt-3 space-y-3 text-sm leading-6 text-muted-foreground"
              >
                <li>One clear message in the first viewport.</li>
                <li>Accessible, reusable interface patterns from the start.</li>
                <li>
                  Production-ready foundations that can become demos or ship to
                  clients.
                </li>
              </ul>
            </section>
          </div>
        </aside>
      </main>
    </div>
  </div>
</template>
