<?php

namespace App\Notifications;

use App\Models\CampaignSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignSessionScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<int, string>  $channels
     */
    public function __construct(
        public CampaignSession $session,
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
            'campaign_id' => $this->session->campaign_id,
            'session_id' => $this->session->id,
            'message' => "A new session was scheduled for {$this->session->campaign->title}.",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New campaign session scheduled')
            ->line("A new session was scheduled for {$this->session->campaign->title}.")
            ->line("Starts at {$this->session->starts_at?->toDayDateTimeString()} UTC")
            ->action('Open campaign', route('campaigns.show', $this->session->campaign));
    }
}
