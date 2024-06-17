<?php

namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Text extends AbstractFormData
{
    public function render(bool $is_error)
    {
        // Constructing the wire:model directive
        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $class_prediction='';
        if($this->prediction) {
            $class_prediction.='prediction';
        }

        // dump($this->prediction);

        $data = '<div class="form-group col-sm-12 '.($this->config->class ?? "").'  group_'.$class_prediction.'">';
        $data .= '<label>'.$this->config->title.'</label>';
        $data .= '<input wire:model="'.$wireModel.'" class="form-control '.$class_prediction.'" type="text" name="'.$this->name.'"';

        if ($this->config->required) {
            $data .= ' required ';
        }
        $data .= ' value="'.($this->value).'">';
        if($this->prediction) {
            $data.='<span class="prediction">Predicted by OCEER</span>';
        }
        $data .= '</div>';




        return $data;
    }
}
