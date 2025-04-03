<?php

namespace App\Http\Livewire\Forms;
use App\Services\JsonValidator;

use Livewire\Component;

class Analyze extends AbstractData
{
    public $invalidGroups = [];

    public function mount($conf, $form_id, $dossier_id)
    {
        parent::mount($conf, $form_id, $dossier_id);

        $this->invalidGroups = (new JsonValidator())->getInvalidGroups($this->value);
    }

    public function getErrorMessage()
    {
        return 'La valeur ne peut être vide';
    }

    protected function validateValue($value): bool
    {
        return !($this->conf['required'] == 1 && empty($value));
    }

    public function render()
    {
        return view('livewire.forms.analyze', [
            'invalidGroups' => $this->invalidGroups
        ]);
    }
}
