<?php

namespace App\Http\Livewire\Forms;

use App\Models\FormsData;


class Audio extends AbstractData
{



    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);

        $pdf=FormsData::where('dossier_id',$dossier_id)
        ->where('form_id',$form_id)
        ->where('meta_key',$conf['name'].'_pdf')->first();

        $this->pdf = $pdf->meta_value ?? false;

        print_r($conf);
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
        return view('livewire.forms.audio');
    }
}
