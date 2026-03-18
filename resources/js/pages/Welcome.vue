<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import SurfaceBrandLockup from '@/components/SurfaceBrandLockup.vue';
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
            <Button
              id="welcome-login-link"
              as="a"
              :href="login().url"
              appearance="outline"
              variant="primary"
            >
              Sign in
            </Button>
          </template>
        </div>
      </header>

      <main
        id="welcome-page-hero"
        class="surface-public-hero motion-stage grid flex-1 items-center gap-10 rounded-[1.75rem] p-6 sm:p-8 lg:grid-cols-[minmax(0,1.05fr)_22rem] lg:p-12"
      >
        <section
          id="welcome-page-content"
          class="flex max-w-2xl flex-col justify-center"
        >
          <h1
            class="motion-step max-w-3xl text-[clamp(2.65rem,5.4vw,4.8rem)] leading-[0.96] font-semibold tracking-[-0.05em] text-balance"
            style="--motion-order: 2"
          >
            Modern websites and web applications built for real business use.
          </h1>
          <p
            class="motion-step mt-6 max-w-xl text-base leading-7 text-muted-foreground sm:text-lg"
            style="--motion-order: 3"
          >
            Southeast Code designs and builds polished marketing sites, client
            portals, internal tools, and demo-ready products that feel
            trustworthy on first contact and stay practical once teams rely on
            them.
          </p>

          <div
            id="welcome-page-actions"
            class="motion-step mt-9 flex flex-col gap-3 sm:flex-row"
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
            class="motion-step mt-10 border-t border-border/55 pt-6"
            style="--motion-order: 5"
          >
            <dl class="grid gap-5 sm:grid-cols-3">
              <div class="space-y-2 sm:pr-4">
                <dt
                  class="text-[0.72rem] font-semibold tracking-[0.18em] text-primary uppercase"
                >
                  Service clarity
                </dt>
                <dd class="text-sm leading-6 text-foreground/90">
                  Clear offers for websites, portals, internal tools, and custom
                  application work.
                </dd>
              </div>
              <div class="space-y-2 sm:px-2">
                <dt
                  class="text-[0.72rem] font-semibold tracking-[0.18em] text-secondary uppercase"
                >
                  Technical depth
                </dt>
                <dd class="text-sm leading-6 text-foreground/90">
                  Laravel, Inertia, Vue, and reusable systems shaped for
                  production rather than one-off screens.
                </dd>
              </div>
              <div class="space-y-2 sm:pl-4">
                <dt
                  class="text-[0.72rem] font-semibold tracking-[0.18em] text-accent uppercase"
                >
                  Outcome-focused
                </dt>
                <dd class="text-sm leading-6 text-foreground/90">
                  Interfaces designed to build trust, reduce friction, and move
                  businesses toward a clear next step.
                </dd>
              </div>
            </dl>
          </div>
        </section>

        <aside
          id="welcome-page-proof-panel"
          class="surface-public-panel motion-step relative overflow-hidden rounded-[1.75rem] p-6 lg:p-7"
          style="--motion-order: 6"
        >
          <div class="space-y-6">
            <section class="space-y-3">
              <p class="section-kicker">What clients should understand fast</p>
              <h2
                class="text-[clamp(1.85rem,3vw,2.8rem)] font-semibold tracking-[-0.03em] text-balance"
              >
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
                <div class="space-y-1 border-l-2 border-primary/40 pl-4">
                  <dt
                    class="text-sm font-semibold tracking-tight text-foreground"
                  >
                    Custom marketing sites
                  </dt>
                  <dd class="text-sm text-muted-foreground">
                    Strategy-led pages shaped for conversion and credibility.
                  </dd>
                </div>
                <div class="space-y-1 border-l-2 border-secondary/40 pl-4">
                  <dt
                    class="text-sm font-semibold tracking-tight text-foreground"
                  >
                    Business web applications
                  </dt>
                  <dd class="text-sm text-muted-foreground">
                    Workflow tools that stay clear once real teams use them.
                  </dd>
                </div>
                <div class="space-y-1 border-l-2 border-accent/40 pl-4">
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
