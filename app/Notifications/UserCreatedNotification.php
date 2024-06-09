<?php 

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

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
        return (new MailMessage)
            ->subject('Your Account has been Created')
            ->greeting('Hello ' . $this->user->name . ',')
            ->line('An account has been created for you. Here are your login details:')
            ->line('**Email:** ' . $this->user->email)
            ->line('**Temporary Password:** ' . $this->temporaryPassword)
            ->line('Please change your password after logging in.')
            ->action('Reset Password', url('/password/reset', ['email' => $this->user->email]));
    }
}
