<?php

namespace App\FormModel\FormData;

class Text extends AbstractFormData
{
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


        $data .= '<label style="display:inline-block">' . $this->config->title . '</label>';


        $data .= $this->generate_loading();


        $data .= '<input wire:model.lazy="' . $wireModel . '" class="form-control ' . $class_prediction . '" type="text" name="' . $this->name . '"';

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

}
