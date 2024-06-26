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

        $data .= '<td>';
        $data .= '<div class="d-flex px-2 py-1 align-items-center">';
        $data .= $this->config->title;

        $data .= '</div>';
        $data .= '</td>';

        $data .= '<td class="w-30">';

        $extension = explode('.', $this->value);



        $data .= '<button type="button" class="btn btn-secondary btn-view generatePdfButton"
       
  
        data-dossier_id="' . $this->dossier->folder . '"
        data-template="' . $optionsArray['template'] . '">
        <i class="fas fa-file-pdf"></i> Générer';





        $data .= '</td>';


        $data .= '<td class="w-30" ><div >';


        if($this->value) {
        $extension = explode('.', $this->value);

      

            $filePath = storage_path('app/public/dossiers/'.$this->dossier->folder .'/' . $optionsArray['template'].'.pdf');  // File system path
    
            // $data.=(asset('storage/' . $this->value));
            if (file_exists($filePath)) {
                if (end($extension) != 'pdf') {
                    $data .= '<button type="button" class="btn btn-success btn-view imageModal"
            data-toggle="modal" data-target="imageModal"
            data-img-src="' . asset('storage/' . $this->value) . '?time=' . strtotime('now') . '"
            data-name="' . $this->config->title . '">
            <i class="fas fa-eye"></i> Visualiser
        </button> ';
                } else {
                    $data .= '<div class="btn btn-success btn-view pdfModal"
            data-toggle="modal" 
             
            data-img-src="' . asset('storage/' . $this->value) . '?time=' . strtotime('now') . '"
            data-name="' . $this->config->title . '">
            <i class="fas fa-eye"></i> Visualiser</div>';
                }
            }

        




        $data .= '</div>';
        }
        $data.='</td>';


        $data .= '<td class="align-middle text-sm">';
        $data .= '<form action="' . route("upload_file", ["form_id" => $this->form_id, "folder" => "dossiers", "clientId" => $this->dossier->folder, "template" => $this->name]) . '" class="dropzone dropzone_button bg-primary" id="dropzone-' . $this->name . '">';
        $data .= csrf_field(); // This will generate the CSRF token input field
        $data .= '<div class="dz-message"><i class="fas fa-arrow-up"></i> Upload';
        $data .= '</div>';
        $data .= '</form>';

        $data .= '</td>';
        $data .= '</tr>';

        return $data;
    }


    public function check_value()
    {

        return true;
    }

}