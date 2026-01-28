import '@inertiajs/core';

declare module '@inertiajs/core' {
    interface PageProps {
        auth?: {
            user?: {
                id: number;
                name: string;
                email: string;
            } | null;
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
