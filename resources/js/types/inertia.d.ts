import "@inertiajs/core";

declare module "@inertiajs/core" {
  interface PageProps {
    auth?: {
      user?: import("./auth").User | null;
      roles?: string[];
      permissions?: string[];
    };
    csrf_token?: string;
    flash?: {
      success?: string;
      error?: string;
    };
  }
}
