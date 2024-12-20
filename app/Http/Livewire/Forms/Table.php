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

class Table extends AbstractData
{


    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
        
        $data=[];
        $newvalue=[];
        $is_old=false;
        try {
            $data=json_decode($this->value,true);
          
            $is_old=$this->isAssociativeJson($data);
           

        } catch (\Throwable $th) {
            //throw $th;
        }
        if($is_old) {
            foreach($data as $key=>$values) {
                $newvalue[]=$key;
                foreach($values as $tag=>$value) {
                    FormsData::updateOrCreate(
                        [
                            'dossier_id' => $this->dossier_id,
                            'form_id' => $this->form_id,
                            'meta_key' => $this->conf['name'].'.'.$key.'.'.$tag
                        ],
                        [
                            'meta_value' => $value['value']
                        ]
                    );
                }
    
            }
            $this->value=$newvalue;
            $this->updatedValue($this->value);
        } else {
            $this->value=$data;
        }


    }
    public function updatedValue($newValue)
    {
    

        // Always save, regardless of validity
        FormsData::updateOrCreate(
            [
                'dossier_id' => $this->dossier_id,
                'form_id' => $this->form_id,
                'meta_key' => $this->conf['name']
            ],
            [
                'meta_value' => json_encode($newValue)
            ]
        );
        $this->emit($this->conf['name']);
    }
    public function getErrorMessage() {
        return '';
    }


    protected function validateValue($value): bool
    {

        return true;
    }


    private function isAssociativeJson($json) {
        // Check if the input is an array
        if (!is_array($json)) {
            return false;
        }
    
        // Loop through the array and check if the keys are strings
        foreach ($json as $key => $value) {
            if (is_array($value) && !is_numeric($key)) {
                // If the value is an array and the key is not numeric, it's associative
                return true;
            }
        }
    
        // If none of the conditions match, it's not the associative JSON structure
        return false;
    }


    public function render()
    {
        return view('livewire.forms.table');
    }
}
