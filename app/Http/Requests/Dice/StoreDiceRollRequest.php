<?php

namespace App\Http\Requests\Dice;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiceRollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'expression' => ['required', 'string', 'max:100'],
            'session_id' => ['nullable', 'exists:campaign_sessions,id'],
        ];
    }
}
