<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\UserPosition;
use App\Models\User;

class MapTracker extends Component
{
    public $positionsByUser = [];

    public function mount()
    {
        $this->fetchPositions();
    }

    public function fetchPositions()
    {
        // Fetch positions with user data
        $positions = UserPosition::with('user') // Eager load the user relationship
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('lat', '!=', 0)
            ->where('lng', '!=', 0)
            ->get(['user_id', 'lat', 'lng', 'created_at']);

        // Group positions by user_id and include user name and color code
        $this->positionsByUser = $positions->groupBy('user_id')->map(function ($userPositions) {
            $user = $userPositions->first()->user;
            $userName = $user ? $user->name : 'Unknown User';

            // Generate color code using the helper function
            $colorCode = stringToColorCode($userName);

            return [
                'userName' => $userName,
                'colorCode' => $colorCode,
                'positions' => $userPositions->map(function ($pos) {
                    return [
                        'lat' => $pos->lat,
                        'lng' => $pos->lng,
                        'created_at' => $pos->created_at,
                    ];
                })->values(),
            ];
        })->toArray();

        // Dispatch updated positions to the JavaScript side
        $this->dispatchBrowserEvent('positions-updated', ['positionsByUser' => $this->positionsByUser]);
    }

    public function render()
    {
        return view('livewire.map-tracker');
    }
}
