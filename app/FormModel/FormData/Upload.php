<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use App\Models\FormsData;
use Illuminate\Support\Facades\Storage;

class Upload extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $data = '';
        $identify_array=[];
        $result_value=false;
        $result_score=false;
        $check_identify=false;
        $check=false;
        $wireModel = "formData.{$this->form_id}.{$this->name}";

        // print_r($this->config->options ?? '');

        if (isset($this->config->options)) {
            $options = json_decode($this->config->options, true); // Decode as associative array
        
            $identify = !empty($options['identify']); // Check if 'identify' exists and is true
        }

        if($identify) {
            $check_identify =  FormsData::where('dossier_id', $this->dossier_id)
            ->where('meta_key', $this->name.'_identify')
            ->first();
           
            // dump($check_identify);

            if($check_identify && $check_identify->meta_value) {
                
                $identify_array=(json_decode($check_identify->meta_value));
            
                $result_value=$identify_array->document->data->identification_results->results->document_identification->value;
                $result_score=$identify_array->document->data->identification_results->results->document_identification->score;
              
              
                    $check=true;
                

            }


        }

        $data = '<tr>';


        $data .= '<td>';
        $data .= '<div class="d-flex px-2 py-1 align-items-center">';
        
        
        $data .= $this->config->title;

        $data .= '</div>';
        $data .= '</td>';


        $data .= '<td>';

        $data .= '</td>';

        $data .= '<td class="w-30" ><div >';
   
        $data.='<input type="hidden" id="doc-'.$this->form_id.$this->name.'" wire:model="' . $wireModel . '">';
            

        if($this->value) {
        $extension = explode('.', $this->value);

      

            $filePath = storage_path('app/public/' . $this->value);  // File system path

            // $data.=$filePath;
      
            if (file_exists($filePath)) {
                if (end($extension) != 'pdf') {
                    $data .= '<button type="button" class="btn btn-success btn-view imageModal"
            data-toggle="modal" data-target="imageModal"
            data-img-src="' . asset('storage/' . $this->value).'"
            data-name="' . $this->config->title . '">
            <i class="fas fa-eye"></i> Visualiser
        </button> ';
                } else {
                    $data .= '<div class="btn btn-success btn-view pdfModal"
            data-toggle="modal" 
             
            data-img-src="' . asset('storage/' . $this->value) . '"
            data-name="' . $this->config->title . '">
            <i class="fas fa-eye"></i> Visualiser</div>';
                }
            }

        




        $data .= '</div>';
        }
        $data .= '</td>';


        $data .= '<td class="align-middle text-sm">';
        $data .= '<form method="post" action="' . route("upload_file", ["form_id" => $this->form_id, "folder" => "dossiers", "upload_image" => "dossiers", "clientId" => ($this->dossier->folder ?? $this->dossier_id), "template" => $this->name,'config'=>$this]) . '" class="dropzone dropzone_button bg-primary" id="dropzone-' . $this->name . '">';
        $data .= csrf_field(); // This will generate the CSRF token input field
        $data .= '<input type="hidden" name="template" value="'.$this->name.'">';
        $data .= '<input type="hidden" name="dossier_id" value="'.$this->dossier_id.'">';
        $data .= '<input type="hidden" name="form_id" value="'.$this->form_id.'">';
        $data .= '<input type="hidden" name="clientId" value="'.($this->dossier->folder ?? $this->dossier_id).'">';
        $data .= '<input type="hidden" name="random_name" value="false">';
        $data .= '<input type="hidden" name="upload_image" value="true">';
        $data .= '<div class="dz-message"><i class="fas fa-arrow-up"></i> Upload';

        $data .= '</div>';
        $data .= '</form>';

        $data .= '</td>';
        $data .= '</tr>';


        if($check_identify && $this->value!='') {
        if(!empty($identify_array) && $result_value==$this->name) {
           
            
            $data .= '<tr>';
            $data .= '<td colspan="4" style="text-align:center">';
            $data.='<div class="txt-center alert alert-success font-weight-bold  text-white " role="alert">
            <strong>Document identifié par OCEER comme '.$this->config->title.' à '.number_format($result_score*100,2).'% </strong>
          </div>';
          
        
            $data .= '</td>';
            $data .= '</tr>';
        }


        if(!empty($identify_array) && $result_value!=$this->name) {
           
            
            $data .= '<tr>';
            $data .= '<td colspan="4" style="text-align:center">';
            $data.='<div class="txt-center alert alert-danger font-weight-bold  text-white " role="alert">
              <strong>Le document n\'a pas été identifié par OCEER comme '.$this->config->title.'</strong>
            </div>';
            $data .= '</td>';
            $data .= '</tr>';
        }

    


        if(empty($identify_array) && $check_identify) {
            $data .= '<tr>';
            $data .= '<td colspan="4" style="text-align:center">';
            $data.='<div class="txt-center alert alert-warning font-weight-bold  text-white " role="alert">
              <strong>Document en cours d\'analyse par OCEER</strong>
            </div>';
            $data .= '</td>';
            $data .= '</tr>';
        }
    }

        return $data;
    }


    public function check_value()
    {

        return true;
        // return Storage::disk('public')->exists($this->value);
    }

}