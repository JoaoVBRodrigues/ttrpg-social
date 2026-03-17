<?php

namespace App\Notifications;

use App\Models\CampaignMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignInviteNotification extends Notification implements ShouldQueue
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
            'message' => "You were invited to {$this->membership->campaign->title}.",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Campaign invitation')
            ->line("You were invited to {$this->membership->campaign->title}.")
            ->action('View campaign', route('campaigns.show', $this->membership->campaign));
    }
}
