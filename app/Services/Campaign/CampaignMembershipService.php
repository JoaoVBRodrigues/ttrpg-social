<?php

namespace App\Services\Campaign;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Events\CampaignMemberInvited;
use App\Events\CampaignMembershipReviewed;
use App\Exceptions\DomainException;
use App\Models\Campaign;
use App\Models\CampaignMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CampaignMembershipService
{
    public function requestToJoin(User $user, Campaign $campaign): CampaignMember
    {
        return DB::transaction(function () use ($user, $campaign): CampaignMember {
            $membership = CampaignMember::query()->firstOrNew([
                'campaign_id' => $campaign->getKey(),
                'user_id' => $user->getKey(),
            ]);

            if ($membership->exists && $membership->isActive()) {
                throw new DomainException('You are already part of this campaign.');
            }

            $membership->fill([
                'role' => $membership->role ?? CampaignMemberRole::PLAYER,
                'status' => CampaignMemberStatus::PENDING,
            ]);
            $membership->save();

            return $membership->refresh();
        });
    }

    public function inviteByUsername(User $actor, Campaign $campaign, string $username, string $role = CampaignMemberRole::PLAYER->value): CampaignMember
    {
        $invitee = User::query()->where('username', $username)->firstOrFail();

        if ($invitee->is($actor)) {
            throw new DomainException('You are already part of this campaign.');
        }

        return DB::transaction(function () use ($campaign, $invitee, $actor, $role): CampaignMember {
            $membership = CampaignMember::query()->firstOrNew([
                'campaign_id' => $campaign->getKey(),
                'user_id' => $invitee->getKey(),
            ]);

            if ($membership->exists && $membership->isActive()) {
                throw new DomainException('This user is already an active member.');
            }

            $membership->fill([
                'role' => $role,
                'status' => CampaignMemberStatus::INVITED,
                'invited_by' => $actor->getKey(),
            ]);
            $membership->save();

            DB::afterCommit(function () use ($membership): void {
                CampaignMemberInvited::dispatch($membership->fresh(['campaign', 'user.notificationPreference', 'inviter']));
            });

            return $membership->refresh()->load(['campaign', 'user.notificationPreference', 'inviter']);
        });
    }

    public function reviewMembership(CampaignMember $membership, string $status): CampaignMember
    {
        return DB::transaction(function () use ($membership, $status): CampaignMember {
            $shouldNotify = $membership->status?->value !== $status
                && in_array($status, [
                    CampaignMemberStatus::ACTIVE->value,
                    CampaignMemberStatus::REJECTED->value,
                ], true);

            $membership->status = $status;

            if ($status === CampaignMemberStatus::ACTIVE->value) {
                $membership->joined_at = now();
            }

            $membership->save();

            if ($shouldNotify) {
                DB::afterCommit(function () use ($membership): void {
                    CampaignMembershipReviewed::dispatch($membership->fresh(['campaign', 'user.notificationPreference']));
                });
            }

            return $membership->refresh()->load(['campaign', 'user.notificationPreference']);
        });
    }

    public function removeMember(CampaignMember $membership): CampaignMember
    {
        return DB::transaction(function () use ($membership): CampaignMember {
            $membership->status = CampaignMemberStatus::REMOVED;
            $membership->save();

            return $membership->refresh();
        });
    }

    public function leaveCampaign(User $user, Campaign $campaign): CampaignMember
    {
        $membership = CampaignMember::query()
            ->where('campaign_id', $campaign->getKey())
            ->where('user_id', $user->getKey())
            ->firstOrFail();

        if ($membership->role === CampaignMemberRole::GM) {
            throw new DomainException('Game masters cannot leave without transferring ownership first.');
        }

        $membership->status = CampaignMemberStatus::LEFT;
        $membership->save();

        return $membership->refresh();
    }
}
