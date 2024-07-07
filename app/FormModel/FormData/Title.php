<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Title extends AbstractFormData
{
    public function render(bool $is_error)
    {


        $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);

        $optionsArray = json_decode($jsonString, true);

        $condition_valid = false;

        if(isset($optionsArray['conditions'])) {
          
            foreach ($optionsArray as $key=>$condition_config) {
                if($key=='conditions') {
                    if ($this->check_condition($condition_config)) {
                
                        $condition_valid = true;
        
                        if (isset($condition_config['operation']) && $condition_config['operation'] == 'AND') {
                            break;
                        }
        
                    } else {
                        $condition_valid = false; 
                    }
                }

    
            }
        } else {
            $condition_valid = true;
        }


  
        if( $condition_valid == false) {
            return '';
        }
       
        $data= '<div class="row"><div class="col-12 form_title"><h6>'.$this->config->title.'</h6></div></div>';



        return $data;
    }
    public function match_value($tag, $list_values)
    {
        $value = $this->getOtherValue($tag);
       
        foreach ($list_values as $list_value) {
            if ($value == $list_value) {
                return true;
            }
        }
        return false;
    }

    public function check_condition($condition_config)
    {
        foreach ($condition_config as $tag => $list_values) {
            if (!$this->match_value($tag, $list_values)) {

                return false;
            }
        }
    
        return true;
    }
}