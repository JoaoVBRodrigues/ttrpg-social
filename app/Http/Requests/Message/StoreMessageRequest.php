<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
            'session_id' => ['nullable', 'exists:campaign_sessions,id'],
            'is_important' => ['nullable', 'boolean'],
        ];
    }
}
