<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Textarea extends AbstractFormData
{
    public function render(bool $is_error)
    {
        $wireModel = "formData.{$this->form_id}.{$this->name}";

        $data = '<div class="form-group  col-sm-12 '.($this->config->class ?? "").'">';
        $data .= '<label>'.($this->config->title ?? '').'</label>';
        $data .= '<textarea  wire:blur="update_value(\''.$wireModel.'\',  $event.target.value)" class="form-control" name="'.($this->config->name ?? '').'">'.($this->value ?? "") .'</textarea>';

        $data .= '</div>';


        return $data;
    }

    public function render_form(bool $is_error)
    {
       

        $data = '<div class="form-group  col-sm-12 '.($this->config->class ?? "").'">';
        $data .= '<label>'.($this->config->title ?? '').'</label>';
        $data .= '<div  class="form-control" name="'.($this->config->name ?? '').'">'.($this->value ?? "") .'</div>';

        $data .= '</div>';


        return $data;
    }

<<<<<<< HEAD
<<<<<<< HEAD
}
=======
        dd($this->value);
=======
>>>>>>> 8d22cca6f2308341b08658a03b4ae5949098190a

        if(!$this->value || $this->value=='') {
            return false;
        }

        $data = '<div  class="form-group col-sm-12 ' . ($this->config->class ?? "") . '">';


        $data .= '<div class="s3" style="display:block;margin-top:15px;margin-bottom:8px">' . $this->config->title . '</div>';




        $data .= '<div style="display:block;margin-bottom:8px">'.$this->value.'</div>';




        $data .= '</div>';


        return $data;
    }
}
>>>>>>> 40934717e9c90edf9442ccb8f55bc13a8b053679
