<?php

namespace App\Http\Livewire\Forms;


class Result extends AbstractData
{


    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
        $this->calculate_value();
    }


    public function calculate_value()
    {
        $this->value=null;

        if($this->options && $this->options['operands'] ) {
            foreach($this->options['operands'] as $operands) {
                foreach($operands['tags'] as $tag) {

                if(is_numeric($tag)) {
                    $value=$tag;
                } else {
                    $this->listeners[$tag]='calculate_value';

                    $value = \App\Models\FormsData::where('dossier_id', $this->dossier_id)
                    ->where('meta_key', $tag)
                    ->value('meta_value');
                }

      

                $a=getValueFromOperation($this->value,$operands['operand']);
                $b=getValueFromOperation($value,$operands['operand']);
                $this->value=performOperation($a,$b,$operands['operand']);

            }
        }


        }
        $this->value=number_format($this->value, 2, '.', '');
        $this->updatedValue($this->value);

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
        return view('livewire.forms.result');
    }
}
