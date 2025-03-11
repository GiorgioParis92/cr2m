<?php

namespace App\Http\Livewire;

use Livewire\Component;

namespace App\Http\Livewire;

use Livewire\Component;

class ConnectionStatus extends Component
{
    public  $connectionType = 'unknown';

    public function updatedConnectionType($value)
    {
        // This method runs any time connectionType changes
        // For example, you could log it or emit an event:
        // Log::info('New connection type: '.$value);
    }

    public function render()
    {
        return view('livewire.connection-status');
    }
}
