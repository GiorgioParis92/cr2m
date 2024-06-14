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
           

        $config_dossier = \DB::table('dossiers_data')
            ->where('dossier_id', $dossier_id)
            ->where('meta_key', $name)
            ->first();
    
        // Initialize value with the first table's value or an empty string
        $this->name = $name;
        $this->value = $config->meta_value ?? '';
        $this->prediction='';

        // If the value from the second table exists and is not null, use it
        if ($config_dossier && isset($config_dossier->meta_value)) {
            $this->prediction=$config_dossier->meta_value;

            if($this->value=='') {
                $this->value = $config_dossier->meta_value;
            }
           

        }


        if (!$this->check_value()) {
            $this->value = '';
        }
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
        if (!$this->check_value()) {
            return false;
        }

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

        return true;
    }


    public function render(bool $is_error)
    {

        return '<div>'.$this->value.'</div>';
        
    }


    public function get_error_message()
    {

        return 'Mauvaise valeur';
        
    }
}
