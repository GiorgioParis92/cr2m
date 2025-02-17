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

class Operationtable extends AbstractData
{


    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
   
        if(auth()->user()->id==1) {
            $this->listeners[$this->options['table_name']]="loadTableValues";
            $this->loadTableValues();
        }

        
    }

    public function getErrorMessage() {
        return 'La valeur ne peut être vide';
    }


    protected function validateValue($value): bool
    {

        return !($this->conf['required']==1 && empty($value));
    }



    public function loadTableValues() {

        $table_name=$this->options['table_name'];
        $col_name=$this->options['col_name'];
        $operation=$this->options['operation'];

        $meta_key=$table_name.'.value.%.'.$col_name;
        $results=FormsData::where('meta_key','LIKE',$meta_key)
        ->where('form_id',$this->form_id)
        ->where('dossier_id',$this->dossier_id)
        ->get()->toArray();

        $this->value=0;
        foreach($results as $result) {
            $this->listeners[$result['meta_key']]="loadTableValues";
            $this->value=$this->calculateValue($this->value,$result['meta_value'],$operation);


        }

    }


    public function calculateValue($value1, $value2, $operation) {
        // Ensure values are numeric
        $value1 = is_numeric(str_replace(',', '.', $value1)) ? (float) str_replace(',', '.', $value1) : null;
        $value2 = is_numeric(str_replace(',', '.', $value2)) ? (float) str_replace(',', '.', $value2) : null;
    
        // If any value is invalid, return 0
        if ($value2 === null) {
            return $value1;
        }
    
        switch ($operation) {
            case "+":
                return $value1 + $value2;
    
            case "-":
                return $value1 - $value2;
    
            case "*":
                return $value1 * $value2;
    
            case "/":
                return ($value2 != 0) ? ($value1 / $value2) : 0; // Prevent division by zero
    
            default:
                return 0;
        }
    }
    

    public function render()
    {
        return view('livewire.forms.operationtable');
    }
}
