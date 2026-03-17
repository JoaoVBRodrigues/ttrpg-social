<?php

namespace App\Http\Requests\Campaign;

use App\Enums\CampaignStatus;
use App\Enums\CampaignVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'game_system_id' => ['required', 'exists:game_systems,id'],
            'title' => ['required', 'string', 'max:120'],
            'synopsis' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'rules_summary' => ['nullable', 'string'],
            'max_players' => ['required', 'integer', 'min:1', 'max:12'],
            'visibility' => ['required', new Enum(CampaignVisibility::class)],
            'status' => ['nullable', new Enum(CampaignStatus::class)],
            'language' => ['required', 'string', 'max:12'],
            'timezone' => ['required', 'timezone'],
            'frequency_label' => ['nullable', 'string', 'max:120'],
        ];
    }
}
