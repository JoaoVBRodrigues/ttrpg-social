<?php

namespace App\Http\Requests\Profile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:40',
                'regex:/^[A-Za-z0-9_]+$/',
                Rule::unique(User::class, 'username')->ignore($user->getKey()),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($user->getKey()),
            ],
            'bio' => ['nullable', 'string', 'max:2000'],
            'timezone' => ['required', 'timezone'],
            'preferred_role' => ['required', Rule::in(['player', 'gm', 'both'])],
            'favorite_systems' => ['nullable', 'array'],
            'favorite_systems.*' => ['string', 'max:120'],
            'availability' => ['nullable', 'array'],
            'availability.*.day' => ['required_with:availability', 'string', 'max:20'],
            'availability.*.window' => ['required_with:availability', 'string', 'max:80'],
            'is_profile_public' => ['nullable', 'boolean'],
            'is_email_public' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $favoriteSystems = collect(explode(',', (string) $this->input('favorite_systems')))
            ->map(fn (string $system): string => trim($system))
            ->filter()
            ->values()
            ->all();

        $availability = collect(preg_split('/\r\n|\r|\n/', (string) $this->input('availability_text')))
            ->map(function (string $line): ?array {
                if (! str_contains($line, ':')) {
                    return null;
                }

                [$day, $window] = array_map('trim', explode(':', $line, 2));

                if ($day === '' || $window === '') {
                    return null;
                }

                return [
                    'day' => $day,
                    'window' => $window,
                ];
            })
            ->filter()
            ->values()
            ->all();

        $this->merge([
            'username' => strtolower((string) $this->input('username')),
            'favorite_systems' => $favoriteSystems,
            'availability' => $availability,
            'is_profile_public' => $this->boolean('is_profile_public'),
            'is_email_public' => $this->boolean('is_email_public'),
        ]);
    }
}
