<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;


class Prompt extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $jsonString = str_replace(["\n", "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);


        $data = '<div style="margin-bottom:20px" class="form-group  col-sm-12 '.($this->config->class ?? "").'">';
        $data .= '<label>'.$this->config->title.'</label>';
        $data .= '<textarea  style="min-height:300px" class="form-control" name="'.$this->config->name.'">'.nl2br($optionsArray['prompt']).'</textarea>';

        $data .= '</div>';


        return $data;
    }

    public function save_value()
    {

        return true;
    }

}