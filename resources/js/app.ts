import { createInertiaApp } from '@inertiajs/vue3';
import { Fragment, createApp, h } from 'vue';
import AppToasts from './components/AppToasts.vue';
import DeleteConfirmationDialog from './components/DeleteConfirmationDialog.vue';
import '../css/app.css';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Southeast Code';
const fallbackProgressColor = 'var(--primary)';

const resolveProgressColor = () => {
  if (typeof window === 'undefined') {
    return fallbackProgressColor;
  }

  return getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || fallbackProgressColor;
};

createInertiaApp({
  pages: './pages',
  title: (title) => (title ? `${title} - ${appName}` : appName),
  setup({ el, App, props, plugin }) {
    createApp({
      render: () => h(Fragment, [h(App, props), h(AppToasts), h(DeleteConfirmationDialog)]),
    })
      .use(plugin)
      .mount(el!);
  },
  progress: {
    color: resolveProgressColor(),
  },
});

// This will set light / dark mode on page load...
initializeTheme();
