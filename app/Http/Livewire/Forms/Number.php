<?php

namespace App\Http\Livewire\Forms;



class Number extends AbstractData
{



   
    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
    }

    public function getErrorMessage() {
        return 'La valeur n\'est pas un nombre.';
    }


    protected function validateValue($value): bool
    {

        return !(!empty($value) && !is_numeric($value));
    }


    public function render()
    {
        return view('livewire.forms.number');
    }
}
