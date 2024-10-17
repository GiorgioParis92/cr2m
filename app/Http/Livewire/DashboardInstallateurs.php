<?php

namespace App\Http\Livewire;

use Livewire\Component;



class DashboardInstallateurs extends Component
{
  
    public $devis;

    public function mount()
    {
        $this->devis=[1,2,3];
    }
    


    public function render()
    {
        return view('livewire.dashboard.devis');
    }

  
}
