<?php

namespace App\Http\Livewire\Forms;

use Livewire\Component;
use App\Models\{
    Dossier,
    Etape,
    DossiersActivity,
    User,
    Form,
    Forms,
    FormConfig,
    Rdv,
    RdvStatus,
    Client,
    FormsData,
    Card
};

class Photo extends AbstractData
{

    public $listeners = [
        'fileUploaded' => 'handleFileUploaded'
    ];

    public function handleFileUploaded($response)
    {
        // Here, $response should contain the file path or filename that was uploaded.
        // Assuming $response['file_path'] or something similar is returned.
        
        $filePath = $response['file_path'] ?? null;
        if ($filePath) {
            // $this->values is currently the array of existing values.
            // Add the new file to this array.
            $currentValues = $this->values;
            $currentValues[] = $filePath;
            
            // Update the property
            $this->values = $currentValues;
            $this->value = $currentValues; // keep them in sync if needed

            // Persist in the database
            FormsData::updateOrCreate(
                [
                    'dossier_id' => $this->dossier_id,
                    'form_id' => $this->form_id,
                    'meta_key' => $this->conf->name
                ],
                [
                    'meta_value' => json_encode($currentValues) // Store as JSON if multiple files
                ]
            );

            // After updating, Livewire will re-render the component automatically,
            // showing the newly uploaded file.
        }
    }
    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);

        if(!is_user_allowed($conf->name)) {
            return '';
        }
        $this->dossier=Dossier::find($dossier_id);
        $json_value=decode_if_json($this->value);
       
        // $json_value=json_decode($this->value);
        
        if($json_value) {
            $values=$json_value;
        }
        else {
            $values=[$this->value];
        }
        $this->value=$values;
        $this->values=$values;
    }

    public function getErrorMessage() {
        return '';
    }


    protected function validateValue($value): bool
    {

        return true;
    }


   


    public function render()
    {
        return view('livewire.forms.photo');
    }
}
