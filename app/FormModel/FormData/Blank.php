<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Blank extends AbstractFormData
{
    public function render(bool $is_error)
    {
 
        $data = '<div class="form-group   '.($this->config->class ?? "").'">';
 

        $data .= '</div>';


        return $data;
    }




}