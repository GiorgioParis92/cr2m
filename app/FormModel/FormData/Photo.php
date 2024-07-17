<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\Storage;

class Photo extends AbstractFormData
{
    public function render(bool $is_error)
    {
        $data = '';
        $wireModel = "formData.{$this->form_id}.{$this->name}";
        $data .= '<input type="hidden" wire:model.lazy="' . $wireModel . '">';

        $csrfToken = csrf_token();
        $data .= "<div class='row'>";
        $data .= "<div class='col-lg-3'>";



        $data .= '<div style="cursor:pointer" class="dropzone photo_button bg-secondary" id="dropzone-' . $this->name . '">';
        $data .= csrf_field(); // This will generate the CSRF token input field
        $data .= '<div style="color:white" class="dz-message"><i class="fas fa-camera"></i> ' . $this->config->title . '</div>';
        $data .= '</div>';

        // Generate the upload URL
        $uploadUrl = route("upload_file", [
            "form_id" => $this->form_id,
            "folder" => "dossiers",
            "clientId" => $this->dossier->folder,
            "template" => $this->name,
            "random_name" => "true",
            'config' => $this
        ]);
        $deleteUrl = route("delete_file");
        // Dropzone script
        $data .= "<script>
         
            var dropzoneElementId = '#dropzone-" . $this->name . "';
            var dropzoneElement = document.querySelector(dropzoneElementId);
            
            var dropzone = new Dropzone(dropzoneElement, {
                url: '{$uploadUrl}', // Use the generated upload URL
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{$csrfToken}'
                },
                paramName: 'file',
                sending: function(file, xhr, formData) {
                    formData.append('folder', 'dossiers');
                    formData.append('template', '{$this->name}');
                    formData.append('random_name', 'true');
                },
                init: function() {
                    this.on('success', function(file, response) {
                        console.log(response);
                 
                        console.log('Successfully uploaded:', response);
                    });
                    this.on('error', function(file, response) {
                        console.log('Upload error:', response);
                    });
                }
            });
        
            $('.delete_photo').click(function(){
                var link=$(this).data('val');
                $.ajax({
            url: '{$deleteUrl}',
            method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{$csrfToken}'
                },
            data: {
                    link: link,
                   
                },
            success: function(response) {
          
    
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(', ');
                }
        
            }
        });
            })
        
        </script>";
        if ($this->value) {
            $extension = explode('.', $this->value);



            $filePath = storage_path('app/public/' . $this->value);  // File system path

            // $data.=$filePath;

            if (file_exists($filePath)) {
                if (end($extension) != 'pdf') {
                    $data .= '<div><button type="button" class="btn btn-success btn-view imageModal"
        data-toggle="modal" data-target="imageModal"
        data-img-src="' . asset('storage/' . $this->value) . '"
        data-val="'.$this->value . '"
        data-name="' . $this->config->title . '">';
                    $data .= '<img src="' . asset('storage/' . $this->value) . '">';
                    $data .= '<i class="fas fa-eye"></i>' . $this->config->title . '
    </button> <i data-val="'.$this->value . '" data-img-src="' . asset('storage/' . $this->value) . '" class="delete_photo btn btn-danger fa fa-trash bg-danger"></i></div>';
                } else {
                    $data .= '<div class="btn btn-success btn-view pdfModal"
        data-toggle="modal" 
         
        data-img-src="' . asset('storage/' . $this->value) . '"
        data-val="'.$this->value . '"
        data-name="' . $this->config->title . '">';

                    $data .= '<i class="fas fa-eye"></i>' . $this->config->title . '</div>';
                }
            }
            $data .= "<script>initializePdfModals()</script>";
        }
        $data .= "</div>";
        $data .= "</div>";

        return $data;
    }

    public function check_value()
    {
        return Storage::disk('public')->exists($this->value);
    }
}
