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

        $data = '<div class="form-group col-sm-12 '.($this->config->class ?? "").'">';
        $data .= '<label>'.$this->config->title.'</label>';
        $data .= '<input wire:model="'.$wireModel.'" class="form-control" type="text" name="'.$this->name.'"';

        if ($this->config->required) {
            $data .= ' required ';
        }
        $data .= ' value="'.($this->value).'">';
        $data .= '</div>';

        return $data;
    }
}
