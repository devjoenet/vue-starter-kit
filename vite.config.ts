import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig, loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');

  return {
    build: {
      chunkSizeWarningLimit: 340,
    },
    define: {
      __VUE_PROD_DEVTOOLS__: JSON.stringify(env.APP_ENV !== 'production'),
    },
    plugins: [
      laravel({
        input: ['resources/js/app.ts'],
        refresh: true,
      }),
      inertia(),
      tailwindcss(),
      wayfinder({
        formVariants: true,
      }),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
        },
      }),
    ],
  };
});
