<?php

namespace App\Models;

use App\Enums\CampaignMemberRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'bio',
        'avatar_path',
        'timezone',
        'preferred_role',
        'favorite_systems',
        'availability',
        'is_profile_public',
        'is_email_public',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'favorite_systems' => 'array',
            'availability' => 'array',
            'is_profile_public' => 'boolean',
            'is_email_public' => 'boolean',
        ];
    }

    public function ownedCampaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'owner_id');
    }

    public function campaignMemberships(): HasMany
    {
        return $this->hasMany(CampaignMember::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_members')
            ->using(CampaignMember::class)
            ->withPivot(['id', 'role', 'status', 'joined_at', 'invited_by', 'review_message', 'reviewed_at'])
            ->withTimestamps();
    }

    public function createdSessions(): HasMany
    {
        return $this->hasMany(CampaignSession::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(SessionAttendance::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function diceRolls(): HasMany
    {
        return $this->hasMany(DiceRoll::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function activeMembershipFor(Campaign $campaign): ?CampaignMember
    {
        return $this->campaignMemberships
            ->firstWhere('campaign_id', $campaign->getKey());
    }

    public function canManageCampaign(Campaign $campaign): bool
    {
        $membership = $this->activeMembershipFor($campaign);

        if (! $membership || ! $membership->isActive()) {
            return false;
        }

        return in_array($membership->role, [CampaignMemberRole::GM, CampaignMemberRole::CO_GM], true);
    }
}
