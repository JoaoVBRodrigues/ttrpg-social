<?php

namespace App\Http\Requests\NotificationPreference;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'email_sessions_enabled' => ['nullable', 'boolean'],
            'email_invites_enabled' => ['nullable', 'boolean'],
            'email_messages_enabled' => ['nullable', 'boolean'],
            'in_app_sessions_enabled' => ['nullable', 'boolean'],
            'in_app_invites_enabled' => ['nullable', 'boolean'],
            'in_app_messages_enabled' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email_sessions_enabled' => $this->boolean('email_sessions_enabled'),
            'email_invites_enabled' => $this->boolean('email_invites_enabled'),
            'email_messages_enabled' => $this->boolean('email_messages_enabled'),
            'in_app_sessions_enabled' => $this->boolean('in_app_sessions_enabled'),
            'in_app_invites_enabled' => $this->boolean('in_app_invites_enabled'),
            'in_app_messages_enabled' => $this->boolean('in_app_messages_enabled'),
        ]);
    }
}
