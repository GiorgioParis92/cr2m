<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\Storage;

class Upload extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $data = '';
// print_r($this);

        $wireModel = "formData.{$this->form_id}.{$this->name}";

        print_r($this->config->options ?? '');

        if(isset($this->config->options)) {
            $options=json_decode($this->config->options);
            dump($options);
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

        return $data;
    }


    public function check_value()
    {

        return true;
        // return Storage::disk('public')->exists($this->value);
    }

}