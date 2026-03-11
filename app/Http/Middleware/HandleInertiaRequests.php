<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Data\Auth\SharedAuthData;
use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'flash' => $this->sharedFlash($request),
            'auth' => $this->sharedAuth($request),
            'sidebarOpen' => $this->resolveSidebarOpen($request),
        ];
    }

    /**
     * Define the props that are shared once and remembered across navigations.
     *
     * @return array<string, mixed>
     */
    public function shareOnce(Request $request): array
    {
        return [
            'name' => fn (): string => config('app.name'),
        ];
    }

    /** @return array{success: Closure, error: Closure, warning: Closure, info: Closure} */
    private function sharedFlash(Request $request): array
    {
        $session = $request->hasSession() ? $request->session() : null;

        return [
            'success' => fn () => $session?->get('success'),
            'error' => fn () => $session?->get('error'),
            'warning' => fn () => $session?->get('warning'),
            'info' => fn () => $session?->get('info'),
        ];
    }

    /** @return array{user: array<string, mixed>|null, roles: list<string>, permissions: list<string>} */
    private function sharedAuth(Request $request): array
    {
        return SharedAuthData::fromRequest($request)->toArray();
    }

    private function resolveSidebarOpen(Request $request): bool
    {
        if (! $request->hasCookie('sidebar_state')) {
            return true;
        }

        return $request->cookie('sidebar_state') === 'true';
    }
}
