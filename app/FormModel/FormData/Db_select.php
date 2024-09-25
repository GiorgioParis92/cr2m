<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\View;

class Db_select extends AbstractFormData
{
    public function render(bool $is_error)
    {
        if(!is_array($this->config->options)) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->config->options;
        }


        $sql_command = $optionsArray['sql'];
      
        if (isset($optionsArray['arguments'])) {
            foreach ($optionsArray['arguments'] as $key => $data) {
                $sql_command = str_replace($key, eval ($data), $sql_command);
            }
        }
        $class_prediction = '';
        if (!$this->check_value()) {
            $class_prediction = ' is-invalid';
        }

        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $request = DB::select($sql_command);

        $data = '<div class="form-group ' . ($this->config->class ?? "") . ' col-sm-12 group_' . $class_prediction . '';
        $data .= $this->config->class ?? '';
        $data .= '">';
        $data .= '<label>' . $this->config->title . '</label><br />';
        $data .= '<select wire:change="update_value(\''.$wireModel.'\',  $event.target.value)" id="form_config_' . $this->name . '"';
        if ($this->config->required == 1) {
            $data .= ' required ';
        }
        $data .= 'name="' . $this->config->name . '" class="form-control ' . $class_prediction . '">';
        $data .= '<option value=""';
        // if ($this->value == '0'  || $this->value == '' || empty($this->value)) {
        //     $data .= ' selected ';
        // }
        $data.='>Choisir</option>';
   
        foreach ($request as $result) {
            $fieldValue = $optionsArray['value'];
            $fieldLabel = $optionsArray['label'];
            $data .= '<option ';

            if ($this->value == $result->$fieldValue) {
                $data .= ' selected ';
            }


            $data .= ' value="' . $result->$fieldValue . '">' . $result->$fieldLabel . '</option>';
        }
        $data .= '</select>';

        if (!$this->check_value()) {
            $data .= '<div  class="invalid-feedback">' . $this->get_error_message() . '</div>';

        }

        $data .= '</div>';




        return $data;
    }

    public function check_value()
    {
        if ($this->config->required && $this->value == '') {
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


    public function render_pdf()
    {
        if(!is_array($this->config->options)) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->config->options;
        }


        $sql_command = $optionsArray['sql'];
      
        if (isset($optionsArray['arguments'])) {
            foreach ($optionsArray['arguments'] as $key => $data) {
                $sql_command = str_replace($key, eval ($data), $sql_command);
            }
        }
        $class_prediction = '';
        if (!$this->check_value()) {
            $class_prediction = ' is-invalid';
        }

        $request = DB::select($sql_command);


        if(!$this->value || $this->value=='') {
            return false;
        }

        $data = '<div  class="form-group col-sm-12 ' . ($this->config->class ?? "") . '">';


        $data .= '<div class="s3" style="display:block;margin-top:15px;margin-bottom:8px">' . $this->config->title . '</div>';

        foreach ($request as $result) {
            $fieldValue = $optionsArray['value'];
            $fieldLabel = $optionsArray['label'];
      

            if ($this->value == $result->$fieldValue) {
                $found_value = $result->$fieldValue;
            }


        }


        $data .= '<div style="display:block;margin-bottom:8px">'.$found_value.'</div>';

   

  
        $data .= '</div>';


        return $data;


        

      




        return $data;
    }


}