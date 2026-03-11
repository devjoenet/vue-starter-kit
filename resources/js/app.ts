import { createInertiaApp } from '@inertiajs/vue3';
import { Fragment, createApp, h } from 'vue';
import DeleteConfirmationDialog from './components/DeleteConfirmationDialog.vue';
import AppToasts from './components/AppToasts.vue';
import '../css/app.css';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  pages: './pages',
  title: (title) => (title ? `${title} - ${appName}` : appName),
  setup({ el, App, props, plugin }) {
    createApp({
      render: () =>
        h(Fragment, [h(App, props), h(AppToasts), h(DeleteConfirmationDialog)]),
    })
      .use(plugin)
      .mount(el!);
  },
  progress: {
    color: '#4B5563',
  },
});

// This will set light / dark mode on page load...
initializeTheme();
