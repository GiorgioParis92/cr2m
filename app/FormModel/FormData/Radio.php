<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Radio extends AbstractFormData
{
    
    public function render(bool $is_error)
    {
        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $class_prediction = '';

        if (!$this->check_value()) {
            $class_prediction = 'is-invalid';
        }

        $data = '<div class="form-group col-sm-12 '.($this->config->class ?? "").' group_' . $class_prediction . '">';
        $data .= '<div class="form-group">';

        // Clean and decode the JSON options
        $jsonString = str_replace(["\n", "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);

        $colors = ['3498DB', 'F1C40F', 'C0392B'];

        $data .= '<label>'.$this->config->title.'</label>';
        $data .= '<div>';

        if (is_array($optionsArray)) {
            foreach ($optionsArray as $key => $element) {
                $isChecked = $this->value == $element['value'] ? 'checked' : '';
                $backgroundColor = $element['color'] ?? ($colors[$key-1] ?? '3498DB');
                $backgroundColor = '';
                $data.='<div class="radio_line" style="background:#'.$backgroundColor.' ">';
                $data .= '<input id="'.$this->name.'_'.$key.'"
                    wire:model.lazy="'.$wireModel.'" 
                    wire:click="" 
                    value="'.$element['value'].'"
                    name="'.$this->config->name.'"
                    class="'.($this->value == $element['value'] ? 'choice_checked' : '').' "
                    data-radiocharm-background-color="'.$backgroundColor.'"
                    data-radiocharm-text-color="FFF" 
                    data-radiocharm-label="'.$element['label'].'" 
                    type="radio" '.$isChecked.'>';
                $data .= '<label  for="'.$this->name.'_'.$key.'">'.$element['label'].'</label><br>';
                $data .= '</div>';
            }
        }

        $data .= '</div>';
        $data .= '</div>';

        if (!$this->check_value()) {
            $data .= '<div  class="invalid-feedback">' . $this->get_error_message() . '</div>';

        }

        $data .= '</div>';
     
        return $data;
    }


    
    public function check_value() {
     
        if($this->config->required && $this->value=='') {
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

}
