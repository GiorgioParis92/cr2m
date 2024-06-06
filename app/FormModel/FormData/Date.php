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

        $data = '<div class="form-group  col-sm-12 '.($this->config->class ?? "").'">';
        $data .= '<label>'.$this->config->title.'</label>';
        $data .= '<input class="form-control datepicker ';
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
        $data .= '</div>';


        return $data;
    }

}