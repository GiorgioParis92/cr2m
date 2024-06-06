<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Text extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $data = '<div class="form-group  col-sm-12 '.($this->config->class ?? "").'">';
        $data .= '<label>'.$this->config->title.'</label>';
        $data .= '<input class="form-control" type="text" name="'.$this->config->name.'"';
        if ($this->config->required) {
            $data .= ' required ';
         } 
        $data .= 'value="'.($this->value).'">';
        $data .= '</div>';


        return $data;
    }

}