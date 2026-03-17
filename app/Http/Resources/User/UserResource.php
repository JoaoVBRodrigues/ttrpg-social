<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $viewer = $request->user();
        $canViewPrivateEmail = $viewer?->is($this->resource) || $this->is_email_public;
        $canViewPreferences = $viewer?->is($this->resource);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_path ? asset('storage/'.$this->avatar_path) : null,
            'timezone' => $this->timezone,
            'preferred_role' => $this->preferred_role,
            'favorite_systems' => $this->favorite_systems ?? [],
            'availability' => $this->availability ?? [],
            'is_profile_public' => (bool) $this->is_profile_public,
            'email' => $canViewPrivateEmail ? $this->email : null,
            'email_verified_at' => $canViewPreferences ? $this->email_verified_at?->toIso8601String() : null,
            'notification_preferences' => $this->when(
                $canViewPreferences && $this->relationLoaded('notificationPreference'),
                fn (): array => [
                    'email_sessions_enabled' => (bool) $this->notificationPreference?->email_sessions_enabled,
                    'email_invites_enabled' => (bool) $this->notificationPreference?->email_invites_enabled,
                    'email_messages_enabled' => (bool) $this->notificationPreference?->email_messages_enabled,
                    'in_app_sessions_enabled' => (bool) $this->notificationPreference?->in_app_sessions_enabled,
                    'in_app_invites_enabled' => (bool) $this->notificationPreference?->in_app_invites_enabled,
                    'in_app_messages_enabled' => (bool) $this->notificationPreference?->in_app_messages_enabled,
                ],
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
