import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';

const rootPages = import.meta.glob<DefineComponent>('../pages/**/*.vue');
const modulePages = import.meta.glob<DefineComponent>('../../../Modules/*/resources/js/Pages/**/*.vue');

const pages = {
  ...rootPages,
  ...modulePages,
};

export const resolveInertiaPage = (name: string): Promise<DefineComponent> => {
  const [moduleName, ...segments] = name.split('/');
  const modulePath = segments.length > 0 ? `../../../Modules/${moduleName}/resources/js/Pages/${segments.join('/')}.vue` : null;

  return resolvePageComponent([`../pages/${name}.vue`, ...(modulePath === null ? [] : [modulePath])], pages);
};
