<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Form; 
use App\Models\Etape; 
use App\Models\Status; 

class EditEtapeForms extends Component
{
    public $etapeNumber;
    public $statuts;
    public $etape;
    public $forms = [];
    public $newForm = [
        'fiche_id' => '',
        'etape_id' => '',
        'etape_number' => '',
        'version_id' => '',
        'form_title' => '',
        'type' => '',
    ];
    public $formTypes = [];

    public function mount($id,$form_type)
    {
        $etapeNumber = $id;
        $this->etapeNumber = $id;
        $this->form_type = $form_type;
        $this->newForm['etape_id'] = $etapeNumber;    
        $this->newForm['etape_number'] = $etapeNumber;
        $this->loadEtape($id);
       
        $this->newForm['version_id'] = 1;
        $this->newForm['fiche_id'] = 1;

        if($this->form_type=='document') {
            $this->newForm['type'] = 'document';

        }

        $this->loadForms();
        $this->loadFormTypes();

        $this->statuts=Status::where('etape_id',$id)->get();


    }

    public function loadForms()
    {
        $this->forms = Form::where('etape_number', $this->etapeNumber)->get()->toArray();

    }

    public function updatedForms()
    {
        // Iterate over the forms and update each one in the database
        foreach ($this->forms as $form) {
            Form::where('id', $form['id'])->update($form);
        }
    }
    
    public function loadEtape($id)
    {
        $this->etape = Etape::findOrFail($id);
    }

    public function loadFormTypes()
    {
        $this->formTypes = Form::distinct()->pluck('type')->toArray();
    }

    public function addForm()
    {

        Form::create($this->newForm);

        $this->loadForms();
        $this->reset('newForm');

        // Re-set etape_id and etape_number after reset
        $this->newForm['etape_id'] = $this->etapeNumber;
        $this->newForm['etape_number'] = $this->etapeNumber;
        $this->newForm['version_id'] = 1;
        $this->newForm['fiche_id'] = 1;
    }

    public function deleteForm($formId)
    {
     
    }

    public function render()
    {
        return view('livewire.edit-etape-forms', ['formTypes' => $this->formTypes]);
    }
  
}
