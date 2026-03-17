<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'type' => ['required', 'string', Rule::in([
                'useful_link',
                'house_rule',
                'system_note',
                'intro_material',
                'character_baseline',
            ])],
            'content' => ['nullable', 'string', 'required_without:external_url'],
            'external_url' => ['nullable', 'url:http,https', 'required_without:content', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
