<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Checkbox extends AbstractFormData
{

    
    public function render(bool $is_error)
    {
        if(!is_array($this->config->options)) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->config->options;
        }
        if (!is_array($optionsArray)) {
            $optionsArray = [];
        }

        if($this->value==0) {
            $this->value='';
            $this->save_value();
        }

        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $data = '<div class="form-group col-sm-12 ';
        $data .= $this->config->class ?? '';
        $data .= '">';
        if(isset($this->config->title)) {
            $data .= '<label>' . $this->config->title . '</label><br />';
        }
  
        $value=$optionsArray[1]['value'];

        if($this->value==$optionsArray[1]['value']) {
            $value=$optionsArray[0]['value'];
        }


        if (is_array($optionsArray)) {
            $data .= '<label class="switch" >';
            $data .= '<input type="checkbox" ';
            if ($this->value == "1") {
                $data .= 'checked';
            }
            $data .= ' id="checkbox_'.$this->config->name.'"
            wire:change="update_value(\''.$wireModel.'\',  '.$value.')"
            value="'.$optionsArray[1]['value'].'">';

            $data .= '<span class="slider round"></span>';
            $data .= '</label>';
            $data .= '<label class="custom-control-label" for="checkbox_'.$this->config->name.'">';
            $data .= $optionsArray[1]['label'] ?? 'Checkbox Label';
            $data .= '</label>';
        }

        $data .= '</div>';
        return $data;
    }


    public function render_pdf()
    {

        if(!$this->value) {
           return false; 
        }
        if(!is_array($this->config->options)) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->config->options;
        }
        if (!is_array($optionsArray)) {
            $optionsArray = [];
        }



        if($this->value==0) {
            $this->value='';
        }

        $wireModel = "formData.{$this->form_id}.{$this->name}";
        $data='';
        if($this->value) {
        $data .= '<div class="form-group col-sm-12 ';
        $data .= $this->config->class ?? '';
        $data .= '">';
        if(isset($this->config->title)) {
            $data .= '<label>' . $this->config->title . '</label><br />';
        }
  
        $value=$optionsArray[1]['value'];

        if($this->value==$optionsArray[1]['value']) {
            $value=$optionsArray[0]['value'];
        }

        $data.=$optionsArray;
        if (is_array($optionsArray)) {
            $data .= '<label class="switch" >';
           
            $data .= '<input type="checkbox" ';
            if ($this->value == "1") {
                $data .= 'checked="checked"';

            }
            $data .= ' value="'.$optionsArray[1]['value'].'">';

            $data .= '<span class="slider round"></span>';
            $data .= '</label>';
            $data .= '<label class="custom-control-label" for="checkbox_'.$this->config->name.'">';
            $data .= $optionsArray[1]['label'] ?? 'Checkbox Label';
            $data .= '</label>';
        
        }

        $data .= '</div>';
        }
        return $data;
    }

}
