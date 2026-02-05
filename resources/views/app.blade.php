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
        background-color: #f8fafc;
        color: #0f172a;
      }

      html.dark {
        background-color: oklch(18.312% 0.03092 263.398);
        color: oklch(92.876% 0.01272 255.643);
      }
    </style>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
  </head>
  <body class="bg-background text-foreground font-sans antialiased">
    @inertia
  </body>
</html>
