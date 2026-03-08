<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

abstract class Controller
{
    protected function backWithSuccess(string $message): RedirectResponse
    {
        return back()->with('success', $message);
    }

    protected function redirectRouteWithSuccess(
        string $route,
        mixed $parameters,
        string $message,
    ): RedirectResponse {
        return redirect()->route($route, $parameters)
            ->with('success', $message);
    }
}
