<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Number extends AbstractFormData
{
    public function render(bool $is_error)
    {
        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $data = '<div class="form-group  col-sm-12 '.($this->config->class ?? "").'">';
        $data .= '<label>'.$this->config->title.'</label>';
        $data .= '<input wire:model="'.$wireModel.'" class="form-control ';

        if(!$this->check_value()) {
            $data .=' error is-invalid';
        }

        $data .='" type="text" name="'.$this->config->name.'"';
        if ($this->config->required) {
            $data .= ' required ';
         } 
        $data .= 'value="'.($this->value).'">';
        if(!$this->check_value()) {
        $data .='<div id="validationServerUsernameFeedback" class="invalid-feedback">'.$this->get_error_message().'</div>';
        }
        $data .= '</div>';



        return $data;
    }

    public function generate_value() {
        return str_replace(',', '.', $this->value);
    }

    public function check_value() {

        return is_numeric($this->generate_value());
    }
    public function get_error_message() {

        return 'Veuillez entrer une valeur numérique';
    }

}