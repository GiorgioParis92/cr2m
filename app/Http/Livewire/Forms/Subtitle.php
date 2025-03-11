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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class Subtitle extends AbstractData
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

        return view('livewire.forms.subtitle');
    }
}
