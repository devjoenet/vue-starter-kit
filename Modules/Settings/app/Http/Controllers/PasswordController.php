<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Settings\Actions\UpdatePassword;
use Modules\Settings\Http\Requests\PasswordUpdateRequest;

class PasswordController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Settings/Password');
    }

    #[Middleware('throttle:6,1')]
    public function update(
        PasswordUpdateRequest $request,
    ): RedirectResponse {
        UpdatePassword::handle($request->user(), $request->password);

        return back();
    }
}
