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

class Phone extends AbstractData
{

    protected $frenchPhonePattern = '/^(?:(?:\+|00)33|0)[1-9](?:[\s\.\-]?\d{2}){4}$/';

    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
    }

    public function getErrorMessage() {
        return 'Le numéro de téléphone n\'est pas au format français.';
    }


    protected function validateValue($value): bool
    {

        return !(!empty($value) && !preg_match($this->frenchPhonePattern, $value));
    }


   


    public function render()
    {
        return view('livewire.forms.phone');
    }
}
