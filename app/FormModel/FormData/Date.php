<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Date extends AbstractFormData
{

    public function check_value() {
        $pattern = "/^(\d{2})\/(\d{2})\/(\d{4})$/";
        return preg_match($pattern, $this->value) === 1;
    }

    public function get_error_message() {
        return "Mauvais format de date";
    }


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



        $data = '<div class="form-group col-sm-12 ' . ($this->config->class ?? "") . '  group_' . $class_prediction . '">';

        $data .= '<label>'.$this->config->title.'</label>';

        $data .= '<input wire:model="'.$wireModel.'"  class="form-control datepicker ' . $class_prediction.' ';
        if($is_error) {
            $data .=' error is-invalid';
        }
        $data.='" type="text" name="'.$this->config->name.'"';
        if ($this->config->required) {
            $data .= ' required ';
         } 
        $data .= 'value="'.($this->value).'">';
        if($is_error) {
            $data .='<div id="validationServerUsernameFeedback" class="invalid-feedback">'.$this->get_error_message().'</div>';
            }

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

}