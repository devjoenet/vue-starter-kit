<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\DeleteProfile;
use App\Actions\Settings\UpdateProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Support\Data\Settings\UpdateProfileData;
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
        UpdateProfile $updateProfile,
    ): RedirectResponse {
        $updateProfile->handle($request->user(), new UpdateProfileData(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
        ));

        return to_route('profile.edit');
    }

    public function destroy(
        ProfileDeleteRequest $request,
        DeleteProfile $deleteProfile,
    ): RedirectResponse {
        $user = $request->user();

        Auth::logout();

        $deleteProfile->handle($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
