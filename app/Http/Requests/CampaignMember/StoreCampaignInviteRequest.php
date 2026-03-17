<?php

namespace App\Http\Requests\CampaignMember;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', Rule::exists(User::class, 'username')],
            'role' => ['nullable', Rule::in(['player', 'spectator'])],
        ];
    }
}
