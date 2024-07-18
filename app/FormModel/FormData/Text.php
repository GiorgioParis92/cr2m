<?php

namespace App\FormModel\FormData;

class Text extends AbstractFormData
{
    public function render(bool $is_error)
    {
        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $class_prediction = '';
        if ($this->prediction) {
            $class_prediction = ' prediction';
        }
        if (!$this->check_value()) {
            $class_prediction = ' is-invalid';
        }

        $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);

        $optionsArray = json_decode($jsonString, true);

        $condition_valid = false;

        if(isset($optionsArray['conditions'])) {
          
            foreach ($optionsArray as $key=>$condition_config) {
                if($key=='conditions') {
                    if ($this->check_condition($condition_config)) {
                
                        $condition_valid = true;
        
                        if (isset($condition_config['operation']) && $condition_config['operation'] == 'AND') {
                            break;
                        }
        
                    } else {
                        $condition_valid = false; 
                    }
                }

    
            }
        } else {
            $condition_valid = true;
        }


  
        if( $condition_valid == false) {
            return '';
        }

        $data = '<div wire:model class="form-group col-sm-12 ' . ($this->config->class ?? "") . '  group_' . $class_prediction . '">';


        $data .= '<label style="display:inline-block">' . $this->config->title . '</label>';


       $data .= $this->generate_loading();


        $data .= '<input wire:blur="update_value(\''.$wireModel.'\',  $event.target.value)" value="'.$this->value.'"   class="form-control ' . $class_prediction . '" type="text" name="' . $this->name . '"';

        if ($this->config->required) {
            $data .= ' required ';
        }
        $data .= ' value="' . ($this->value) . '">';

        if (!$this->check_value()) {
            $data .= '<div  class="invalid-feedback">' . $this->get_error_message() . '</div>';

        } else {
            if ($this->prediction) {
                $data .= '<span class="prediction">Predicted by OCEER</span>';
            }
        }
        $data .= '</div>';


        return $data;
    }

    public function check_value()
    {

        if ($this->config->required && $this->value == '') {
            return false;
        }

        return true;
    }

    public function get_error_message()
    {
        if ($this->config->required && $this->value == '') {
            return 'La valeur ne peut pas être vide';
        }

        return 'Erreur';
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

    public function check_condition($condition_config)
    {
        foreach ($condition_config as $tag => $list_values) {
            if (!$this->match_value($tag, $list_values)) {

                return false;
            }
        }
    
        return true;
    }
}
