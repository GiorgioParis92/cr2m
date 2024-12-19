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
