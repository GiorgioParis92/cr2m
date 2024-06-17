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
        $data .= '<label>';
        $data .= $this->config->title;
        $data .= '</label><br />';
        if (is_array($optionsArray)) {
            $data .= '<label class="switch">';
            $data .= '<input type="checkbox" 
            wire:model="'.$wireModel.'"
            value="1"
            name="'.$this->config->name.'"
            class="" '.(($this->value>0 && $this->value!="0") ? 'checked' : '').'>';
            $data .= '<span class="slider round"></span>';
            $data .= '</label>';
            $data .= '<label class="custom-control-label" for="';
            $data .= $this->config->name;
            $data .= '">';
            $data .= $optionsArray[1]['label'];
            $data .= '</label>';
        }
        $data .= ($this->config->name);
        $data .= ($this->value);
        $data .= '</div>';
        return $data;
    }

}