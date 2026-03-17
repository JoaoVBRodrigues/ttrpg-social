<?php

namespace App\Models;

use App\Enums\CampaignSessionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignSession extends Model
{
    use HasFactory;

    protected $table = 'campaign_sessions';

    protected $fillable = [
        'campaign_id',
        'created_by',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'timezone',
        'status',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => CampaignSessionStatus::class,
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(SessionAttendance::class, 'session_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'session_id');
    }
}
