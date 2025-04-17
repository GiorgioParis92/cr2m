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
        try {
        if (!$this->value || $this->value == '' || $this->value == '[]') {
            return false;
        }
        
        $json_value = decode_if_json($this->value);
        
       

        if ($json_value) {
            $values = $json_value;
        } else {
            $values = [$this->value];
        }
    
        // Prepare display variable
        $text = '';
    
        // Title
        $text .= '<p class="s2" style="padding-top: 5pt; padding-left: 8pt; text-indent: 0pt; text-align: left;">'
               . $this->config->title
               . '</p>';
    
        // Counter for batch display (3 images per row)
        $count = 0;
    
        foreach ($values as $value) {

            if($value == false) {
                continue;
            }
            $filePath = storage_path('app/public/' . $value);
    
            $pathInfo = pathinfo($value);
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumbnail.' . $pathInfo['extension'];
            
            if (Storage::exists('public/' . $thumbnailPath)) {
                $filePath = storage_path('app/public/' . $thumbnailPath);
            } else {
                $filePath = storage_path('app/public/' . $value);
            }


            // Check if the file exists
            if (!empty($value) && file_exists($filePath)) {



                $fileSize = filesize($filePath);
    
                // If file is an image and larger than 1MB, compress it
                if ($fileSize > 1 * 1024 * 1024) { // 1MB
                    // $filePath = $this->compressImage($filePath, 1024 * 1024);
                }
            
                // Open a row every 3 images
                if ($count % 3 === 0) {
                    $text .= '<div class="row">';
                }
    
                // Convert to base64
                $imageData = base64_encode(file_get_contents($filePath));
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $src = 'data:image/' . $extension . ';base64,' . $imageData;
    
                // Display image
                $text .= '<div class="col-lg-3" style="width:32%; display:inline-block; vertical-align:top; margin-bottom:5px; margin-right:1%;">';
      
                    $text .= '<img src="' . $src . '" style="width:100%; height:auto;">';
                    // $text .= $thumbnailPath;
                

                $text .= '</div>';
    
                $count++;
    
                // Close row every 3 images
                if ($count % 3 === 0) {
                    $text .= '</div>';
                }
                if ($count % 12 === 0) {
                    $text .= '</table></div><div><table style="margin:auto;width:90%;border-collapse: collapse;margin-top:20px">';
                }
           

            }
        } 
     
        // If the last row isn't closed (not an exact multiple of 3 images)
        if ($count % 3 !== 0) {
            $text .= '</div>';
        }

        return $text;
        }
        catch (Exception $e) { return $e->getMessage();}
        
    }
    
    /**
     * Compresses an image to a maximum size of 1MB while maintaining quality.
     *
     * @param string $filePath Path to the image.
     * @param int $maxSize Maximum allowed size in bytes (1MB = 1024*1024).
     * @return string Path to the compressed image.
     */
    private function compressImage($filePath, $maxSize)
    {
        $imageInfo = getimagesize($filePath);
        $mime = $imageInfo['mime'];
    
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filePath);
                $outputFormat = 'jpeg';
                break;
            case 'image/png':
                $image = imagecreatefrompng($filePath);
                $outputFormat = 'png';
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($filePath);
                $outputFormat = 'webp';
                break;
            default:
                return $filePath; // Return original if format is not supported
        }
    
        $quality = 90;
        $compressedPath = storage_path('app/public/compressed_' . basename($filePath));
    
        do {
            if ($outputFormat == 'jpeg') {
                imagejpeg($image, $compressedPath, $quality);
            } elseif ($outputFormat == 'png') {
                imagepng($image, $compressedPath, round($quality / 10)); // PNG uses 0-9 scale
            } elseif ($outputFormat == 'webp') {
                imagewebp($image, $compressedPath, $quality);
            }
    
            clearstatcache();
            $newSize = filesize($compressedPath);
            $quality -= 5; // Reduce quality gradually
        } while ($newSize > $maxSize && $quality > 10);
    
        imagedestroy($image);
    
        return $compressedPath;
    }
    
}
