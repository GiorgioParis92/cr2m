<?php

namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use App\Models\FormsData;
use Illuminate\Support\Facades\Schema;

class AbstractFormData
{
    public $value;
    protected $name;
    protected $form_id;
    protected $dossier_id;
    protected $dossier;
    protected $config;
    public $prediction;
    public $updating = false;
    public $condition_valid = false;

    public function __construct($config, $name, $form_id, $dossier_id, $should_load = true)
    {
        $this->form_id = $form_id;
        $this->dossier_id = $dossier_id;
        // $this->config = $config;
        $this->config = (object) $config;

        // $this->title=$config->title;



        if ($should_load) {
            $config = \DB::table('forms_data')
                ->where('form_id', $form_id)
                ->where('dossier_id', $dossier_id)
                ->where('meta_key', $name)
                ->first();

            $config_dossiers = \DB::table('dossiers_data')
                ->where('dossier_id', $dossier_id)
                ->where('meta_key', $name)
                ->first();
            $this->value = $config->meta_value ?? '';

            $this->prediction = $config_dossiers->meta_value ?? '';
        }

        $this->name = $name;

  

        $this->global_data = '';

        if ($this->value == '') {
            $this->value = $this->prediction;
        }

        if (!is_array($this->config->options)) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->config->options;

        }
        $optionsArray = convertToArray($optionsArray);
        $this->optionsArray = $optionsArray;
    
        // $this->condition_valid = false;

        // if (isset($optionsArray['conditions'])) {
        //     if ($this->check_condition($optionsArray['conditions'])) {
        //         $this->condition_valid = true;
        //     }  
          
        // } else {
        //     $this->condition_valid = true;
        // }

   
    }

    public function check_value()
    {

        return true;

    }


    public function generate_loading()
    {
        if ($this->updating) {
            return '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>';
        }
        return '';
    }
    public function generate_value()
    {
        return $this->value;
    }
    public function save_value()
    {
        $value = $this->generate_value();



        // DB::table('forms_data')->updateOrInsert(
        //     [
        //         'dossier_id' => $this->dossier_id,
        //         'form_id' => $this->form_id,
        //         'meta_key' => $this->name
        //     ],
        //     [
        //         'meta_value' => $value ?? null,
        //         'created_at' => now(),
        //         'updated_at' => now()
        //     ]
        // );
        $update = FormsData::updateOrCreate(
            [
                'dossier_id' => $this->dossier_id,
                'form_id' => $this->form_id,
                'meta_key' => $this->name
            ],
            [
                'meta_value' => $value ?? null,
            ]
        );

        // $maxRetries = 5; // Number of retries
        // $attempts = 0;
    
        // while ($attempts < $maxRetries) {
        //     try {    
        //         DB::table('forms_data')->updateOrInsert(
        //             [
        //                 'dossier_id' => $this->dossier_id,
        //                 'form_id' => $this->form_id,
        //                 'meta_key' => $this->name
        //             ],
        //             [
        //                 'meta_value' => $value ?? null,
        //                 'created_at' => now(),
        //                 'updated_at' => now()
        //             ]
        //         );
    
        //         // If no exception occurs, break out of the loop
        //         return;
        //     } catch (\Illuminate\Database\QueryException $e) {
        //         $attempts++;
    
        //         // Check if the error is a deadlock issue
        //         if ($e->getCode() == '40001' || str_contains($e->getMessage(), '1213 Deadlock')) {
        //             if ($attempts < $maxRetries) {
        //                 // Wait for a short duration before retrying
        //                 usleep(100000); // 100 milliseconds
        //             } else {
        //                 // Re-throw the exception if max retries reached
        //                 throw $e;
        //             }
        //         } else {
        //             // Re-throw other exceptions
        //             throw $e;
        //         }
        //     }
        // }



        if($this->check_value()) {
        if ($this->form_id == 3 || $this->form_id == 10) {
            $beneficiaire = DB::table('dossiers')->where('id', $this->dossier_id)->first();

            if ($beneficiaire) {
                $columnExists = DB::getSchemaBuilder()->hasColumn('beneficiaires', $this->name);

                if ($columnExists) {
                    $columnType = $this->getColumnType('beneficiaires', $this->name);

                    if ($columnType == 'int' && $value === '') {
                        $value = 0;
                    }

                    DB::table('beneficiaires')->where('id', $beneficiaire->beneficiaire_id)->update([
                        $this->name => $value,
                        'updated_at' => now()
                    ]);
                }

                $columnExists = DB::getSchemaBuilder()->hasColumn('dossiers', $this->name);

                if ($columnExists) {
                    $columnType = $this->getColumnType('dossiers', $this->name);

                    if ($columnType == 'int' && $value === '') {
                        $value = 0;
                    }

                    DB::table('dossiers')->where('id', $this->dossier_id)->update([
                        $this->name => $value,
                        'updated_at' => now()
                    ]);
                }

            }
        }
    }


        return $this->check_value();
    }


    public function render(bool $is_error)
    {
        if(!$this->condition_valid) {
            return false;
        }
        return '<div></div>';

    }


    public function get_error_message()
    {

        return 'Mauvaise valeur';

    }



    public function set_dossier($dossier)
    {
        $this->dossier = $dossier;
    }


    public function getOtherValue($name)
    {
        $otherValue = DB::table('forms_data')
            ->where('dossier_id', $this->dossier_id)
            ->where('meta_key', $name)
            ->first();
       
        return $otherValue->meta_value ?? '';
    }


    protected function getColumnType($table, $column)
    {
        $database = env('DB_DATABASE');
        $columnInfo = DB::select("
        SELECT DATA_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = ? 
        AND TABLE_NAME = ? 
        AND COLUMN_NAME = ?
    ", [$database, $table, $column]);

        return $columnInfo[0]->DATA_TYPE ?? null;
    }

    public function render_pdf()
    {
        return false;
    }



    // public function check_condition($condition_config)
    // {
      
    //     // foreach ($condition_config as $tag => $list_values) {
    //     //     if (!$this->match_value($tag, $list_values)) {

    //     //         return false;
    //     //     }
    //     // }

    //     return true;
    // }
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

    public function getConfig()
    {
        // Assuming $this->config is an object or array
        return is_object($this->config) ? (array) $this->config : $this->config;
    }
}
