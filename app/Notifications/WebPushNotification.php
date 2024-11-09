<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\WebPushMessage;
use NotificationChannels\WebPush\WebPushMessage;

class WebPushNotification extends Notification
{
    public function via($notifiable)
    {
        return ['webpush'];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
        ->title('Test Notification')
        ->body('This is a test.');
    }
}
