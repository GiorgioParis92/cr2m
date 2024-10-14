<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Card;
use App\Models\User;
use App\Models\UserType;

class AddCardModal extends Component
{
    public $newCardName = '';
    public $dossier_id;
    public $assignedUsers = [];
    public $type_users_selected = [];
    public $type_users = [];
    public $users = [];

    protected $listeners = [
        'saveCard'=>'saveCard',
        'hide_add_card_modal' => 'hide_add_card_modal',
    ];

    public function mount()
    {
        $this->users = User::all();
        $this->type_users = UserType::where('id', '>', 1)->get();
    }

    public function openAddCardModal($dossierId = null)
    {
        if ($dossierId) {
            $this->dossier_id = $dossierId;
        }
        $this->reset(['newCardName', 'assignedUsers', 'type_users_selected']);
        $this->dispatchBrowserEvent('show-add-card-modal');
    }

    public function saveCard($assignedUsers, $type_users_selected)
    {
        // Assign the values to the component's properties
        $this->assignedUsers = $assignedUsers;
        $this->type_users_selected = $type_users_selected;
        // Validation logic here...

        // Create the card
        $card = Card::create([
            'title' => $this->newCardName,
            'user_id' => auth()->user()->id,
            'status' => 1,
            'dossier_id' => $this->dossier_id ?? 0,
        ]);

        // Attach assigned users
        $card->users()->attach($this->assignedUsers);

        if (!empty($this->type_users_selected)) {

            foreach ($this->type_users_selected as $typeUserId) {
                $usersByType = User::where('type_id', $typeUserId)->get();
            
                foreach ($usersByType as $user) {
                    $card->users()->attach($user->id);
                }
            }

        }

        // Close the modal and reset fields
        $this->dispatchBrowserEvent('hide-add-card-modal');
        $this->reset(['newCardName', 'assignedUsers', 'type_users_selected']);

        // Emit events for UI updates
        $this->emit('cardAdded', $card->id);
    }

    public function hide_add_card_modal()
    {
        $this->dispatchBrowserEvent('hide-add-card-modal');
    }

    public function render()
    {
        return view('livewire.add-card-modal');
    }
}
