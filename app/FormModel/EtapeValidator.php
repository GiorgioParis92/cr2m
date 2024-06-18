<?php

namespace App\FormModel;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use App\FormModel\FormData\AbstractFormData;
use Illuminate\Http\Request;



class EtapeValidator
{
    public $per_etape_config = [];

    public function __construct($etape_id)
    {

        $config = DB::table("etape_validation_config")
            ->where('etape_id', $etape_id)
            ->orderBy('ordering', 'asc')
            ->get();
        foreach ($config as $key => $value) {
            $this->per_etape_config[$value->status_id] = json_decode($value->config);
        }
    }

    private function is_config_valid($config, $data)
    {
        foreach ($config as $form_id => $validation_config) {
            // FOR AND 

            foreach ($validation_config->and as $tag => $value) {
                if (array_key_exists(intval($form_id), $data)) {
                    if (array_key_exists($tag, $data[$form_id]->formData)) {
                        if (isset($value)) {
                            if ($value != $data[$form_id]->formData[$tag]->generate_value()) {
                              

                                return false;
                            }
                        } else {
                            if (!$data[$form_id]->formData[$tag]->check_value()) {
                               

                                return false;
                            }
                        }
                    } else {
                  
                        return False;
                    }
                } else {
               

                    return False;
                }
            }


            // FOR OR


        }

        return True;
    }

    public function get_last_validate_status($data)
    {
        $valid_status = null;
        foreach ($this->per_etape_config as $status_to_go_id => $config) {
            if ($this->is_config_valid($config, $data)) {
                $valid_status = $status_to_go_id;
            }
        }

        return $valid_status;
    }

}
