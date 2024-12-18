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

class Text extends Component
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
       
        $existingValue = FormsData::where('dossier_id', $dossier_id)
            ->where('form_id', $form_id)
            ->where('meta_key', $conf->name)
            ->first();
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

        $this->value = $existingValue ? $existingValue->meta_value : '';

        $this->validateValue($this->value);

        if(isset($this->options['conditions'])) {
            foreach($this->options['conditions'] as $tag=>$value) {
                $this->listeners[$tag]='handleFieldUpdated';

            }

            $check_condition=check_condition($this->options ?? '',$dossier_id);
            $this->check_condition=$check_condition;
        }
    }

    // This will be automatically called whenever $value changes
    public function updatedValue($newValue)
    {
        $this->validateValue($newValue);
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
    }


    protected function validateValue($value)
    {
    
        if ($this->conf->required==1 && empty($value)) {
            $this->addError('value', 'La valeur ne peut être vide');
        } else {
            $this->resetErrorBag('value');
        }
    }
    public function handleFieldUpdated()
    {
        $check_condition=check_condition($this->options ?? '',$this->dossier_id);
        $this->check_condition=$check_condition;
     

    }
    public function render()
    {
        return view('livewire.forms.text');
    }
}
