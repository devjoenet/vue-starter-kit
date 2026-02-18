<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Inline script to detect system dark mode preference and apply it immediately --}}
    <script>
      (function() {
        const appearance = '{{ $appearance ?? 'system' }}';

        if (appearance === 'system') {
          const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

          if (prefersDark) {
            document.documentElement.classList.add('dark');
          }
        }
      })();
    </script>

    {{-- Inline style to set the HTML background color based on our theme in app.css --}}
    <style>
      html {
        background-color: color-mix(in srgb, #294360 6%, white);
        color: #294360;
      }

      html.dark {
        background-color: color-mix(in srgb, black 82%, #294360 18%);
        color: color-mix(in srgb, white 92%, #1c96ce 8%);
      }
    </style>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=league-spartan:600|open-sans:300,400,600" rel="stylesheet" />

    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
  </head>
  <body class="bg-background text-foreground font-sans antialiased">
    @inertia
    <svg aria-hidden="true" style="position: absolute; width: 0; height: 0; overflow: hidden">
      <defs>
        <filter id="liquid-default-surface" x="-20%" y="-20%" width="140%" height="140%">
          <feTurbulence type="fractalNoise" baseFrequency="0.008 0.008" numOctaves="2" seed="92" result="noise" />
          <feGaussianBlur in="noise" stdDeviation="0.02" result="blur" />
          <feDisplacementMap in="SourceGraphic" in2="blur" scale="26" xChannelSelector="R" yChannelSelector="G" />
        </filter>
      </defs>
    </svg>
  </body>
</html>
