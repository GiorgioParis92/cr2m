<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Radio extends AbstractFormData
{
    public function render(bool $is_error)
    {
        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $data = '<div class="form-group  col-sm-12 '.($this->config->class ?? "").'">';
        $data .= '<div class="form-group">';

       
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
    
            if (is_array($optionsArray)) {
            } else {
            }
            $colors = ['3498DB', 'F1C40F', 'C0392B'];
   
    
            $data .= '<label>'.$this->config->title.'</label>';
            $data .= '<div>';
            if (is_array($optionsArray)) {
                foreach ($optionsArray as $key => $element) {
                    $data .= '<input wire:model="'.$wireModel.'" ';
                    if ($this->value == $element['value']) {
                        $data .= 'checked';
                    }
                    $data .= 'value="'.$element['value'].'" style="width:100%"
                    name="'.$this->config->name.'"
                    class="';
                    if ($this->value == $element['value']) 
                    {
                        $data.='choice_checked';
                    }
                    $data.='"
                    data-radiocharm-background-color="'.($element['color'] ?? ($colors[$key] ?? '3498DB')) .'"
                    data-radiocharm-text-color="FFF" data-radiocharm-label="'.$element['label'] .'" type="radio">
                    ';
                }
   
            }
    
            $data .= '</div>';
            $data .= '</div>';
    
        $data .= '</div>';



        return $data;
    }

    

}