import { createInertiaApp } from '@inertiajs/vue3';
import { createSSRApp, h } from 'vue';
import { resolveInertiaPage } from './lib/resolve-page-component';

const appName = import.meta.env.VITE_APP_NAME || 'Southeast Code';

createInertiaApp({
  resolve: resolveInertiaPage,
  title: (title) => (title ? `${title} - ${appName}` : appName),
  setup: ({ App, props, plugin }) => createSSRApp({ render: () => h(App, props) }).use(plugin),
});
