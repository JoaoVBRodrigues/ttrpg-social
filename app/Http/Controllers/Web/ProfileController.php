<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\HandlesDomainExceptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationPreference\UpdateNotificationPreferenceRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\User\UserProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use HandlesDomainExceptions;

    public function edit(Request $request, UserProfileService $service): View|UserResource
    {
        $user = $request->user()->load('notificationPreference');
        $preferences = $service->getOrCreateNotificationPreferences($user);
        $user->setRelation('notificationPreference', $preferences);

        if ($request->expectsJson()) {
            return new UserResource($user);
        }

        return view('profile', [
            'user' => $user,
            'notificationPreferences' => $preferences,
        ]);
    }

    public function update(UpdateProfileRequest $request, UserProfileService $service)
    {
        return $this->handleAction($request, function () use ($request, $service) {
            $user = $service->updateProfile($request->user(), $request->validated())
                ->load('notificationPreference');

            if ($request->expectsJson()) {
                return new UserResource($user);
            }

            return redirect()
                ->route('profile')
                ->with('status', 'profile-updated');
        });
    }

    public function updatePreferences(UpdateNotificationPreferenceRequest $request, UserProfileService $service)
    {
        return $this->handleAction($request, function () use ($request, $service) {
            $service->updateNotificationPreferences($request->user(), $request->validated());

            $user = $request->user()->fresh()->load('notificationPreference');

            if ($request->expectsJson()) {
                return new UserResource($user);
            }

            return redirect()
                ->route('profile')
                ->with('status', 'notification-preferences-updated');
        });
    }

    public function showPublic(Request $request, User $user): View|UserResource
    {
        if (! $user->is_profile_public && ! $request->user()?->is($user)) {
            abort(404);
        }

        $user->load('notificationPreference');

        if ($request->expectsJson()) {
            return new UserResource($user);
        }

        return view('public-profile', [
            'profileUser' => $user,
            'profileResource' => new UserResource($user),
        ]);
    }
}
