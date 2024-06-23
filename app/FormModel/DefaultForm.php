<?php

namespace App\FormModel;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class DefaultForm
{
    protected $value;
    protected $tag;
    protected $configurations;
    protected $formData;

    public function __construct()
    {
        $this->configurations = collect(); // Ensure configurations is never null
        $this->formData = collect(); // Ensure formData is never null
    }

    public function render()
    {

        return '<div>'.$this->value.'</div>';
        
    }

    public function getFormById($id)
    {
        $form = \DB::table('forms')->where('id', $id)->first();
        if ($form) {
            $this->value = $form->form_title;
            $this->configurations = \DB::table('forms_config')->where('form_id', $id)->orderBy('ordering')->get();
        } else {
            $this->configurations = collect(); // Ensure configurations is never null
        }
        return $form;
    }

    public function getConfigurations()
    {
        return $this->configurations;
    }

    public function getFormData($formId, $dossierId)
    {

        // Retrieve form data from the first table
        $this->formData = \DB::table('forms_data')
            ->where('dossier_id', $dossierId)
            ->where('form_id', $formId)
            ->pluck('meta_value', 'meta_key')
            ->toArray();

        $additionalData = \DB::table('dossiers_data')
            ->where('dossier_id', $dossierId)

            ->pluck('meta_value', 'meta_key')
            ->toArray();

        // Merge the results
        $this->formData = array_merge($this->formData, $additionalData);

        $dossier = Dossier::where('id', $dossierId)->first();
        $dossier_datas = DB::table('dossiers_data')->where('dossier_id', $dossierId)->get();

        $beneficiaire = DB::table('beneficiaires')->where('id', $dossier->beneficiaire_id)->first();
        
        if ($dossier) {
            foreach ($dossier as $key => $value) {
                $this->formData[$key] = $value;
            }
        }
        if ($dossier_datas) {
            foreach ($dossier_datas as $key => $value) {
        
                $this->formData[$value->meta_key] = $value->meta_value;
            }
        }

        if ($beneficiaire) {
            foreach ($beneficiaire as $key => $value) {
                $this->formData[$key] = $value;
            }
        }

        
        return $this->formData;
    }

    public function saveFormData($dossierId, $formId, $data)
    {
    
    
        if ($formId == 3) {

            // Assume the beneficiaire_id can be retrieved via the dossier_id
            $dossier = Dossier::where('id', $dossierId)->first();

            if ($dossier && isset($dossier->beneficiaire_id)) {
                $beneficiaireId = $dossier->beneficiaire_id;

                // Prepare the data to update the beneficiaires table
                $beneficiaireData = [];

                foreach ($data as $key => $value) {

                    // Update only if the key exists in the beneficiaires table columns
                    if (\Schema::hasColumn('beneficiaires', $key)) {
                        \DB::table('beneficiaires')->where('id', $beneficiaireId)->update([$key => $value, 'updated_at' => now()]);

                    }
                }


            }
        }

        foreach ($data as $key=>$value) {
            if ($key && $key!='_token' && $key!='form_id') {
                \DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => $dossierId,
                        'form_id' => $formId,
                        'meta_key' => $key
                    ],
                    [
                        'meta_value' => $value ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

        }
     
    }
}
