<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Card;
use App\Models\User;

class AddCardModal extends Component
{
    public $newCardName = '';
    public $dossier_id; // Dossier ID to be passed
    public $assignedUsers = [];
    public $users = []; // List of users fetched from DB

    protected $listeners = [ 'saveCard' => 'saveCard'];

    public function mount()
    {
        // Fetch available users
        $this->users = User::all();
    }

    public function openAddCardModal($dossierId = null)
    {
        // If dossier_id is passed, set it
        if ($dossierId) {
            $this->dossier_id = $dossierId;
        }
        // Reset form fields
        $this->reset(['newCardName', 'assignedUsers']);
        
        // Dispatch a browser event to show the modal
        $this->dispatchBrowserEvent('show-add-card-modal');
    }

    public function saveCard()
    {

        $this->validate([
            'newCardName' => 'required|string|max:255',
            'assignedUsers' => 'required|array|min:1',
        ]);

        // Create the card
        $card = Card::create([
            'title' => $this->newCardName,
            'user_id' => auth()->user()->id,
            'status' => 1,
            'dossier_id' => $this->dossier_id ?? 0, // Pass the dossier ID if it exists
        ]);

        // Attach assigned users
        $card->users()->attach($this->assignedUsers);

        // Close the modal and reset fields
        $this->dispatchBrowserEvent('hide-add-card-modal');
        $this->reset(['newCardName', 'assignedUsers']);

        // Optional: emit an event if you want to refresh the card board or other components
        $this->emit('cardAdded', $card->id);
    }

    public function render()
    {
        return view('livewire.add-card-modal');
    }
}
