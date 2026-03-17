<?php

namespace App\Models;

use App\Enums\MessageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'session_id',
        'type',
        'content',
        'is_important',
        'metadata',
        'edited_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => MessageType::class,
            'is_important' => 'boolean',
            'metadata' => 'array',
            'edited_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CampaignSession::class, 'session_id');
    }

    public function diceRoll(): HasOne
    {
        return $this->hasOne(DiceRoll::class);
    }
}
