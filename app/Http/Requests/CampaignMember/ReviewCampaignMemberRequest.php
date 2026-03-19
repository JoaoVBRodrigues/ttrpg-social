<?php

namespace App\Http\Requests\CampaignMember;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewCampaignMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['active', 'rejected'])],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
