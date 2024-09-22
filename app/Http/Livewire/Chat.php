<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\User;
use App\Models\Dossier;
class Chat extends Component
{
    use WithFileUploads;

    public $chatMessages;
    public $messageContent;
    public $dossier_id;
    public $form_id;
    public $count_messages;
    public $file;

    protected $rules = [
        'messageContent' => 'nullable|string',
        'file' => 'nullable|file|max:1024000',
    ];

    protected $messages = [];

    public function mount($dossier_id)
    {
        $this->dossier_id = $dossier_id;


        $this->refreshMessages();
    }

    public function refreshMessages()
    {
        $this->chatMessages = Message::with('user')
            ->where('dossier_id', $this->dossier_id)
            ->where('form_id', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        if (count($this->chatMessages) > $this->count_messages) {
            $this->emit('new_message');
        }

        $this->count_messages = count($this->chatMessages);
    }

    public function refresh()
    {
        $this->refreshMessages();
    }

    public function sendMessage()
    {
        $this->validate();

        if (empty($this->messageContent) && empty($this->file)) {
            $this->addError('messageContent', 'Veuillez saisir un message ou sélectionner un fichier.');
            return;
        }

        $filePath = null;

        if ($this->file) {
            // Get original filename
            $originalFilename = $this->file->getClientOriginalName();

            // Sanitize filename
            $safeFilename = pathinfo($originalFilename, PATHINFO_FILENAME);
            $extension = $this->file->getClientOriginalExtension();

            // Option 1: Use Str::slug to replace spaces and special characters
            $safeFilename = Str::slug($safeFilename);

            // Option 2: Remove unwanted characters but keep spaces and dots
            // $safeFilename = preg_replace('/[^\w\s.-]+/', '', $safeFilename);
            // $safeFilename = trim($safeFilename);

            // Limit filename length
            $safeFilename = Str::limit($safeFilename, 50, '');

            // Reconstruct safe filename
            $filename = $safeFilename . '.' . $extension;

            // Check for filename conflicts
            $i = 1;
            $filePath = 'chat_files/' . $filename;
            while (Storage::disk('public')->exists($filePath)) {
                $filename = $safeFilename . '_' . $i . '.' . $extension;
                $filePath = 'chat_files/' . $filename;
                $i++;
            }

            // Store the file
            $this->file->storeAs('chat_files', $filename, 'public');
        }

       $message= Message::create([
            'user_id' => auth()->user()->id,
            'dossier_id' => $this->dossier_id,
            'form_id' => 0,
            'content' => $this->messageContent,
            'file_path' => $filePath,
        ]);
        dd($message->id);
     
        $dossier=Dossier::where('id',$this->dossier_id)->first();

        $users = User::where('id', '>', 0)
        ->where(function($query) use ($dossier) {
            $query->where('client_id', $dossier->mar)
                  ->orWhere('client_id', $dossier->installateur)
                  ->orWhere(function($subQuery) use ($dossier) {
                      if ($dossier->mandataire_financier > 0) {
                          $subQuery->where('client_id', $dossier->mandataire_financier);
                      }
                  });
        })
        ->get();
        dd($users);


        $this->refreshMessages();

        // Reset input fields
        $this->messageContent = '';
        $this->file = null;
    
        // Dispatch browser event to reset the file input
        $this->dispatchBrowserEvent('clearFileInput');
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
