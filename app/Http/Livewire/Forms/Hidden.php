<?php

namespace App\Http\Livewire\Forms;


class Hidden extends AbstractData
{



    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
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
        return view('livewire.forms.hidden');
    }
}
