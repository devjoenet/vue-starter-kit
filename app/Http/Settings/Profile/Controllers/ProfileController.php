<?php

declare(strict_types=1);

namespace App\Http\Settings\Profile\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Settings\Profile\Requests\ProfileDeleteRequest;
use App\Http\Settings\Profile\Requests\ProfileUpdateRequest;
use App\Modules\Settings\Actions\DeleteProfile;
use App\Modules\Settings\Actions\UpdateProfile;
use App\Modules\Settings\DTOs\UpdateProfileData;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    public function update(
        ProfileUpdateRequest $request,
    ): RedirectResponse {
        UpdateProfile::handle($request->user(), new UpdateProfileData(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
        ));

        return to_route('profile.edit');
    }

    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        DeleteProfile::handle($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
