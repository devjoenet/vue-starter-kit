<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import SurfaceBrandLockup from '@/components/SurfaceBrandLockup.vue';
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
      class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-linear-to-b from-primary/18 via-primary/6 to-transparent"
    />
    <div
      class="pointer-events-none absolute -top-20 right-0 size-96 rounded-full bg-primary/14 blur-3xl"
    />
    <div
      class="pointer-events-none absolute top-1/3 left-1/2 size-72 -translate-x-1/2 rounded-full bg-accent/10 blur-3xl"
    />
    <div
      class="pointer-events-none absolute -bottom-20 left-0 size-[28rem] rounded-full bg-secondary/18 blur-3xl"
    />

    <div
      class="relative mx-auto flex min-h-screen w-full max-w-7xl flex-col p-6 lg:p-8"
    >
      <header
        id="welcome-page-header"
        class="flex items-center justify-between gap-4 pb-10 text-sm"
      >
        <div
          id="welcome-page-brand"
          class="motion-step shrink-0"
          style="--motion-order: 0"
        >
          <SurfaceBrandLockup />
        </div>

        <div
          class="motion-step flex items-center gap-3"
          style="--motion-order: 1"
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
        </div>
      </header>

      <main
        id="welcome-page-hero"
        class="surface-public-hero motion-stage grid flex-1 items-center gap-10 rounded-[1.75rem] p-6 sm:p-8 lg:grid-cols-[minmax(0,1.2fr)_22rem] lg:p-12"
      >
        <section id="welcome-page-content" class="max-w-2xl">
          <h1
            class="motion-step max-w-xl text-3xl font-semibold tracking-tight text-balance sm:text-4xl"
            style="--motion-order: 2"
          >
            Modern websites and web applications built for real business use.
          </h1>
          <p
            class="motion-step mt-5 max-w-2xl text-base leading-7 text-muted-foreground sm:text-lg"
            style="--motion-order: 3"
          >
            Southeast Code designs and builds polished marketing sites, client
            portals, internal tools, and demo-ready products that feel
            trustworthy on first contact and stay practical once teams rely on
            them.
          </p>

          <div
            id="welcome-page-actions"
            class="motion-step mt-8 flex flex-col gap-3 sm:flex-row"
            style="--motion-order: 4"
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

          <div
            class="motion-step mt-10 grid gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]"
            style="--motion-order: 5"
          >
            <div class="space-y-3">
              <p class="section-kicker">What this starter proves</p>
              <p class="text-sm leading-6 text-muted-foreground">
                It should read as a Southeast Code build first: clear offer,
                sound technical footing, and a more disciplined interface than a
                default scaffold.
              </p>
            </div>

            <dl class="space-y-4">
              <div class="border-l-2 border-primary/45 pl-4">
                <dt
                  class="text-sm font-semibold tracking-tight text-foreground"
                >
                  Service clarity
                </dt>
                <dd class="mt-1 text-sm leading-6 text-muted-foreground">
                  Clear offers for websites, portals, internal tools, and custom
                  application work.
                </dd>
              </div>
              <div class="border-l-2 border-secondary/55 pl-4">
                <dt
                  class="text-sm font-semibold tracking-tight text-foreground"
                >
                  Technical depth
                </dt>
                <dd class="mt-1 text-sm leading-6 text-muted-foreground">
                  Laravel, Inertia, Vue, and reusable systems shaped for
                  production rather than one-off screens.
                </dd>
              </div>
              <div class="border-l-2 border-accent/55 pl-4">
                <dt
                  class="text-sm font-semibold tracking-tight text-foreground"
                >
                  Outcome-focused
                </dt>
                <dd class="mt-1 text-sm leading-6 text-muted-foreground">
                  Interfaces designed to build trust, reduce friction, and move
                  businesses toward a clear next step.
                </dd>
              </div>
            </dl>
          </div>
        </section>

        <aside
          id="welcome-page-proof-panel"
          class="surface-public-panel motion-step relative overflow-hidden rounded-[1.75rem] p-6"
          style="--motion-order: 6"
        >
          <div
            class="pointer-events-none absolute inset-x-8 top-0 h-px bg-linear-to-r from-transparent via-accent/60 to-transparent"
          />
          <div class="space-y-6">
            <section class="space-y-3">
              <p class="section-kicker">What clients should understand fast</p>
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
              <dl class="mt-4 space-y-4">
                <div class="space-y-1">
                  <dt
                    class="text-sm font-semibold tracking-tight text-foreground"
                  >
                    Custom marketing sites
                  </dt>
                  <dd class="text-sm text-muted-foreground">
                    Strategy-led pages shaped for conversion and credibility.
                  </dd>
                </div>
                <div class="space-y-1">
                  <dt
                    class="text-sm font-semibold tracking-tight text-foreground"
                  >
                    Business web applications
                  </dt>
                  <dd class="text-sm text-muted-foreground">
                    Workflow tools that stay clear once real teams use them.
                  </dd>
                </div>
                <div class="space-y-1">
                  <dt
                    class="text-sm font-semibold tracking-tight text-foreground"
                  >
                    Client portals and internal tools
                  </dt>
                  <dd class="text-sm text-muted-foreground">
                    Structure, reporting, and dependable access control.
                  </dd>
                </div>
              </dl>
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
