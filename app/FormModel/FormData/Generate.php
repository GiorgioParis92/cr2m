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


        if (isset($optionsArray['on_generation'])) {
            $generation = $optionsArray['on_generation'];
        } else {
            $generation = null;
        }

        if (isset($optionsArray['signable']) && $optionsArray['signable'] == 'true' ) {
            $check_signature=DB::table('forms_data')->where('form_id',$this->form_id)->where('dossier_id',$this->dossier_id)->where('meta_key','signature_request_id')->first();
            $check_status=DB::table('forms_data')->where('form_id',$this->form_id)->where('dossier_id',$this->dossier_id)->where('meta_key','signature_status')->first();
            $check_document=DB::table('forms_data')->where('form_id',$this->form_id)->where('dossier_id',$this->dossier_id)->where('meta_key','document_id')->first();
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


        if((isset($check_status) && $check_status->meta_value!='finish') || !isset($check_status)) {
        $data .= '<button type="button" class="btn btn-secondary btn-view generatePdfButton"
       
  
        data-dossier_id="' . $this->dossier->folder . '"';
        $data .= "data-generation='" . $generation . "'";
        $data .= "data-form_id='" . $this->form_id . "'";
        $data .= "data-name='" . $this->name . "'";
        $data .= 'data-template="' . $optionsArray['template'] . '">
        <i class="fas fa-file-pdf"></i> Générer';
        }




        $data .= '</td>';


        $data .= '<td class="w-30" ><div >';


        if ($this->value) {
            $extension = explode('.', $this->value);


            $filePath = storage_path('app/public/dossiers/' . $this->dossier->folder . '/' . $this->name . '.pdf');  // File system path

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

            if (isset($optionsArray['signable']) && $optionsArray['signable'] == 'true') {


                if((isset($check_status) && $check_status->meta_value!='finish') || !isset($check_status)) {
                if(!$check_signature)
                {
                    $data .= '<button type="button" class="btn btn-warning btn-view signable"
                    data-toggle="modal" 
                       data-dossier_id="' . $this->dossier->folder . '"';
                    $data .= "data-generation='" . $generation . "'";
                    $data .= "data-form_id='" . $this->form_id . "'";
                    $data .= "data-fields='" . json_encode($optionsArray['fields']) . "'";
                    $data .= "data-name='" . $this->name . "'";
                    $data .= 'data-template="' . $optionsArray['template'] . '"
                    data-name="' . $this->config->title . '">
                    <i class="fas fa-eye"></i> Signer le document
                </button> ';
                } else {
                    $data .= '<button type="button" class="btn btn-warning btn-view check_signature"
                    data-toggle="modal" 
                       data-dossier_id="' . $this->dossier->folder . '"';
                    $data .= "data-generation='" . $generation . "'";
                    $data .= "data-form_id='" . $this->form_id . "'";
                    $data .= "data-signature_request_id='" . $check_signature->meta_value . "'";
                    $data .= "data-document_id='" . $check_document->meta_value . "'";

                    $data .= 'data-template="' . $optionsArray['template'] . '"
                    data-name="' . $this->config->title . '">
                    <i class="fas fa-eye"></i> Télécharger le document signé
                </button> '; 
                $data.='<div id="message_' . $optionsArray['template'] . '">';
                
                    if($check_status) {
                        if($check_status->meta_value=='ongoing') {
                            $data.='Le document est en cours de signature';
                        }

                        if($check_status->meta_value=='done') {
                            $data.='Le document a été signé';
                        }
                        if($check_status->meta_value=='finish') {
                            $data.='Le document a été signé';
                        }
                    }
                }
                $data.='</div>';
                } else {
                    $data.='<div id="message_' . $optionsArray['template'] . '">';
                    $data.='Document signé';
                    $data.='</div>';
                }
             
    
            
      
            }


        }
        $data .= '</td>';


        $data .= '<td class="align-middle text-sm">';
        if((isset($check_status) && $check_status->meta_value!='finish') || !isset($check_status)) {
        $data .= '<form action="' . route("upload_file", ["form_id" => $this->form_id, "folder" => "dossiers","upload_image" => "dossiers",  "clientId" => $this->dossier->folder, "template" => $this->name]) . '" class="dropzone dropzone_button bg-primary" id="dropzone-' . $this->name . '">';
        $data .= csrf_field(); // This will generate the CSRF token input field
        $data .= '<div class="dz-message"><i class="fas fa-arrow-up"></i> Upload';
        $data .= '</div>';
        $data .= '</form>';
        }
        $data .= '</td>';
        $data .= '</tr>';

        return $data;
    }


    public function check_value()
    {

        return true;
    }

}