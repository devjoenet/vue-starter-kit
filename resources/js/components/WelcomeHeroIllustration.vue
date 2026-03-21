<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <svg :class="cn('h-full w-full', props.class)" viewBox="0 0 980 820" fill="none" aria-hidden="true">
    <defs>
      <linearGradient id="welcome-hero-panel-stroke" x1="140" y1="120" x2="760" y2="620" gradientUnits="userSpaceOnUse">
        <stop offset="0%" style="stop-color: color-mix(in oklab, var(--secondary) 58%, var(--primary) 42%)" />
        <stop offset="100%" style="stop-color: color-mix(in oklab, var(--accent) 44%, var(--primary) 56%)" />
      </linearGradient>
      <linearGradient id="welcome-hero-panel-fill" x1="260" y1="140" x2="700" y2="620" gradientUnits="userSpaceOnUse">
        <stop offset="0%" style="stop-color: color-mix(in oklab, var(--background) 42%, transparent)" />
        <stop offset="100%" style="stop-color: color-mix(in oklab, var(--surface-panel-secondary) 38%, transparent)" />
      </linearGradient>
      <radialGradient id="welcome-hero-spotlight" cx="0" cy="0" r="1" gradientTransform="translate(694 222) rotate(122.807) scale(326.15 412.465)" gradientUnits="userSpaceOnUse">
        <stop offset="0%" style="stop-color: color-mix(in oklab, var(--secondary) 34%, transparent)" />
        <stop offset="100%" style="stop-color: transparent" />
      </radialGradient>
    </defs>

    <g class="welcome-hero-illustration-scene" opacity="0.92">
      <circle class="welcome-hero-illustration-halo" cx="710" cy="218" r="190" fill="url(#welcome-hero-spotlight)" />

      <path
        d="M138 186C138 163.909 155.909 146 178 146H294C316.091 146 334 163.909 334 186V596C334 618.091 316.091 636 294 636H178C155.909 636 138 618.091 138 596V186Z"
        style="fill: color-mix(in oklab, var(--background) 16%, transparent); stroke: color-mix(in oklab, var(--border) 58%, transparent); stroke-width: 1.4"
      />
      <path d="M272 234H200" style="stroke: color-mix(in oklab, var(--foreground) 28%, transparent); stroke-linecap: round; stroke-width: 10" />
      <path d="M248 286H200" style="stroke: color-mix(in oklab, var(--foreground) 18%, transparent); stroke-linecap: round; stroke-width: 8" />
      <rect x="182" y="344" width="108" height="104" rx="24" style="fill: color-mix(in oklab, var(--primary) 20%, transparent); stroke: color-mix(in oklab, var(--primary) 38%, transparent); stroke-width: 1.3" />
      <rect x="182" y="470" width="108" height="148" rx="24" style="fill: color-mix(in oklab, var(--secondary) 16%, transparent); stroke: color-mix(in oklab, var(--secondary) 34%, transparent); stroke-width: 1.3" />

      <rect x="248" y="116" width="506" height="376" rx="40" fill="url(#welcome-hero-panel-fill)" stroke="url(#welcome-hero-panel-stroke)" stroke-width="1.8" />
      <path d="M248 188H754" style="stroke: color-mix(in oklab, var(--border) 66%, transparent); stroke-width: 1.2" />
      <circle class="welcome-hero-illustration-spark welcome-hero-illustration-spark--one" cx="290" cy="152" r="8" style="fill: color-mix(in oklab, var(--accent) 76%, transparent)" />
      <circle class="welcome-hero-illustration-spark welcome-hero-illustration-spark--two" cx="318" cy="152" r="8" style="fill: color-mix(in oklab, var(--secondary) 72%, transparent)" />
      <circle class="welcome-hero-illustration-spark welcome-hero-illustration-spark--three" cx="346" cy="152" r="8" style="fill: color-mix(in oklab, var(--primary) 72%, transparent)" />

      <rect x="286" y="226" width="176" height="220" rx="28" style="fill: color-mix(in oklab, var(--background) 24%, transparent); stroke: color-mix(in oklab, var(--border) 74%, transparent); stroke-width: 1.3" />
      <path d="M320 270H426" style="stroke: color-mix(in oklab, var(--foreground) 24%, transparent); stroke-linecap: round; stroke-width: 10" />
      <path d="M320 306H390" style="stroke: color-mix(in oklab, var(--foreground) 16%, transparent); stroke-linecap: round; stroke-width: 8" />
      <rect x="320" y="350" width="106" height="12" rx="6" style="fill: color-mix(in oklab, var(--secondary) 58%, transparent)" />
      <rect x="320" y="378" width="84" height="12" rx="6" style="fill: color-mix(in oklab, var(--primary) 48%, transparent)" />
      <rect x="320" y="406" width="64" height="12" rx="6" style="fill: color-mix(in oklab, var(--accent) 38%, transparent)" />

      <rect x="490" y="226" width="220" height="116" rx="28" style="fill: color-mix(in oklab, var(--background) 22%, transparent); stroke: color-mix(in oklab, var(--border) 72%, transparent); stroke-width: 1.3" />
      <path d="M530 268H646" style="stroke: color-mix(in oklab, var(--foreground) 22%, transparent); stroke-linecap: round; stroke-width: 10" />
      <path d="M530 304H620" style="stroke: color-mix(in oklab, var(--foreground) 16%, transparent); stroke-linecap: round; stroke-width: 8" />
      <rect x="530" y="322" width="120" height="8" rx="4" style="fill: color-mix(in oklab, var(--secondary) 34%, transparent)" />
      <circle class="welcome-hero-illustration-node" cx="662" cy="286" r="20" style="fill: color-mix(in oklab, var(--primary) 38%, transparent); stroke: color-mix(in oklab, var(--primary) 64%, transparent); stroke-width: 1.2" />

      <rect x="490" y="364" width="220" height="82" rx="28" style="fill: color-mix(in oklab, var(--background) 18%, transparent); stroke: color-mix(in oklab, var(--border) 68%, transparent); stroke-width: 1.2" />
      <path d="M530 400H666" style="stroke: color-mix(in oklab, var(--foreground) 18%, transparent); stroke-linecap: round; stroke-width: 8" />
      <path d="M530 426H608" style="stroke: color-mix(in oklab, var(--foreground) 12%, transparent); stroke-linecap: round; stroke-width: 7" />

      <path
        class="welcome-hero-illustration-route welcome-hero-illustration-route--primary"
        d="M332 544C392 514 454 486 520 464C596 438 674 424 752 422"
        style="stroke: color-mix(in oklab, var(--secondary) 44%, transparent); stroke-dasharray: 7 11; stroke-linecap: round; stroke-width: 2.6"
      />
      <path
        class="welcome-hero-illustration-route welcome-hero-illustration-route--secondary"
        d="M332 602C432 624 524 636 640 636C736 636 814 614 868 562"
        style="stroke: color-mix(in oklab, var(--accent) 32%, transparent); stroke-dasharray: 7 12; stroke-linecap: round; stroke-width: 2.4"
      />

      <rect x="576" y="520" width="276" height="154" rx="34" style="fill: color-mix(in oklab, var(--background) 14%, transparent); stroke: color-mix(in oklab, var(--border) 72%, transparent); stroke-width: 1.5" />
      <path d="M618 570H786" style="stroke: color-mix(in oklab, var(--foreground) 20%, transparent); stroke-linecap: round; stroke-width: 10" />
      <path d="M618 608H754" style="stroke: color-mix(in oklab, var(--foreground) 14%, transparent); stroke-linecap: round; stroke-width: 8" />
      <rect x="618" y="638" width="84" height="10" rx="5" style="fill: color-mix(in oklab, var(--primary) 52%, transparent)" />
      <rect x="714" y="638" width="58" height="10" rx="5" style="fill: color-mix(in oklab, var(--secondary) 48%, transparent)" />

      <rect x="72" y="514" width="228" height="152" rx="34" style="fill: color-mix(in oklab, var(--background) 12%, transparent); stroke: color-mix(in oklab, var(--border) 64%, transparent); stroke-width: 1.4" />
      <path d="M112 562H236" style="stroke: color-mix(in oklab, var(--foreground) 20%, transparent); stroke-linecap: round; stroke-width: 10" />
      <path d="M112 598H212" style="stroke: color-mix(in oklab, var(--foreground) 13%, transparent); stroke-linecap: round; stroke-width: 8" />
      <circle
        class="welcome-hero-illustration-node welcome-hero-illustration-node--accent"
        cx="254"
        cy="590"
        r="26"
        style="fill: color-mix(in oklab, var(--accent) 24%, transparent); stroke: color-mix(in oklab, var(--accent) 52%, transparent); stroke-width: 1.2"
      />

      <circle class="welcome-hero-illustration-spark welcome-hero-illustration-spark--four" cx="816" cy="194" r="14" style="fill: color-mix(in oklab, var(--primary) 42%, transparent)" />
      <circle class="welcome-hero-illustration-spark welcome-hero-illustration-spark--five" cx="842" cy="230" r="10" style="fill: color-mix(in oklab, var(--secondary) 42%, transparent)" />
      <circle class="welcome-hero-illustration-spark welcome-hero-illustration-spark--six" cx="786" cy="234" r="8" style="fill: color-mix(in oklab, var(--accent) 40%, transparent)" />
    </g>
  </svg>
</template>
