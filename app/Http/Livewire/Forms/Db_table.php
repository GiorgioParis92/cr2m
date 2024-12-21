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

class Db_table extends AbstractData
{


    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
    }

    public function getErrorMessage() {
        return 'La valeur ne peut être vide';
    }


    protected function validateValue($value): bool
    {

        return !($this->conf['required']==1 && empty($value));
    }

    public function render()
    {
        return view('livewire.forms.db_table');
    }
}
