<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\Storage;

class Generate extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $jsonString = str_replace(["\n", ' ', "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);
        if (!is_array($optionsArray)) {
            $optionsArray = [];
        }

  
        $data = '';





        $data = '<tr>';
        $data .= '<td class="w-30">';

        $extension=explode('.',$this->value);


    
        $data .= '<button type="button" class="btn btn-primary btn-view generatePdfButton"
       
  
        data-dossier_id="'.$this->dossier_id.'"
        data-template="'.$optionsArray['template'].'">
        <i class="fas fa-file-pdf"></i>';
  




        $data .= '</td>';
        $data .= '<td>';
        $data .= '<div class="d-flex px-2 py-1 align-items-center">';
        $data .= $this->config->title;
  
        $data .= '</div>';
        $data .= '</td>';

        $data .= '<td class="align-middle text-sm">';
        $data .= '<form action="' . route("upload_file", ["form_id" => $this->form_id, "folder" => "dossiers", "clientId" => $this->dossier_id, "template" => $this->name]) . '" class="dropzone dropzone_button" id="dropzone-' . $this->name . '">';
        $data .= csrf_field(); // This will generate the CSRF token input field
        $data .= '<div class="dz-message"><i class="fas fa-arrow-up"></i>';
        $data .= '</div>';
        $data .= '</form>';

        $data .= '</td>';
        $data .= '</tr>';

        return $data;
    }


    public function check_value() {

        return Storage::disk('public')->exists($this->value);
    }

}