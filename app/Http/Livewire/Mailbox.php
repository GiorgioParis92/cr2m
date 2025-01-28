<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\UserMailSetting;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class Mailbox extends Component
{
    public $emails = [];
    public $showCompose = false;
    public $to;
    public $subject;
    public $body;

    public $mail_host;
    public $mail_port;
    public $mail_username;
    public $mail_password;
    public $mail_encryption;

    public function mount()
    {
        $this->loadMailSettings();
    }

    public function loadMailSettings()
    {
        $mailSettings = auth()->user()->mailSettings;

        if ($mailSettings) {
         
            $this->mail_host = $mailSettings->mail_host;
            $this->mail_port = $mailSettings->mail_port;
            $this->mail_username = $mailSettings->mail_username;
            $this->mail_password = $mailSettings->mail_password;
            $this->mail_encryption = $mailSettings->mail_encryption;
        }
    }

    public function loadInbox()
    {
        $this->emails = Email::where('recipient', auth()->user()->email)->get();
   
        $this->showCompose = false;
    }

    public function loadSent()
    {
        $this->emails = Email::where('sender', auth()->user()->email)->get();
        $this->showCompose = false;
    }

    public function composeEmail()
    {
        $this->showCompose = true;
    }

    public function sendEmail()
    {
        // Configure the mailer dynamically
        Config::set('mail.mailers.smtp.host', $this->mail_host);
        Config::set('mail.mailers.smtp.port', $this->mail_port);
        Config::set('mail.mailers.smtp.username', $this->mail_username);
        Config::set('mail.mailers.smtp.password', $this->mail_password);
        Config::set('mail.mailers.smtp.encryption', $this->mail_encryption);

        Mail::raw($this->body, function ($message) {
            $message->to($this->to)
                    ->subject($this->subject);
        });

        Email::create([
            'subject' => $this->subject,
            'sender' => auth()->user()->email,
            'recipient' => $this->to,
            'body' => $this->body,
        ]);

        $this->showCompose = false;
        session()->flash('message', 'Email envoyé avec succès !');
    }

    public function saveMailSettings()
    {
        $this->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string',
        ]);

        UserMailSetting::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'mail_host' => $this->mail_host,
                'mail_port' => $this->mail_port,
                'mail_username' => $this->mail_username,
                'mail_password' => $this->mail_password,
                'mail_encryption' => $this->mail_encryption,
            ]
        );

        session()->flash('message', 'Paramètres de messagerie enregistrés avec succès.');
    }
    public function testConnection()
    {
        try {
            // Dynamically configure the mailer
            Config::set('mail.mailers.smtp.host', $this->mail_host);
            Config::set('mail.mailers.smtp.port', $this->mail_port);
            Config::set('mail.mailers.smtp.username', $this->mail_username);
            Config::set('mail.mailers.smtp.password', $this->mail_password);
            Config::set('mail.mailers.smtp.encryption', $this->mail_encryption);
    
            // Attempt to send a test email
            Mail::raw('This is a test email.', function ($message) {
                $message->to(auth()->user()->email)
                        ->subject('Test Email Connection');
            });
    
            session()->flash('message', 'Connexion réussie et email envoyé avec succès.');
        } catch (\Exception $e) {
            // Catch any errors and show a message
            session()->flash('error', 'Échec de la connexion : ' . $e->getMessage());
        }
    }
    
    public function render()
    {
     
        return view('livewire.mailbox');
    }
}
