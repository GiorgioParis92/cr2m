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
        // return $jsonString;
        $optionsArray = json_decode($jsonString, true);

        $sql_command = $optionsArray['sql'];

        if (isset($optionsArray['arguments'])) {
            foreach ($optionsArray['arguments'] as $key => $data) {
                $sql_command = str_replace($key, eval ($data), $sql_command);
            }
        }

        $condition_valid = false;

        if(isset($optionsArray['conditions'])) {
          
            foreach ($optionsArray as $key=>$condition_config) {
                if($key=='conditions') {
                    if ($this->check_condition($condition_config)) {
                
                        $condition_valid = true;
        
                        if (isset($condition_config['operation']) && $condition_config['operation'] == 'AND') {
                            break;
                        }
        
                    } else {
                        $condition_valid = false; 
                    }
                }

    
            }
        } else {
            $condition_valid = true;
        }

 
  
        if( $condition_valid == false) {
            return '';
        }
        $class_prediction = '';

        if (!$this->check_value()) {
            $class_prediction = ' is-invalid';
        }

        $request = DB::select($sql_command);
        if ($this->value && $this->value != '' && $this->value != 0) {

            foreach ($request as $result) {

                $fieldValue = $optionsArray['value'];
                $fieldLabel = $optionsArray['label'];

                // SI preco ajouter par l'utilisateur
                if ($result->$fieldLabel >= $this->value) {
                    $this->value = $result->$fieldLabel;
                    $this->save_value();
                }
            }

        } else {
            foreach ($request as $result) {
                $fieldValue = $optionsArray['value'];
                $fieldLabel = $optionsArray['label'];
                $this->value = $result->$fieldLabel;
                $this->save_value();
            }
        }


        if (is_numeric($this->value)) {
            $this->value = number_format($this->value, 2, ',', ' ');
        }

        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $readonly = '';
        if (isset($optionsArray['readonly'])) {
            $readonly = (($optionsArray['readonly'] == true) ? '' : '');
        }

        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $class_prediction = '';

        if ($this->prediction) {
            $class_prediction = ' prediction';
        }
        if (!$this->check_value()) {
            $class_prediction = ' is-invalid';
        }

        $data = '<div wire:poll class="form-group col-sm-12 ' . ($this->config->class ?? "") . '  group_' . $class_prediction . '">';

        $data .= '<label style="display:inline-block">' . $this->config->title . '</label>';

        $data .= $this->generate_loading();

        $data .= '<input type="text" ' . $readonly . ' wire:change="update_value(\''.$wireModel.'\',  $event.target.value)"  class="form-control ' . $class_prediction . '" type="text" name="' . $this->name . '" ';

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
    public function match_value($tag, $list_values)
    {
        $value = $this->getOtherValue($tag);

        foreach ($list_values as $list_value) {
            if ($value == $list_value) {
                return true;
            }
        }
        return false;
    }

 

    public function render_pdf()
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

        $request = DB::select($sql_command);

        if ($this->value && $this->value != '') {

            foreach ($request as $result) {
                $fieldValue = $optionsArray['value'];
                $fieldLabel = $optionsArray['label'];

                if ($this->value > $result->$fieldLabel) {
                    $this->value = $result->$fieldLabel;
                    $this->save_value();
                }

            }

        } else {
            foreach ($request as $result) {
                $fieldValue = $optionsArray['value'];
                $fieldLabel = $optionsArray['label'];
                $this->value = $result->$fieldLabel;
                $this->save_value();
            }
        }


        if (is_numeric($this->value)) {
            $this->value = number_format($this->value, 2, ',', ' ');
        }

        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $readonly = '';
        if (isset($optionsArray['readonly'])) {
            $readonly = (($optionsArray['readonly'] == true) ? 'disabled' : '');
        }

        $wireModel = "formData.{$this->form_id}.{$this->name}";

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

        $data .= '<input ' . $readonly . ' wire:blur="update_value(\''.$wireModel.'\',  $event.target.value)"  class="form-control ' . $class_prediction . '" type="text" name="' . $this->name . '" ';

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

    }

    public function check_condition($condition_config)
    {
        foreach ($condition_config as $tag => $list_values) {
            if (!$this->match_value($tag, $list_values)) {

                return false;
            }
        }
    
        return true;
    }
}