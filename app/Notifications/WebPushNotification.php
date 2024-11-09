<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class WebPushNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['webpush'];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Test Notification')
            ->body('This is a test notification.')
            ->icon('/path/to/icon.png') // Ensure the icon path is correct
            ->action('View App', 'notification_action')
            ->data(['url' => 'https://your-app-url.com']);
    }
}
