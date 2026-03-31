<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Modules\Settings\Actions\UpdatePassword;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    public function update(
        PasswordUpdateRequest $request,
    ): RedirectResponse {
        UpdatePassword::handle($request->user(), $request->password);

        return back();
    }
}
