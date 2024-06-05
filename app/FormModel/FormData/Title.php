<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Title extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $data= '<h6>'.$this->config->title.'</h6>';



        return $data;
    }

}