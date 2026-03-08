import '@inertiajs/core';

declare module '@inertiajs/core' {
  interface PageProps {
    auth: import('./auth').Auth;
    csrf_token?: string;
    flash: {
      success?: string;
      error?: string;
      warning?: string;
      info?: string;
    };
  }
}
