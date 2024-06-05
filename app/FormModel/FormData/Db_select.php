<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\View;

class Db_select extends AbstractFormData
{
    public function render(bool $is_error)
    {
        $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);

        $sql_command = $optionsArray['sql'];

        if (isset($optionsArray['arguments'])) {
            foreach ($optionsArray['arguments'] as $key => $data) {
                $sql_command = str_replace($key, eval($data), $sql_command);
            }
        }

     
        $request=DB::select($sql_command);

        $data = '<div class="form-group ';
        $data .= $this->config->class ?? '';
        $data .= '">';
        $data .='<label>'.$this->config->title.'</label><br />';
        $data .='<select id="form_config_'.$this->name.'"';
        if($this->config->required==1) {$data.=' required '; } 
        $data .='name="'.$this->config->name.'" class="form-control">';
        $data.='<option value="">Choisir</option>';
        
        foreach($request as $result) {
            $fieldValue = $optionsArray['value'];
            $fieldLabel = $optionsArray['label'];
            $data.='<option ';
            
            if($this->value==$result->$fieldValue) {
                $data.=' selected ';
            }
  

            $data.=' value="'.$result->$fieldValue.'">'.$result->$fieldLabel.'</option>'; 
        }
        $data .= '</select>';
        $data .= '</div>';
  

        
        return $data;
    }

}