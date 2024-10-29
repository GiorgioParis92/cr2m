<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\UserPosition; // Replace with your actual model namespace

class MapTracker extends Component
{
    public $positionsByUser = [];

    public function mount()
    {
        $this->fetchPositions();
    }

    public function fetchPositions()
    {
        $positions = UserPosition::whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('lat', '!=', 0)
            ->where('lng', '!=', 0)
            ->get(['user_id', 'lat', 'lng', 'created_at']);
    
        // Group positions by user_id
        $this->positionsByUser = $positions->groupBy('user_id')->map(function ($userPositions) {
            return $userPositions->map(function ($pos) {
                return [
                    'lat' => $pos->lat,
                    'lng' => $pos->lng,
                    'created_at' => $pos->created_at,
                ];
            })->values();
        })->toArray();
    
        // Dispatch updated positions to the JavaScript side
        $this->dispatchBrowserEvent('positions-updated', ['positionsByUser' => $this->positionsByUser]);
    }
    

    public function render()
    {
        return view('livewire.map-tracker');
    }
}
