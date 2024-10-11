<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Card;

class CardBoard extends Component
{
    public $columns = [];
    public $newCardName = '';
    public $users = []; // List of users fetched from DB
    public $assignedUsers = []; // For multiple user selection
    protected $listeners = ['openAddCardModal' => 'openAddCardModal'];

    public function mount()
    {
        // Fetch existing cards from the database and structure them
        $this->loadCards();

        // Example user list, you can fetch from DB
        $this->users = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Developer Team'],
            ['id' => 3, 'name' => 'System Admin'],
        ];
    }

    public function loadCards()
    {
        $cards = Card::all(); // Fetch all cards from the DB
        $this->columns = [
            ['name' => 'To Do', 'tickets' => $cards->where('user_id', 14)->toArray()], // Assuming 'To Do' user_id = 14
            ['name' => 'In Progress', 'tickets' => $cards->where('user_id', 1)->toArray()], // Assuming 'In Progress' user_id = 1
        ];
    }

    public function addCardWithDetails()
{
    // Validate the card name and assigned users
    if (empty($this->newCardName)) {
        $this->addError('newCardName', 'The card name is required.');
        return;
    }

    if (empty($this->assignedUsers)) {
        $this->addError('assignedUsers', 'Please assign at least one user.');
        return;
    }

    // Create a new card
    $card = Card::create([
        'title' => $this->newCardName,
    ]);

    // Log for debugging
    dump(debug('New card created', ['card' => $card, 'assignedUsers' => $this->assignedUsers]));

    // Attach the selected users to the card
    $card->users()->attach($this->assignedUsers);

    // Reload the cards and reset form fields
    $this->loadCards();
    $this->reset(['newCardName', 'assignedUsers']);

    // Close the modal
    $this->dispatchBrowserEvent('hide-add-card-modal');
}

    
    
    public function openAddCardModal($columnIndex)
    {
        // Store the current column index in session for later use
        session(['current_column_index' => $columnIndex]);
    
        // Reset form fields (e.g., the card name and assigned users) to avoid showing old values
        $this->reset(['newCardName', 'assignedUsers']);
    
        // Dispatch a browser event to show the modal
        $this->dispatchBrowserEvent('show-add-card-modal');
    }
    public function render()
    {
        return view('livewire.card-board', [
            'columns' => $this->columns,
        ]);
    }
}
