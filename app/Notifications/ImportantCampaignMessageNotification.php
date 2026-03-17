<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportantCampaignMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<int, string>  $channels
     */
    public function __construct(
        public Message $message,
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
            'campaign_id' => $this->message->campaign_id,
            'message_id' => $this->message->id,
            'message' => "Important update in {$this->message->campaign->title}: {$this->message->content}",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Important campaign message')
            ->line("Important update in {$this->message->campaign->title}.")
            ->line($this->message->content)
            ->action('Open campaign', route('campaigns.show', $this->message->campaign));
    }
}
