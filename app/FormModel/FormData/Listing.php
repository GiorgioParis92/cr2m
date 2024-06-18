<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;


class Listing extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $jsonString = str_replace(["\n", "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);

        if($optionsArray['elements']) {
            $elements=$optionsArray['elements'];
        }

     
        $data = '<div class="form-group  col-sm-12 '.($this->config->class ?? "").'">';

        // $data .= '<textarea  class="form-control" name="'.$this->config->name.'">'.$optionsArray['prompt'].'</textarea>';
$data .= '12345';
        $data .= '</div>';


        return $data;
    }

    public function save_value()
    {

        return true;
    }

}