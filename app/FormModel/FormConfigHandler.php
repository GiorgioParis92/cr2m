<?php

namespace App\FormModel;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use App\FormModel\FormData\AbstractFormData;
use Illuminate\Http\Request;


class FormConfigHandler
{
    public $form;
    public $dossier;
    public $formData = [];

    public function __construct( $dossier,$form)
    {
        $this->dossier = $dossier;
        $this->form = $form;
        $this->init();

    }

    public function init()
    {

        // Retrieve form data from the first table
        $formData = \DB::table('forms_config')
            ->where('form_id', $this->form->id)
            ->orderBy('ordering')
            ->get();

            foreach ($formData as $value) {
                // Define the base namespace
                $baseNamespace = 'App\FormModel\FormData\\';
            
                // Generate the full class name including the namespace
                $className = $baseNamespace . ucfirst($value->type);
                
        
                // Check if the class exists
                if (class_exists($className)) {
                    $reflectionClass = new \ReflectionClass($className);
                    $this->formData[$value->name] = $reflectionClass->newInstance($value, $value->name, $this->form->id, $this->dossier->id);
                } else {
                    // Fallback to AbstractFormData if the class does not exist
                    $this->formData[$value->name] = new AbstractFormData($value, $value->name, $this->form->id, $this->dossier->id);
                }
            }
    }

    public function save()
    {
        $saved = [];
        foreach ($this->formData as $tag => $data_form) {
            if ($data_form !== null) {
                $saved[$tag] = $data_form->save_value();
            } else {
                // Debug: Log the problematic tag
                $saved[$tag] = null;
            }
        }
        return $saved;
    }
    public function render($errors)
    {

        if($this->form->type=='document') {
            return $this->render_document($errors);
        } else {
            return $this->render_form($errors);
        }
      
    }


    public function render_document($errors)
    {
        $data='';
        foreach ($this->formData as $tag => $data_form) {
            $is_error = false;
            if (isset($errors[$tag]) && $errors[$tag] == false) {
                $is_error = true;
            }
            $data .= $data_form->render($is_error);
    
        }
      

        return $data;
    }


    public function render_form($errors)
    {

        $data = '<div class="row">';
        

        foreach ($this->formData as $tag => $data_form) {
            $is_error = false;
            if (isset($errors[$tag]) && $errors[$tag] == false) {
                $is_error = true;
            }
            $data .= $data_form->render($is_error);
    
        }

        $data .= '</div>';

        return $data;
    }

}
