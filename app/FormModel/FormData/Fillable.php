<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\Storage;

class Fillable extends AbstractFormData
{

    public $optionsArray;

    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id);
        $jsonString = str_replace(["\n", ' ', "\r"], '', $this->config->options);
        $this->optionsArray = json_decode($jsonString, true);
        if (!is_array($this->optionsArray)) {
            $this->optionsArray = [];
        }
        if (isset($this->optionsArray['link'])) {
            $this->form_id = $this->optionsArray['link']['form_id'];
           
            $form_config=DB::table('forms_config')->where('form_id',$this->form_id)
            ->where('id',$this->optionsArray['link']['id'])
            ->first();

            $this->form_id=$form_config->form_id;

            $this->config=$form_config;

            $config = \DB::table('forms_data')
            ->where('form_id', $this->form_id)
            ->where('dossier_id', $dossier_id)
            ->where('meta_key', $name)
            ->first();

      
        $this->value = $config->meta_value ?? '';

            $jsonString = str_replace(["\n", ' ', "\r"], '', $form_config->options);
            $this->optionsArray = json_decode($jsonString, true);
           
        }

    }


    public function render(bool $is_error)
    {
  

        if (isset($this->optionsArray['on_generation'])) {
            $generation = $this->optionsArray['on_generation'];
        } else {
            $generation = null;
        }

        if (isset($this->optionsArray['signable']) && $this->optionsArray['signable'] == 'true' ) {
            $check_signature = DB::table('forms_data')->where('form_id', $this->form_id)->where('dossier_id', $this->dossier_id)->where('meta_key', 'signature_request_id')->first();
            $check_status = DB::table('forms_data')->where('form_id', $this->form_id)->where('dossier_id', $this->dossier_id)->where('meta_key', 'signature_status')->first();
            $check_document = DB::table('forms_data')->where('form_id', $this->form_id)->where('dossier_id', $this->dossier_id)->where('meta_key', 'document_id')->first();
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


        if ((isset($check_status) && $check_status->meta_value != 'finish') || !isset($check_status)) {
            $data .= '<button type="button" class="btn btn-secondary btn-view fillPDF"

       
  
          data-name="' . $this->name . '"
        data-dossier_id="' . $this->dossier->folder . '"
        data-form_id="' . $this->form_id . '">
        <i class="fas fa-file-pdf"></i> Générer';
        }




        $data .= '</td>';


        $data .= '<td class="w-30" ><div >';


        // $data .=$this->value;

        $extension = explode('.', $this->value);


        $filePath = storage_path('app/public/dossiers/' . $this->dossier->folder . '/' . $this->name . '.pdf');  // File system path


        if (file_exists($filePath) && !empty($this->value)) {
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

        if (isset($this->optionsArray['signable']) && $this->optionsArray['signable'] == 'true' && $this->value) {


            if ((isset($check_status) && $check_status->meta_value != 'finish') || !isset($check_status)) {
                if (!$check_signature) {
                    $data .= '<button type="button" class="btn btn-warning btn-view signable"
                data-toggle="modal" 
                   data-dossier_id="' . $this->dossier->folder . '"';
                    $data .= "data-generation='" . $generation . "'";
                    $data .= "data-form_id='" . $this->form_id . "'";
                    $data .= "data-fields='" . json_encode($this->optionsArray['fields']) . "'";
                    $data .= 'data-template="' . $this->optionsArray['template'] . '"

                data-name="' . $this->name . '">
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

                    $data .= 'data-template="' . $this->optionsArray['template'] . '"
                data-name="' . $this->config->title . '">
                <i class="fas fa-eye"></i> Télécharger le document signé
            </button> ';
                    $data .= '<div id="message_' . $this->optionsArray['template'] . '">';

                    if ($check_status) {
                        if ($check_status->meta_value == 'ongoing') {
                            $data .= '<div>Le document est en cours de signature</div>';
                        }

                        if ($check_status->meta_value == 'done') {
                            $data .= '<div>Le document a été signé</div>';
                        }
                        if ($check_status->meta_value == 'finish') {
                            $data .= '<div>Le document a été signé</div>';
                        }
                    }
                    $data .= '<button type="button" class="btn btn-danger btn-view" wire:click="delete_signature(\''.$this->form_id.'\',\''.$this->name.'\')"
                    data-toggle="modal" 
                       data-dossier_id="' . $this->dossier->folder . '"';
                        $data .= "data-generation='" . $generation . "'";
                        $data .= "data-form_id='" . $this->form_id . "'";
                        $data .= "data-signature_request_id='" . $check_signature->meta_value . "'";
                        $data .= "data-document_id='" . $check_document->meta_value . "'";
    
                        $data .= 'data-template="' . $this->optionsArray['template'] . '"
                    data-name="' . $this->config->title . '">
                    <i class="fas fa-eye"></i> Annuler la demande de signature
                </button> ';
                }
                if(auth()->user()->type_id!=4) {
                    $data .= '<br/><button type="button" class="btn btn-primary btn-view" wire:click="mark_signed(\'' . $this->form_id . '\',\'' . $this->name . '\')"
                    data-toggle="modal" 
                    data-dossier_id="' . $this->dossier->folder . '"';
                    $data .= "data-form_id='" . $this->form_id . "'";

                    $data .= 'data-name="' . $this->config->title . '">
                    <i class="fas fa-eye"></i> Marquer comme signé (signature manuelle)
                    </button> ';
                }
               
                $data .= '</div>';
            } else {
                $data .= '<div id="message_' . $this->optionsArray['template'] . '">';
                $data .= 'Document signé';
                $data .= '</div>';
            }

            if(auth()->user()->id==1 || is_user_allowed('delete_doc')) {
                $data .= '<br/><button type="button" class="btn btn-danger btn-view" wire:click="delete_doc(\''.$this->form_id.'\',\''.$this->name.'\')"
                data-toggle="modal" 
                   data-dossier_id="' . $this->dossier->folder . '"';
                    $data .= "data-form_id='" . $this->form_id . "'";
    
                    $data .= 'data-name="' . $this->config->title . '">
                <i class="fas fa-eye"></i> Supprimer
            </button> ';
                    }


        }


        $data .= '</td>';


        $data .= '<td class="align-middle text-sm">';
        $data .= '<form action="' . route("upload_file", ["form_id" => $this->form_id, "folder" => "dossiers", "upload_image" => "dossiers", "clientId" => $this->dossier->folder, "template" => $this->name]) . '" class="dropzone dropzone_button bg-primary" id="dropzone-' . $this->name . '">';
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

        // return true;
    }

}