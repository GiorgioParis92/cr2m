<?php

namespace App\Http\Livewire\Forms;



class Radio extends AbstractData
{
    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
    }

    public function getErrorMessage() {
        return '';
    }




    public function update_value($newValue)
    {
        if (!$newValue) {
            $newValue = 0;
        }

        $this->updatedValue($newValue);
      

    }
    protected function validateValue($value):bool
    {
       return true;

    }

    public function render()
    {
        return view('livewire.forms.radio');
    }
}
