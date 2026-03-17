<?php

namespace App\Services\Notification;

use App\Models\CampaignMember;
use App\Models\CampaignSession;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Notifications\CampaignInviteNotification;
use App\Notifications\CampaignMembershipReviewedNotification;
use App\Notifications\CampaignSessionScheduledNotification;
use App\Notifications\CampaignSessionUpdatedNotification;
use App\Notifications\SessionReminderNotification;

class NotificationService
{
    public function sendCampaignInvite(CampaignMember $membership): void
    {
        $recipient = $membership->user()->with('notificationPreference')->firstOrFail();
        $channels = $this->inviteChannelsFor($recipient);

        if ($channels === []) {
            return;
        }

        $recipient->notify(new CampaignInviteNotification($membership->load('campaign'), $channels));
    }

    public function sendCampaignMembershipReviewed(CampaignMember $membership): void
    {
        $recipient = $membership->user()->with('notificationPreference')->firstOrFail();
        $channels = $this->inviteChannelsFor($recipient);

        if ($channels === []) {
            return;
        }

        $recipient->notify(new CampaignMembershipReviewedNotification($membership->load('campaign'), $channels));
    }

    public function sendSessionScheduled(CampaignSession $session, ?int $actorId = null): void
    {
        $this->notifySessionMembers($session, $actorId, fn (array $channels) => new CampaignSessionScheduledNotification($session->load('campaign'), $channels));
    }

    public function sendSessionUpdated(CampaignSession $session, ?int $actorId = null): void
    {
        $this->notifySessionMembers($session, $actorId, fn (array $channels) => new CampaignSessionUpdatedNotification($session->load('campaign'), $channels));
    }

    public function sendSessionReminder(CampaignSession $session, User $recipient): void
    {
        $channels = $this->sessionChannelsFor($recipient);

        if ($channels === []) {
            return;
        }

        $recipient->notify(new SessionReminderNotification($session->load('campaign'), $channels));
    }

    protected function notifySessionMembers(CampaignSession $session, ?int $actorId, callable $factory): void
    {
        $session->campaign->members()
            ->where('status', 'active')
            ->with(['user.notificationPreference'])
            ->get()
            ->each(function ($membership) use ($session, $actorId, $factory): void {
                if ($membership->user_id === $actorId) {
                    return;
                }

                $channels = $this->sessionChannelsFor($membership->user);

                if ($channels === []) {
                    return;
                }

                $membership->user->notify($factory($channels));
            });
    }

    /**
     * @return array<int, string>
     */
    protected function inviteChannelsFor(User $user): array
    {
        $preference = $this->preferencesFor($user);
        $channels = [];

        if ($preference->in_app_invites_enabled) {
            $channels[] = 'database';
        }

        if ($preference->email_invites_enabled) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * @return array<int, string>
     */
    protected function sessionChannelsFor(User $user): array
    {
        $preference = $this->preferencesFor($user);
        $channels = [];

        if ($preference->in_app_sessions_enabled) {
            $channels[] = 'database';
        }

        if ($preference->email_sessions_enabled) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    protected function preferencesFor(User $user): NotificationPreference
    {
        return $user->notificationPreference()->firstOrCreate([], [
            'email_sessions_enabled' => true,
            'email_invites_enabled' => true,
            'email_messages_enabled' => false,
            'in_app_sessions_enabled' => true,
            'in_app_invites_enabled' => true,
            'in_app_messages_enabled' => true,
        ]);
    }
}
