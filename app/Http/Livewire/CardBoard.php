<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Card;
use App\Models\User;

class CardBoard extends Component
{
    public $columns = [];
    public $newCardName = '';
    public $dossier_id;
    public $display_dossier=true;
    public $users = []; // List of users fetched from DB
    public $assignedUsers = []; // For multiple user selection
    protected $listeners = ['moveCard' => 'moveCard', 'openAddCardModal' => 'openAddCardModal', 'addCardWithDetails' => 'addCardWithDetails', 'cardAdded' => 'loadCards','saveCard'=>'saveCard'];

    public function mount($id=null)
    {
        // Fetch existing cards from the database and structure them
        $this->loadCards();

        // Example user list, you can fetch from DB
        $this->users = User::all();

        if($id) {
            $this->dossier_id=$id;
        }
    }
    public function moveCard($ticketId, $newColumnIndex)
    {
        // Fetch existing cards from the database and structure them
        Card::where('id', $ticketId)
        ->update(['status' => $newColumnIndex,'archived_by'=>auth()->user()->id]);
        $this->loadCards();
    }

    public function loadCards()
    {
        $authUserId = auth()->user()->id;
    
        // Start building the query with related models
        $query = Card::with([
            'dossier' => function($query) {
                $query->with('beneficiaire', 'fiche', 'etape', 'status', 'mar_client');
            },
            'user',            // Load the user who assigned the card (assigned by)
            'users',           // Load the users assigned to the card
            'archivedByUser'   // Load the user who archived the card
        ])
        ->where(function ($query) use ($authUserId) {
            // Include cards where the authenticated user assigned the card or is assigned to the card
            $query->where('user_id', $authUserId) // User assigned the card
                  ->orWhereHas('users', function ($q) use ($authUserId) {
                      $q->where('user_id', $authUserId); // User is one of the assigned users
                  });
        });
    
        // If dossier_id is set, filter the query by dossier_id
        if (isset($this->dossier_id)) {
            $query->where('dossier_id', $this->dossier_id);
        }
    
        // Execute the query and get the results
        $cards = $query->get();
    
        // Prepare columns with tickets grouped by status
        $this->columns = [
            [
                'index' => 1,
                'name' => 'A faire', 
                'tickets' => $cards->where('status', 1)
                                   ->map(function($card) {
                                       $cardArray = $card->toArray();
                                       
                                       // The user who assigned the card (assigned by)
                                       $assignedBy = $card->user ? $card->user->name : 'Unknown';
    
                                       // The users assigned to the card (from the pivot table)
                                       $assignedTo = $card->users->pluck('name')->toArray();
    
                                       // The user who archived the card
                                       $archivedBy = $card->archivedByUser ? $card->archivedByUser->name : '';
    
                                       // Include the "Assigned by", "Assigned to", and "Archived by" information
                                       $cardArray['assigned_by'] = $assignedBy;
                                       $cardArray['assigned_to'] = $assignedTo;
                                       $cardArray['archived_by'] = $archivedBy;
    
                                       return $cardArray;
                                   })->toArray()
            ],
            [
                'index' => 2,
                'name' => 'En cours', 
                'tickets' => $cards->where('status', 2)
                                   ->map(function($card) {
                                       $cardArray = $card->toArray();
    
                                       $assignedBy = $card->user ? $card->user->name : 'Unknown';
                                       $assignedTo = $card->users->pluck('name')->toArray();
                                       $archivedBy = $card->archivedByUser ? $card->archivedByUser->name : '';
    
                                       $cardArray['assigned_by'] = $assignedBy;
                                       $cardArray['assigned_to'] = $assignedTo;
                                       $cardArray['archived_by'] = $archivedBy;
    
                                       return $cardArray;
                                   })->toArray()
            ],
            [
                'index' => 0,
                'name' => 'Archive', 
                'tickets' => $cards->where('status', 0)
                                   ->map(function($card) {
                                       $cardArray = $card->toArray();
    
                                       $assignedBy = $card->user ? $card->user->name : 'Unknown';
                                       $assignedTo = $card->users->pluck('name')->toArray();
                                       $archivedBy = $card->archivedByUser ? $card->archivedByUser->name : '';
    
                                       $cardArray['assigned_by'] = $assignedBy;
                                       $cardArray['assigned_to'] = $assignedTo;
                                       $cardArray['archived_by'] = $archivedBy;
    
                                       return $cardArray;
                                   })->toArray()
            ],
        ];


        if(isset($this->dossier_id)) {
            $this->display_dossier=false;
        }
    }
    
    
    
    
    
    
    
    public function testFunctions()
{
 
    
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

    
    
public function openAddCardModal($columnIndex = null, $dossierId = null)
{
    if ($dossierId) {
        $this->dossier_id = $dossierId;
        // Debug to ensure dossier_id is correctly set
    }
    // Reset form fields
    $this->reset(['newCardName', 'assignedUsers']);
    // Dispatch event to show the modal
    $this->dispatchBrowserEvent('show-add-card-modal');
}

    public function render()
    {
        return view('livewire.card-board', [
            'columns' => $this->columns,
        ]);
    }

  
}
