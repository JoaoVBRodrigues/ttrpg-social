<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_sessions_enabled',
        'email_invites_enabled',
        'email_messages_enabled',
        'in_app_sessions_enabled',
        'in_app_invites_enabled',
        'in_app_messages_enabled',
    ];

    protected function casts(): array
    {
        return [
            'email_sessions_enabled' => 'boolean',
            'email_invites_enabled' => 'boolean',
            'email_messages_enabled' => 'boolean',
            'in_app_sessions_enabled' => 'boolean',
            'in_app_invites_enabled' => 'boolean',
            'in_app_messages_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
