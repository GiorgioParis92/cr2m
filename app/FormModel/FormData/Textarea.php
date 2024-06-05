<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Textarea extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $data = '<div class="form-group '.($this->config->class ?? "").'">';
        $data .= '<label>'.$this->config->title.'</label>';
        $data .= '<textarea class="form-control" name="'.$this->config->name.'">'.($this->value ?? "") .'</textarea>';

        $data .= '</div>';


        return $data;
    }

}