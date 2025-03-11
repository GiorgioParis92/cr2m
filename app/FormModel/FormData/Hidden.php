<?php

namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Hidden extends AbstractFormData
{
    public function render(bool $is_error)
    {
        // Constructing the wire:model directive

    
        $data = '<input id="'.$this->name.'" value="'.$this->config->options.'" class="form-control" type="hidden" name="'.$this->name.'"';

   

        return $data;
    }
}
