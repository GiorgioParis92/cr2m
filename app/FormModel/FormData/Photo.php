<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\Storage;

class Photo extends AbstractFormData
{
    public function render(bool $is_error)
    {
      

        if(!is_user_allowed($this->name)) {
            return '';
        }

        $json_value=decode_if_json($this->value);
       
        // $json_value=json_decode($this->value);
        
        if($json_value) {
            $values=$json_value;
        }
        else {
            $values=[$this->value];
        }
        
        $data = '';
        // $wireModel = "formData.{$this->form_id}.{$this->name}";
        // // $data .= $wireModel ?? '';
        // $data .= '<input type="text" wire:model.lazy="' . $wireModel . '">';
  // Generate the upload URL
  $uploadUrl = route("upload_file", [
    "form_id" => $this->form_id,
    "folder" => "dossiers",
    "clientId" => $this->dossier->folder,
    "template" => $this->name,
    "random_name" => "true",
    'config' => $this
]);


        $csrfToken = csrf_token();
     
        $data .= "<div class='row'>";
        $data .= "<div class='col-lg-3'>";

        $data .= '<div style="cursor:pointer" data-form_id="'.$this->form_id.'" data-upload-url="'.$uploadUrl.'" class="dropzone photo_button bg-secondary" id="dropzone-' . $this->name . '">';
        $data .= csrf_field(); // This will generate the CSRF token input field
        $data .= '<div style="color:white" class="dz-message"><i class="fas fa-camera"></i> ' . $this->config->title . '</div>';
        $data .= '</div>';

      
        $deleteUrl = route("delete_file");

        
    //     $data .= "<script>
    //         Dropzone.autoDiscover = false;

    //     var dropzoneElementId = '#dropzone-" . str_replace('.', '-', $this->name) . "';
    //     var dropzoneElement = document.querySelector(dropzoneElementId);
        
    //     if (dropzoneElement && !dropzoneElement.dropzone) {

    //         console.log(dropzoneElementId);
    //                 const dropzoneId = dropzoneElement.id;
    //     const key = dropzoneId.replace('dropzone-','');
    //     const uploadUrl = dropzoneElement.getAttribute('data-upload-url');
    //     const form_id = dropzoneElement.getAttribute('data-form_id');

    //         var dropzone = new Dropzone(dropzoneElement, {
    //             url: '{$uploadUrl}',
    //             method: 'post',
    //             headers: {
    //                 'X-CSRF-TOKEN': '{$csrfToken}'
    //             },
    //             maxFilesize: 50000,
    //             paramName: 'file',
    //             sending: function(file, xhr, formData) {
    //                 console.log(file);
    //                 formData.append('folder', 'dossiers');
    //                 formData.append('template', '{$this->name}');
    //                 formData.append('random_name', 'true');
    //             },
    //             init: function() {
    //                 this.on('success', function(file, response) {
    //                     console.log('Successfully uploaded:', response);
    //                     initializeDeleteButtons();
    //                 });
    //                 this.on('error', function(file, response) {
    //                     console.log('Upload error:', response);
    //                 });
    //             }
    //         });
    //     }
    // </script>";
    
            $data .="\r\n"."</script>"."\r\n";
        if(!is_array($values)) {
            $values = [$values];  // Transform into array if not already an array
        }
        if(is_array($values)) {
        foreach($values as $value) {
        if ($value) {
            $extension = explode('.', $value);

            $value_thumbnail = str_replace('.', '_thumbnail.', $value);
            $filePath_thumbnail = storage_path('app/public/' . $value_thumbnail);
        
            if (file_exists($filePath_thumbnail)) {
                $value = $value_thumbnail;
            }

            $filePath = storage_path('app/public/' . $value);  // File system path

            if(count($extension)>2) {
                $first=(explode('/',$extension[0]));
                $tag=$first[2];
                $index=$extension[2];
            
            }

            if($this->name!='photo_maison') {
                // dd($extension);
            }
            
            if (file_exists($filePath)) {
                if (end($extension) != 'pdf') {
                    
                    $data .= '<div style="display:inline-block">
                    <i data-dossier_id="$this->dossier_id" data-tag="'.($tag ?? $this->name).'" data-index="'.($index ?? '').'" data-val="' . $value . '" data-img-src="' . asset('storage/' . $value) . '" class="delete_photo btn btn-danger fa fa-trash bg-danger"></i>

                    <button  type="button" class="btn btn-success btn-view imageModal"
                        data-toggle="modal" data-target="imageModal"
                        data-img-src="' . asset('storage/' . $value) . '"
                        data-val="' . $value . '"
                        data-name="' . $this->config->title . '">';
                    $data .= '<img src="' . asset('storage/' . $value) . '">';
                    $data .= '<i style="display:block" class="fas fa-eye"></i>' . $this->config->title . '
                    </button></div>';
                } else {
                    $data .= '<div class="btn btn-success btn-view pdfModal"
                        data-toggle="modal" 
                        data-img-src="' . asset('storage/' . $value) . '"
                        data-val="' . $value . '"
                        data-name="' . $this->config->title . '">';
                    $data .= '<i class="fas fa-eye"></i>' . $this->config->title . '</div>';
                }
            }
            // $data .= "<script>initializePdfModals()</script>";
        }
        }
    } 
    
        $data .= "</div>";
        $data .= "</div>";
        

        return $data;
    }

    public function check_value()
    {
        // return Storage::disk('public')->exists($this->value);
    }

    public function render_pdf()
    {
        if(!$this->value || $this->value=='') {
            return false;
        }
        $json_value=decode_if_json($this->value);
       
        // $json_value=json_decode($this->value);
        
        if($json_value) {
            $values=$json_value;
        }
        else {
            $values=[$this->value];
        }

        

        $text='';

       
          
        $text.='<p class="s2" style="padding-top: 5pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">'.$this->config->title.'</p>';
        $text .= "<div class='row'>";
      
        
        foreach($values as $value) {
            $value_thumbnail = str_replace('.', '_thumbnail.', $value);
            $filePath_thumbnail = storage_path('app/public/' . $value_thumbnail);
        
            if (file_exists($filePath_thumbnail)) {
                $value = $value_thumbnail;
            }
        
            $filePath = storage_path('app/public/' . $value);
        
            if (!empty($value) && file_exists($filePath)) {
                // Compress the image and then convert it to Base64
                $imageData = compressImage($filePath, 70);  // Compress the image with 70% quality for JPEGs
                $src = 'data:image/' . pathinfo($filePath, PATHINFO_EXTENSION) . ';base64,' . $imageData;
        
                $text .= "<div class='col-lg-3' style='width:32%;display:inline-block;vertical-align:top;margin-bottom:5px;margin-right:1%'>";
                $text .= '<img src="' . $src . '" style="width:100%; height:auto;">';
                $text .= '</div>';
            }
        }
        
        $text.='</div>';
    
        return $text;
    }
}
