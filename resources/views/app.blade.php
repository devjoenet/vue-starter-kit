<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#007979">

    {{-- Inline script to detect system dark mode preference and apply it immediately --}}
    <script>
      (function() {
        const appearance = '{{ $appearance ?? 'system' }}';
        const root = document.documentElement;
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        const themeColor = document.querySelector('meta[name="theme-color"]');

        const applyThemeChrome = (isDark) => {
          root.classList.toggle('dark', isDark);
          root.style.colorScheme = isDark ? 'dark' : 'light';

          if (themeColor) {
            themeColor.setAttribute('content', isDark ? '#1e2022' : '#f0f5f9');
          }
        };

        if (appearance === 'system') {
          applyThemeChrome(prefersDark.matches);

          const handleChange = (event) => {
            applyThemeChrome(event.matches);
          };

          if (typeof prefersDark.addEventListener === 'function') {
            prefersDark.addEventListener('change', handleChange);
          } else if (typeof prefersDark.addListener === 'function') {
            prefersDark.addListener(handleChange);
          }
        } else {
          applyThemeChrome(appearance === 'dark');
        }
      })();
    </script>

    {{-- Inline style to set the HTML background color based on our theme in app.css --}}
    <style>
      html {
        background-color: #f0f5f9;
        color: #1e2022;
        color-scheme: light;
      }

      html.dark {
        background-color: #1e2022;
        color: #f0f5f9;
        color-scheme: dark;
      }
    </style>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
      href="https://fonts.bunny.net/css?family=league-spartan:600|open-sans:300,400,600"
      rel="stylesheet" />

    @vite('resources/js/app.ts')
    @inertiaHead
  </head>
  <body class="bg-background text-foreground font-sans antialiased">
    @inertia
    <svg aria-hidden="true"
      style="position: absolute; width: 0; height: 0; overflow: hidden">
      <defs>
        <filter id="liquid-default-surface" x="-20%" y="-20%" width="140%"
          height="140%">
          <feTurbulence type="fractalNoise" baseFrequency="0.008 0.008"
            numOctaves="2" seed="92" result="noise" />
          <feGaussianBlur in="noise" stdDeviation="0.02" result="blur" />
          <feDisplacementMap in="SourceGraphic" in2="blur" scale="26"
            xChannelSelector="R" yChannelSelector="G" />
        </filter>
      </defs>
    </svg>
  </body>
</html>
