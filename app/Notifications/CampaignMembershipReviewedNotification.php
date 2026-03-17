<?php

namespace App\Notifications;

use App\Enums\CampaignMemberStatus;
use App\Models\CampaignMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignMembershipReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<int, string>  $channels
     */
    public function __construct(
        public CampaignMember $membership,
        protected array $channels,
    ) {
    }

    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'campaign_id' => $this->membership->campaign_id,
            'campaign_title' => $this->membership->campaign->title,
            'status' => $this->membership->status->value,
            'message' => $this->messageText(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Campaign request updated')
            ->line($this->messageText())
            ->action('View campaign', route('campaigns.show', $this->membership->campaign));
    }

    protected function messageText(): string
    {
        return match ($this->membership->status) {
            CampaignMemberStatus::ACTIVE => "Your request to join {$this->membership->campaign->title} was approved.",
            CampaignMemberStatus::REJECTED => "Your request to join {$this->membership->campaign->title} was declined.",
            default => "Your membership for {$this->membership->campaign->title} was updated.",
        };
    }
}
