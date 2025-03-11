<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Audio extends AbstractFormData
{
    
    public function render(bool $is_error)
    {
     
        $data='';
     
        return $data;
    }


    
    public function check_value() {
     
 

        return true;
    }


    public function get_error_message()
    {
 
        return '';
    }

 
    public function render_pdf()
    {
  
        $data='';
  
        
        return $data;
    }

    public function check_condition($condition_config)
    {

    
        return true;
    }

}
