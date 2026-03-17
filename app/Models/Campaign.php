<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\CampaignVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'game_system_id',
        'title',
        'slug',
        'synopsis',
        'description',
        'rules_summary',
        'max_players',
        'visibility',
        'status',
        'language',
        'timezone',
        'frequency_label',
        'next_session_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CampaignStatus::class,
            'visibility' => CampaignVisibility::class,
            'next_session_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function gameSystem(): BelongsTo
    {
        return $this->belongsTo(GameSystem::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CampaignMember::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CampaignSession::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function diceRolls(): HasMany
    {
        return $this->hasMany(DiceRoll::class);
    }

    public function references(): HasMany
    {
        return $this->hasMany(CampaignReference::class)->orderBy('sort_order')->orderBy('title');
    }
}
