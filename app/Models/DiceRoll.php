<?php

namespace App\Models;

use App\Enums\DiceRollMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiceRoll extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'session_id',
        'user_id',
        'message_id',
        'expression',
        'normalized_expression',
        'dice_results',
        'modifiers',
        'total',
        'roll_mode',
        'rolled_at',
    ];

    protected function casts(): array
    {
        return [
            'dice_results' => 'array',
            'modifiers' => 'array',
            'roll_mode' => DiceRollMode::class,
            'rolled_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CampaignSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
