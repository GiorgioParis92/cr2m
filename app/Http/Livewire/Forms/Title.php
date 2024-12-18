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
class Title extends Component
{
    public $conf;
    public $form_id;
    public $dossier_id;
    public $value;
    public $check_condition=true;
    public $listeners = [];


    public function mount($conf, $form_id, $dossier_id)
    {
        $this->conf = $conf;
        $this->form_id = $form_id;
        $this->dossier_id = $dossier_id;
        if(isset($this->conf->options)) {
            if (!is_array($this->conf->options)) {
                $jsonString = str_replace(["\n", '', "\r"], '', $this->conf->options);
                $optionsArray = json_decode($jsonString, true);
            } else {
                $optionsArray = $this->conf->options;
            }
        } else {
            $optionsArray =[]; 
        }
        $this->options=$optionsArray;
        if(isset($this->options['conditions'])) {
            $this->listeners=[];
            foreach($this->options['conditions'] as $tag=>$value) {

                $this->listeners[$tag]='handleFieldUpdated';

            }

            $check_condition=check_condition($this->options ?? '',$dossier_id);
            $this->check_condition=$check_condition;
        }

    }


    public function updatedValue($newValue)
    {

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

        return view('livewire.forms.title');
    }
}
