<?php

namespace App\Services\User;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserProfileService
{
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $user->fill(Arr::only($data, [
                'name',
                'username',
                'email',
                'bio',
                'timezone',
                'preferred_role',
                'favorite_systems',
                'availability',
                'is_profile_public',
                'is_email_public',
            ]));

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            return $user->refresh();
        });
    }

    public function updateNotificationPreferences(User $user, array $data): NotificationPreference
    {
        return DB::transaction(function () use ($user, $data): NotificationPreference {
            $preference = $this->getOrCreateNotificationPreferences($user);
            $preference->fill($data);
            $preference->save();

            return $preference->refresh();
        });
    }

    public function getOrCreateNotificationPreferences(User $user): NotificationPreference
    {
        return $user->notificationPreference()->firstOrCreate([], [
            'email_sessions_enabled' => true,
            'email_invites_enabled' => true,
            'email_messages_enabled' => false,
            'in_app_sessions_enabled' => true,
            'in_app_invites_enabled' => true,
            'in_app_messages_enabled' => true,
        ]);
    }
}
