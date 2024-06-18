<?php

namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class AbstractFormData
{
    public $value;
    protected $name;
    protected $form_id;
    protected $dossier_id;
    protected $config;
    public $prediction;

    public function __construct($config, $name, $form_id, $dossier_id)
    {
        $this->form_id = $form_id;
        $this->dossier_id = $dossier_id;
        $this->config = $config;

        $config = \DB::table('forms_data')
            ->where('form_id', $form_id)
            ->where('dossier_id', $dossier_id)
            ->where('meta_key', $name)
            ->first();


        $config_dossiers = \DB::table('dossiers_data')
            ->where('dossier_id', $dossier_id)
            ->where('meta_key', $name)
            ->first();

        // Initialize value with the first table's value or an empty string
        $this->name = $name;

        if(is_array(@unserialize($config->meta_value))) {

            foreach(@unserialize($config->meta_value) as $key => $value) {
            $this->value = $value;
            }

        } else {
            $this->value = $config->meta_value ?? '';
        }
       
      

        
        $this->prediction = $config_dossiers->meta_value ?? '';


        $this->global_data = '';


        if($this->value=='') {
            $this->value=$this->prediction;

        }

        // if (!$this->check_value()) {
        //     $this->value = '';
        // }

    }

    public function check_value()
    {
  
        return true;

    }

    public function generate_value()
    {
        return $this->value;
    }
    public function save_value()
    {
        $value = $this->generate_value();



        DB::table('forms_data')->updateOrInsert(
            [
                'dossier_id' => $this->dossier_id,
                'form_id' => $this->form_id,
                'meta_key' => $this->name
            ],
            [
                'meta_value' => $value ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return $this->check_value();
    }


    public function render(bool $is_error)
    {

        return '<div>' . $this->value . '</div>';

    }


    public function get_error_message()
    {

        return 'Mauvaise valeur';

    }
}
