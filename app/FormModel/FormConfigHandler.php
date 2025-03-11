<?php

namespace App\FormModel;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use App\FormModel\FormData\AbstractFormData;
use Illuminate\Http\Request;
use App\Models\FormConfig;

class FormConfigHandler
{
    public $form;
    public $dossier;
    public $formData = [];
    public static $form_instance = [];
    public  $form_render ;
    public  $etape_number ;

    public function __construct($dossier, $form)
    {
        $this->dossier = $dossier;
        $this->form = $form;

        $this->init();

    }

    public function init()
    {
        // Retrieve form data
        $formData = FormConfig::where('form_id', $this->form->id)
        ->orderBy('ordering')
        ->get();

        foreach ($formData as $value) {
            $className = $this->getFormDataClassName($value->type);

            // Instantiate the class without checking class_exists every time
            $this->formData[$value->name] = new $className(
                $value,
                $value->name,
                $this->form->id,
                $this->dossier->id
            );

            $this->formData[$value->name]->set_dossier($this->dossier);
        }
    }

    private function getFormDataClassName($type)
    {
        if (isset(self::$form_instance[$type])) {
            return self::$form_instance[$type];
        }

        $baseNamespace = 'App\FormModel\FormData\\';
        $className = $baseNamespace . ucfirst($type);

        if (class_exists($className)) {
            self::$form_instance[$type] = $className;
        } else {
            // Fallback to AbstractFormData if the class does not exist
            self::$form_instance[$type] = AbstractFormData::class;
        }

        return self::$form_instance[$type];
    }

    public function render($errors)
    {
        if ($this->form->type == 'document') {
            return $this->render_document($errors);
        } else {
            return $this->render_form($errors);
        }
    }

    public function render_form($errors)
    {
        $data = '<div class="row">';

        foreach ($this->formData as $tag => $data_form) {
            $is_error = false;
            if (isset($errors[$tag]) && $errors[$tag] == false) {
                $is_error = true;
            }
            // Render form data and handle errors
            $data .= $data_form->render($is_error);
        }

        $data .= '</div>';

        return $data;
    }


    public function get_form_progression_percent()
    {
        $total = 0;
        $valid = 0;

        foreach ($this->formData as $tag => $data_form) {
            $total ++;
            if ($data_form->check_value() ) {
                $valid++;
            }
        }

        if ($total == 0)
            return 0;

        return $valid / $total;
        
    }


    public function render_document($errors)
    {
        $data = '';
        foreach ($this->formData as $tag => $data_form) {
            $is_error = false;
            if (isset($errors[$tag]) && $errors[$tag] == false) {
                $is_error = true;
            }
            $data .= $data_form->render($is_error);
        }

        return $data;
    }

    public function save()
    {
        $saved = [];
       
        foreach ($this->formData as $tag => $data_form) {
            if ($data_form !== null) {
                $saved[$tag] = $data_form->save_value();
            } else {
                $saved[$tag] = null;
            }
        }
        return $saved;
    }
}
