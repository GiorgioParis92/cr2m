<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Etape;
use App\Models\Form;
use App\Models\Status;

class EditEtape extends Component
{
    public $etapes;
    public $statuts;
    public $editingEtapeId = null;
    public $newStatus;
    public $etapeData = [
        'etape_name' => '',
        'etape_desc' => '',
        'etape_style' => '',
        'etape_icon' => '',
    ];


    public function mount($id)
    {
        $etape = Etape::findOrFail($id);
        $this->editingEtapeId = $id;
        $this->etapeData = $etape->toArray();
        $this->loadStatus();


    }


    public function updateEtape()  
    {
        if ($this->editingEtapeId) {
            $etape = Etape::findOrFail($this->editingEtapeId);
            $etape->update($this->etapeData);
        }
        $this->etapes = Etape::orderBy('order_column')->get();
    }

    
    public function addStatus()
    {
        $data['status_desc']=$this->newStatus;
        $data['status_name']=$this->newStatus;
        $data['etape_id']=$this->editingEtapeId;
        Status::create($data);
        $this->loadStatus();
        $this->newStatus='';
    }

    public function updated()
    {
        foreach ($this->statuts as $statut) {
            // Find the status by id
            $existingStatus = Status::find($statut['id']);
    
            // Check if the status exists
            if ($existingStatus) {
                // Update the existing status
                $existingStatus->update([
                    'status_name' => $statut['status_name'],
                    'status_desc' => $statut['status_name'], // Ensure you're updating 'status_desc' correctly if it's meant to be different
                ]);
            }
        }
        $this->loadStatus();

    }
    
    
    public function deleteStatus($statusId)
    {
        Status::find($statusId)->delete();
        $this->loadStatus();


    }
    public function loadStatus()
    {
        $this->statuts=Status::where('etape_id',$this->editingEtapeId)->get()->toArray();
    }

    public function render()
    {
        return view('livewire.edit-etape');
    }
}
