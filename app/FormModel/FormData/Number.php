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
        $data .= '<input  wire:blur="update_value(\''.$wireModel.'\',  $event.target.value)" class="form-control ';

        if(!$this->check_value()) {
            $data .=' error is-invalid';
        }

        $data .='" type="text" name="'.$this->config->name.'"';
        if ($this->config->required) {
            $data .= ' required ';
         } 
        $data .= 'value="'.($this->generate_value()).'">';
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

        if($this->config->required && $this->generate_value() != '') {
            return is_numeric($this->generate_value());
        }
        if(!$this->config->required && $this->generate_value() == '') {
            return true;
        }
        return is_numeric($this->generate_value());
       
    }
    public function get_error_message() {

        return 'Veuillez entrer une valeur numérique';
    }

    public function render_pdf()
    {


        if(!$this->value || $this->value=='') {
            return '';
        }

        $data = '<div  class="form-group col-sm-12 ' . ($this->config->class ?? "") . '">';


        $data .= '<div class="s3" style="display:block;margin-top:15px;margin-bottom:8px">' . $this->config->title . '</div>';


        $data .= '<div style="display:block;margin-bottom:8px">'.$this->value.'</div>';

   

  
        $data .= '</div>';


        return $data;
    }

}