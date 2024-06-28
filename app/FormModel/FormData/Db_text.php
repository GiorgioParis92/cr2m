<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\View;

class Db_text extends AbstractFormData
{
    public function render(bool $is_error)
    {
        $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);

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

      $readonly='';
      if(isset($optionsArray['readonly'])) {
        $readonly=(($optionsArray['readonly']==true) ? 'disabled' : '');
      }


        $wireModel = "formData.{$this->form_id}.{$this->name}";


        foreach ($request as $result) {
            $fieldValue = $optionsArray['value'];
           $fieldLabel = $optionsArray['label'];
           $this->value= $result->$fieldLabel;
            $this->save_value();
        }

        $class_prediction = '';
        if ($this->prediction) {
            $class_prediction = ' prediction';
        }
        if (!$this->check_value()) {
            $class_prediction = ' is-invalid';
        }

        $data = '<div class="form-group col-sm-12 ' . ($this->config->class ?? "") . '  group_' . $class_prediction . '">';


        $data .= '<label style="display:inline-block">' . $this->config->title . '</label>';


        $data .= $this->generate_loading();


        $data .= '<input '.$readonly.' wire:model.lazy="' . $wireModel . '" class="form-control ' . $class_prediction . '" type="text" name="' . $this->name . '" value="'.$result->$fieldLabel.'"';

        if ($this->config->required) {
            $data .= ' required ';
        }
        $data .= ' value="' . ($this->value) . '">';

        if (!$this->check_value()) {
            $data .= '<div  class="invalid-feedback">' . $this->get_error_message() . '</div>';

        } else {
            if ($this->prediction) {
                $data .= '<span class="prediction">Predicted by OCEER</span>';
            }
        }
        $data .= '</div>';


        return $data;

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

}