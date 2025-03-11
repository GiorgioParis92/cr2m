<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Select extends AbstractFormData
{
  

        public function render(bool $is_error)
        {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
    
    
   
    
            $wireModel = "formData.{$this->form_id}.{$this->name}";
    
    
            $data = '<div class="form-group  col-sm-12 ';
            $data .= $this->config->class ?? '';
            $data .= '">';
            $data .= '<label>' . $this->config->title . '</label><br />';
            $data .= '<select wire:model="' . $wireModel . '" id="form_config_' . $this->name . '"';
            if ($this->config->required == 1) {
                $data .= ' required ';
            }
            $data .= 'name="' . $this->config->name . '" class="form-control ">';

    
            foreach ($optionsArray as $result) {
                foreach ($result as $k=>$v) {
            
                    $data .= '<option ';
    
                    if ($this->value == $v) {
                        $data .= ' selected ';
                    }
        
        
                    $data .= ' value="' . $v . '">' . $k . '</option>';
                }
           
          
             

            }
            $data .= '</select>';
    
            if (!$this->check_value()) {
                $data .= '<div  class="invalid-feedback">' . $this->get_error_message() . '</div>';
    
            }
    
            $data .= '</div>';
    
    
    
    
            return $data;
        }



}