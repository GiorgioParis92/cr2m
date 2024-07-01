<?php

namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Condition extends AbstractFormData
{
    public function render(bool $is_error)
    {
        // Constructing the wire:model directive
        $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);
        $wireModel = "formData.{$this->form_id}.{$this->name}";
        $condition_valid=false;
        foreach ($optionsArray as $condition_config) {
       
            if ($this->check_condition($condition_config)) {
                $this->value = $condition_config['result'];
                $this->save_value();
                $condition_valid=true;
                break;
            }

        }

        if(!$condition_valid) {
            $this->value='';
            $this->save_value();
        }
        $data = '<div  class="form-group col-sm-12 ">';
        $data .= '<input wire:model.lazy="' . $wireModel . '" value="'.$this->value.'" id="' . $this->name . '"  class="form-control" type="text" name="' . $this->name . '"';


        if($this->value=='error') {
            $data.='ERROR';
        }

        $data .='</div>';






        return $data;
    }

    public function check_condition($condition_config)
    {

        
        foreach ($condition_config['conditions'] as $tag => $list_values) {
            if (!$this->match_value($tag, $list_values)) {
              
                return false;
            }
        }
        return true;
    }

    public function match_value($tag, $list_values)
    {
        $value = $this->getOtherValue($tag);
       
        foreach ($list_values as $list_value) {
            if ($value == $list_value) {
                return true;
            }
        }
        return false;
    }
}
