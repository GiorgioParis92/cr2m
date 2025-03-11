<?php

namespace App\Http\Livewire\Forms;


class Condition extends AbstractData
{


    public function mount($conf, $form_id, $dossier_id)
    {
        parent::mount($conf, $form_id, $dossier_id);
        $this->calculate_value();
    }

    public function match_value($value, $list_values)
    {
  

        foreach ($list_values as $list_value) {
            if ($value == $list_value) {
                return true;
            }
        }
        return false;
    }
    public function calculate_value()
    {


        if ($this->options) {
            foreach ($this->options as $option) {
                $this->value = $option['result'];
                foreach ($option['conditions'] as $tag => $values) {

                 
                    $this->listeners[$tag] = 'calculate_value';

                    $value = \App\Models\FormsData::where('dossier_id', $this->dossier_id)
                        ->where('meta_key', $tag)
                        ->value('meta_value');
                    
                    if (!$this->match_value($value, $values)) {
                        $this->value = '';
                       
                    }
                }

                if($this->value!='') {
                    break;
                }


            }
        

        }
        $this->updatedValue($this->value);

    }
    public function getErrorMessage()
    {
        return '';
    }


    protected function validateValue($value): bool
    {
        return true;
    }
    public function render()
    {
        return view('livewire.forms.condition');
    }
}
