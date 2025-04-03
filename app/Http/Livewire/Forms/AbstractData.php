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
    Beneficiaire,
    Card
};
use App\Services\CardCreationService;

use Illuminate\Support\Facades\DB;


abstract class AbstractData extends Component
{
    public $conf;
    public $form_id;
    public $dossier_id;
    public $value;
    public $request;
    public $readonly=false;
    public $check_condition=true;

    public $listeners = [];


    public function mount($conf, $form_id, $dossier_id)
    {
        $this->conf = $conf;
       
        $this->form_id = $form_id;
        $this->dossier_id = $dossier_id;

        $this->value = $this->getExistingValue($dossier_id, $form_id, $conf);

        if($this->form_id==3) {
            $dossier = Dossier::where('id', $this->dossier_id)->first();

            if ($dossier && isset($dossier->beneficiaire_id)) {
                $beneficiaireId = $dossier->beneficiaire_id;
                if (\Schema::hasColumn('beneficiaires', $this->conf['name'])) {
                $existingValue = Beneficiaire::where('id', $beneficiaireId)
                ->first();
                
                $this->value=$existingValue->{$conf['name']};
                }

            
            }
        }

        $this->_validateValue($this->value);
        
        if(!is_array($this->conf['options'])) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->conf['options']);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->conf['options'];
        }
    
     
 

        if(isset($this->conf['options'])) {
            if (!is_array($this->conf['options'])) {
                $jsonString = str_replace(["\n", '', "\r"], '', $this->conf['options']);
                $optionsArray = json_decode($jsonString, true);
            } else {
                $optionsArray = $this->conf['options'];
            }
            if(isset($optionsArray['sql'])) {
                $sql_command = $optionsArray['sql'];
                $this->sql_command=$sql_command;
                if (isset($optionsArray['arguments'])) {
                    foreach ($optionsArray['arguments'] as $key => $data) {
                        $sql_command = str_replace($key, eval ($data), $sql_command);
                    }
                }
        
                $request = DB::select($sql_command);
                $request = json_decode(json_encode($request), true);
                $this->request=$request;
            }



        } else {
            $optionsArray =[]; 
        }
        


        
        $this->options=$optionsArray;

        if(isset($this->options['conditions'])) {
            foreach($this->options['conditions'] as $tag=>$value) {
                $this->listeners[$tag]='handleFieldUpdated';
            }

            $check_condition=check_condition($this->options ?? '',$dossier_id);
            $this->check_condition=$check_condition;
        }
        if(isset($this->options['hidden']) && $this->options['hidden']==true) {
            $this->check_condition=false;
        }
        if(isset($this->options['readonly']) ) {
            $this->readonly=$this->options['readonly'];
        }


   
        if (isset($this->options['link'])) {
            $this->form_id = $this->options['link']['form_id'];
           
            $form_config=DB::table('forms_config')->where('form_id',$this->form_id)
            ->where('id',$this->options['link']['id'])
            ->first();

            $this->form_id=$form_config->form_id;

          

            $config = FormsData::
            where('form_id', $this->form_id)
            ->where('dossier_id', $dossier_id)
            ->where('meta_key', $this->conf['name'])
            ->first();

       
            $this->value = $config->meta_value ?? '';

            $jsonString = str_replace(["\n", ' ', "\r"], '', $form_config->options);
            $this->optionsArray = json_decode($jsonString, true);
           
        }

        $this->emit($this->conf['name']);
       
    }

    public function getExistingValue($dossier_id, $form_id, $conf) {
        $existingValue = FormsData::where('dossier_id', $dossier_id)
            ->where('form_id', $form_id)
            ->where('meta_key', $conf['name'])
            ->first();

        return $existingValue ? $existingValue->meta_value : '';
    }


    public function updatedValue($newValue)
    {
        $newValue=str_replace(',','.',$newValue);
        $this->_validateValue($newValue);
        $this->value=$newValue;

        // Always save, regardless of validity
        FormsData::updateOrCreate(
            [
                'dossier_id' => $this->dossier_id,
                'form_id' => $this->form_id,
                'meta_key' => $this->conf['name']
            ],
            [
                'meta_value' => $newValue
            ]
        );
        $dossier = Dossier::where('id', $this->dossier_id)->first();

        $card=app(CardCreationService::class)->checkAndCreateCard(
            $this->conf['name'],
            $newValue,
            $dossier,
            auth()->user()->id
        );
        if($this->form_id==3) {
            $dossier = Dossier::where('id', $this->dossier_id)->first();

            if ($dossier && isset($dossier->beneficiaire_id)) {
                $beneficiaireId = $dossier->beneficiaire_id;
            

                    // Update only if the key exists in the beneficiaires table columns
                    if (\Schema::hasColumn('beneficiaires', $this->conf['name'])) {
                        \DB::table('beneficiaires')->where('id', $beneficiaireId)->update([$this->conf['name'] => $newValue, 'updated_at' => now()]);

                    }
                    if (\Schema::hasColumn('dossiers', $this->conf['name'])) {
                        \DB::table('dossiers')->where('id', $dossier->id)->update([$this->conf['name'] => $newValue, 'updated_at' => now()]);

                    }                


            }
        }


        change_status($this->conf['name'], $newValue,$this->dossier_id);

        $this->emit($this->conf['name']);
    }

    private function _validateValue($value) {
        $this->resetErrorBag('value');
        if (!$this->validateValue($value))
        $this->addError('value', $this->getErrorMessage());
    }

    // Renvoie true si la valid est au bont format sinon false
    abstract protected function validateValue($value):bool;

    // Le message si la valeur n'est pas au bon format
    abstract protected function getErrorMessage();

    public function handleFieldUpdated()
    {
        $check_condition=check_condition($this->options ?? '',$this->dossier_id);
        $this->check_condition=$check_condition;
    }

    // La view a render

    abstract public function render();
}
