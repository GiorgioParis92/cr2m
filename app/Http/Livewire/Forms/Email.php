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

class Email extends AbstractData
{


    protected $email_pattern = '/[\w.+-]+@[\w-]+\.[\w.-]+/';
   
    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
    }

    public function getErrorMessage() {
        return 'Mauvais format d\'email.';
    }

    protected function validateValue($value): bool
    {
        return !empty($value) && preg_match($this->email_pattern, $value);
    }

    public function render()
    {
        return view('livewire.forms.email');
    }
}
