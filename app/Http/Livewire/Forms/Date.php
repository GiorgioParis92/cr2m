<?php

namespace App\Http\Livewire\Forms;

use App\http\Livewire\Forms\AbstractFormData;
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

class Date extends AbstractData
{


    protected $date_pattern = "/^(\d{2})\/(\d{2})\/(\d{4})$/";
   
    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
        $this->value=date("d/m/Y",strtotime(str_replace('/','-',$this->value)));
    }

    public function getErrorMessage() {
        return 'Mauvais format de date.';
    }

    protected function validateValue($value): bool
    {

        return !empty($value) && preg_match($this->date_pattern, $value);
    }

    public function render()
    {
        return view('livewire.forms.email');
    }
}
