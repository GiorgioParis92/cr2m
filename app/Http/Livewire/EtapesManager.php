<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Etape;
use App\Models\Status;

class EtapesManager extends Component
{
    public $etapes;

    public $editingEtapeId = null;
    public $etapeData = [
        'etape_name' => '',
        'etape_desc' => '',
        'etape_style' => '',
        'etape_icon' => '',
    ];

    protected $rules = [
        'etapeData.etape_name' => 'required|string|max:100',
        'etapeData.etape_desc' => 'required|string|max:100',
        'etapeData.etape_style' => 'nullable|string|max:100',
        'etapeData.etape_icon' => 'nullable|string|max:100',
    ];

    public function mount()
    {
        $this->etapes = Etape::orderBy('order_column')->get();

    }

    public function editEtape($id)
    {
        $this->emit('triggerEditModal');
        $etape = Etape::findOrFail($id);
        $this->editingEtapeId = $id;
        $this->etapeData = $etape->toArray();
    }
    public function updateEtape()
    {
        $this->validate();
    
        if ($this->editingEtapeId) {
            $etape = Etape::findOrFail($this->editingEtapeId);
            $etape->update($this->etapeData);
        }
        $this->etapes = Etape::orderBy('order_column')->get();
        $this->emit('closeEditModal');

        $this->reset('editingEtapeId', 'etapeData');
    
        // Emit an event to close the modal
    }
    public function addEtape()
    {
        // $this->validate();
        // $this->reset('editingEtapeId', 'etapeData');

        // Get the highest order_column value and add 1
        $maxOrderColumn = Etape::max('order_column');

        $etapeData['order_column'] = $maxOrderColumn + 1;
        $etapeData['etape_desc'] = 'new';
        $etapeData['etape_name'] = 'new';
    
        $new_etape=Etape::create($etapeData);

        $this->etapes = Etape::orderBy('order_column')->get();
        $this->reset('etapeData');
    }

    public function reorderEtapes($ids)
    {
        foreach ($ids as $index => $id) {
            Etape::where('id', $id)->update(['order_column' => $index]);
        }

        $this->etapes = Etape::orderBy('order_column')->get();
    }

    public function render()
    {
        return view('livewire.etapes-manager');
    }
}
