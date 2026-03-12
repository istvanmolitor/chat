<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FriendRequestReceivedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly User $sender,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $appName = config('app.name');
        $logoUrl = url('/images/logo.webp');
        $senderProfileUrl = rtrim(config('app.frontend_url', config('app.url')), '/').'/users/'.$this->sender->id;

        return (new MailMessage)
            ->subject('Új ismerősnek jelölés érkezett')
            ->view('emails.friend-request-received', [
                'appName' => $appName,
                'logoUrl' => $logoUrl,
                'senderName' => $this->sender->name,
                'senderProfileUrl' => $senderProfileUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
