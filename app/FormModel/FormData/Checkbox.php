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

 


        $data = '<div class="form-group col-sm-12';
        $data .= $this->config->class ?? '';
        $data .= '">';
        $data .= '<label>';
        $data .= $this->config->title;
        $data .= '</label><br />';
        if (is_array($optionsArray)) {
            $data .= '<label class="switch">';
            $data .= '<input wire:model="'.$wireModel.'"  type="hidden" name="';
            $data .= $this->config->name;
            $data .= '" value="';
            $data .= $optionsArray[0]['value'];
            $data .= '">';
            $data .= '<input type="checkbox" id="';
            $data .= $this->config->name;
            $data .= '" name="';
            $data .= $this->config->name;
            $data .= '"';
            $data .= ' value="';
            $data .= $optionsArray[1]['value'];
            $data .= '"';

            if ($this->value == $optionsArray[1]['value']) {
                $data .= ' checked';
            }
            $data .= ">";
            $data .= '<span class="slider round"></span>';
            $data .= '</label>';
            $data .= '<label class="custom-control-label" for="';
            $data .= $this->config->name;
            $data .= '">';
            $data .= $optionsArray[1]['label'];
            $data .= '</label>';
        }

        $data .= '</div>';
        return $data;
    }

}