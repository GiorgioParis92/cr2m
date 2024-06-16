<?php 

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use Illuminate\Support\Facades\URL;


class UserCreatedNotification extends Notification
{
    protected $user;
    protected $temporaryPassword;

    public function __construct(User $user, $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = URL::temporarySignedRoute(
            'temporary.password.reset', now()->addMinutes(60), ['email' => urlencode($this->user->email)]
        );

        return (new MailMessage)
            ->subject('Compte créé')
            ->view('emails.user-created', [
                'user' => $this->user,
                'temporaryPassword' => $this->temporaryPassword,
                'url' => $url
            ]);
    }
}
