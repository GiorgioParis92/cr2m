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
class Checkbox extends Component
{
    public $conf;
    public $form_id;
    public $dossier_id;
    public $value;
    public $display=true;
    protected $listeners = ['fieldUpdated' => 'handleFieldUpdated'];

    public function mount($conf, $form_id, $dossier_id)
    {
        $this->conf = $conf;
        $this->form_id = $form_id;
        $this->dossier_id = $dossier_id;

        $existingValue = FormsData::where('dossier_id', $dossier_id)
            ->where('form_id', $form_id)
            ->where('meta_key', $conf->name)
            ->first();

        if (!is_array($this->conf->options)) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->conf->options);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->conf->options;
        }

        $this->options = $optionsArray;
        $this->newValue = '';
        $this->value = $existingValue ? $existingValue->meta_value : '';
        $this->validateValue($this->value);

        $check_condition=check_condition($this->options ?? '',$dossier_id);
        $this->check_condition=$check_condition;
    }


    public function updatedValue($newValue)
    {
        if (!$newValue) {
            $newValue = 0;
        }

        $this->validateValue($newValue);

        $this->newValue = $newValue;
        $this->value = $newValue;

        FormsData::updateOrCreate(
            [
                'dossier_id' => $this->dossier_id,
                'form_id' => $this->form_id,
                'meta_key' => $this->conf->name
            ],
            [
                'meta_value' => $newValue
            ]
        );
        $this->emit('fieldUpdated', $this->conf->name, $newValue);
      

    }

    public function update_value($newValue)
    {
        if (!$newValue) {
            $newValue = 0;
        }

        $this->validateValue($newValue);

        $this->newValue = $newValue;
        $this->value = $newValue;

        FormsData::updateOrCreate(
            [
                'dossier_id' => $this->dossier_id,
                'form_id' => $this->form_id,
                'meta_key' => $this->conf->name
            ],
            [
                'meta_value' => $newValue
            ]
        );
        $this->emit('fieldUpdated', $this->conf->name, $newValue);

    }
    protected function validateValue($value)
    {
        // Reset errors first to ensure state is fresh

    }

    public function handleFieldUpdated()
    {
        $check_condition=check_condition($this->options ?? '',$this->dossier_id);
        $this->check_condition=$check_condition;
     

    }
    public function render()
    {
        return view('livewire.forms.checkbox');
    }
}
