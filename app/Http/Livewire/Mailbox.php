<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\UserMailSetting;
use App\Models\Email;
use Illuminate\Support\Facades\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailbox extends Component
{
    public $emails = [];
    public $folders = [];
    public $selectedFolder = 'INBOX';
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
        $this->loadFolders();
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

    public function loadFolders()
    {
     
        try {
            $imapConnection = imap_open(
                "{" . $this->mail_host . ":993/imap/ssl}",
                $this->mail_username,
                $this->mail_password
            );
    
            if ($imapConnection) {
            
                $this->folders = imap_list($imapConnection, "{" . $this->mail_host . "}", "*");

                imap_close($imapConnection);
                session()->flash('success', 'Good ' . imap_last_error());

            } else {
                session()->flash('error', 'Unable to load folders: ' . imap_last_error());
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading folders: ' . $e->getMessage());
        }
    }

    public function loadInbox($folder = 'INBOX')
    {
        $this->selectedFolder = $folder;
    
        try {
            // Connect to IMAP to retrieve emails from the selected folder
            $imapConnection = imap_open(
                "{" . $this->mail_host . ":993/imap/ssl}",
                $this->mail_username,
                $this->mail_password
            );
    
            if ($imapConnection) {
                $emails = imap_search($imapConnection, 'ALL');
                $this->emails = [];
    
                if ($emails) {
                    foreach ($emails as $emailId) {
                        $header = imap_headerinfo($imapConnection, $emailId);
    
                        // Fetch plain text content (part 1) and HTML content (part 2)
                        $plainText = imap_fetchbody($imapConnection, $emailId, 1); // Part 1 is plain text
                        $htmlText = imap_fetchbody($imapConnection, $emailId, 2);  // Part 2 is HTML
    
                        // // Decode the email content (if base64 or quoted-printable encoded)
                        $plainText = $this->decodeEmailContent($plainText);
                        $htmlText = $this->decodeEmailContent($htmlText);
    
                        $this->emails[] = [
                            'id' => $emailId,
                            'subject' => $header->subject ?? '(No Subject)',
                            'from' => $header->fromaddress ?? 'Unknown',
                            'date' => $header->date,
                            'plainText' => $plainText,
                            'htmlText' => $htmlText,
                        ];
                    }
                }
    
                imap_close($imapConnection);
            } else {
                session()->flash('error', 'Unable to load emails: ' . imap_last_error());
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading emails: ' . $e->getMessage());
        }
    }
    
    /**
     * Decode email content based on encoding type.
     *
     * @param string $content
     * @return string
     */
/**
 * Decode email content based on encoding type.
 *
 * @param string $content
 * @param string $encoding
 * @return string
 */
private function decodeEmailContent($content, $encoding = '7bit')
{
    try {
        switch (strtolower($encoding)) {
            case 'base64':
                return base64_decode($content) ?: $content;
            case 'quoted-printable':
                return quoted_printable_decode($content) ?: $content;
            case '7bit':
            case '8bit':
            default:
                return $content; // No decoding needed
        }
    } catch (\Exception $e) {
        // Log decoding error
        \Log::error('Error decoding email content: ' . $e->getMessage());
        return $content; // Return raw content if decoding fails
    }
}

    

    public function composeEmail()
    {
        $this->showCompose = true;
    }

    public function sendEmail()
    {
        try {
            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);

            // SMTP server configuration
            $mail->isSMTP();
            $mail->Host = $this->mail_host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->mail_username;
            $mail->Password = $this->mail_password;
            $mail->SMTPSecure = $this->mail_encryption ?: PHPMailer::ENCRYPTION_STARTTLS; // Use default encryption if not set
            $mail->Port = $this->mail_port;

            // Email configuration
            $mail->setFrom($this->mail_username, auth()->user()->name);
            $mail->addAddress($this->to);
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;
            $mail->isHTML(true);

            // Send the email
            $mail->send();

            // Log email in database
            Email::create([
                'subject' => $this->subject,
                'sender' => auth()->user()->email,
                'recipient' => $this->to,
                'body' => $this->body,
            ]);

            $this->showCompose = false;
            session()->flash('message', 'Email sent successfully!');
        } catch (Exception $e) {
            session()->flash('error', 'Failed to send email: ' . $e->getMessage());
        }
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

        session()->flash('message', 'Mail settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.mailbox', [
            'folders' => $this->folders,
            'emails' => $this->emails,
        ]);
    }
}
