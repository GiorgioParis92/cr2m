<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Checkbox extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);
        if (!is_array($optionsArray)) {
            $optionsArray = [];
        }

        $wireModel = "formData.{$this->form_id}.{$this->name}";

 
        $data = '<div class="form-group col-sm-12 ';
        $data .= $this->config->class ?? '';
        $data .= '">';
   
        if (is_array($optionsArray)) {
            $data .= '<label class="switch" >';
            $data .= '<input type="checkbox" 
            id="checkbox_'.$this->config->name.'"
            wire:model="'.$wireModel.'"
            value="1"
            name="'.$this->config->name.'"
             >';
            $data .= '<span class="slider round"></span>';
            $data .= '</label>';
            $data .= '<label class="custom-control-label" for="checkbox_'.$this->config->name.'"';
            $data .= $this->config->name;
            $data .= '>';
            $data .= $optionsArray[1]['label'];
            $data .= '</label>';
        }
   
        $data .= '</div>';
        return $data;
    }

}