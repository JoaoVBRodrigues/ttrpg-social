<?php

namespace App\Models;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CampaignMember extends Pivot
{
    use HasFactory;

    protected $table = 'campaign_members';

    public $incrementing = true;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'role',
        'status',
        'joined_at',
        'invited_by',
        'review_message',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => CampaignMemberRole::class,
            'status' => CampaignMemberStatus::class,
            'joined_at' => 'datetime',
            'reviewed_at' => 'datetime',
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

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isActive(): bool
    {
        return $this->status === CampaignMemberStatus::ACTIVE;
    }
}
