<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public $messages;
    public $messageContent;
    public $dossier_id;
    public $count_messages;

    protected $rules = [
        'messageContent' => 'required|string|max:255',
    ];

    public function mount($dossier_id)
    {
        $this->dossier_id = $dossier_id;
        $this->messages = Message::with('user')
            ->where('dossier_id', $this->dossier_id)

            ->orderBy('created_at', 'asc') // Correct placement of orderBy

            ->get();
        $this->count_messages = count($this->messages);
    }

    public function refresh()
    {
        $this->messages = Message::with('user')
            ->where('dossier_id', $this->dossier_id)

            ->orderBy('created_at', 'asc') // Correct placement of orderBy

            ->get();


        if (count($this->messages) > $this->count_messages) {
            $this->emit('new_message');

        }
        $this->count_messages = count($this->messages);




    }
    public function sendMessage()
    {

        Message::create([
            'user_id' => auth()->user()->id,
            'dossier_id' => $this->dossier_id,
            'content' => $this->messageContent,
        ]);

        $this->messages = Message::with('user')
            ->where('dossier_id', $this->dossier_id)

            ->orderBy('created_at', 'asc') // Correct placement of orderBy

            ->get();

        $this->messageContent = '';
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
