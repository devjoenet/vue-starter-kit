import { createInertiaApp } from '@inertiajs/vue3';
import { createSSRApp, h } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Southeast Code';

createInertiaApp({
  pages: './pages',
  title: (title) => (title ? `${title} - ${appName}` : appName),
  setup: ({ App, props, plugin }) =>
    createSSRApp({ render: () => h(App, props) }).use(plugin),
});
