<?php
namespace App\Http\Livewire\Forms;

use Livewire\Component;
use App\Models\{
    Dossier,
    Etape,
    DossiersActivity,
    User,
    Form,
    Forms,
    FormConfig,
    Rdv,
    RdvStatus,
    Client,
    FormsData,
    Card
};
use Imagick;
use Illuminate\Support\Facades\Storage;

class Photo extends AbstractData
{
    public $listeners = [
        'fileUploaded' => 'handleFileUploaded'
    ];
 

    public function convertHeicToJpg($filePath)
    {
        $heicPath = storage_path("app/public/{$filePath}");
        // dd(($heicPath));
        if (!file_exists($heicPath)) {
          
            return $filePath; // Return original if file doesn't exist
        }
       
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
     
        if (strtolower($extension) !== 'heic') {
         
            return $filePath; // Return original if not HEIC
        }
        
        try {
          
            // $image = new Imagick($heicPath);
            $image = imagecreatefromjpeg($heicPath);

            // $image->setImageFormat('jpeg');
            
            $dirName     = pathinfo($filePath, PATHINFO_DIRNAME);   // e.g. "some_folder/12345"
            $baseName    = pathinfo($filePath, PATHINFO_FILENAME);  // e.g. "originalFilename"
            $jpgFileName = $baseName . '.jpg';
            

            // Construct the NEW path in the SAME folder
            $jpgFilePath = "{$dirName}/{$jpgFileName}";
            // dd($jpgFilePath);
            // Save converted file
            $outputPath = storage_path("app/public/{$jpgFilePath}");
            $image->writeImage($outputPath);
        
            // // Cleanup
            // $image->clear();
            // $image->destroy();
    
            // Optionally delete original HEIC file
            unlink($heicPath);
      
            return $jpgFilePath; // Return new file path
        } catch (\Exception $e) {
            \Log::error("HEIC to JPG conversion failed: " . $e->getMessage());
            return $filePath; // Fallback to original file
        }
    }
    
    public function handleFileUploaded($response)
    {
        // Here, $response should contain the file path or filename that was uploaded.
        $filePath = $response['file_path'] ?? null;

        if ($filePath) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);


            // Check if the file is HEIC and convert it to JPG
            if (strtolower($extension) === 'heic' || strtolower($extension) === 'HEIC') {
                $convertedFilePath = $this->convertHeicToJpg($filePath);

                if ($convertedFilePath) {
                    $filePath = $convertedFilePath;
                }
            }

            // Update the values array
            $currentValues = $this->values;
            $currentValues[] = $filePath;

            // Update the property
            $this->values = $currentValues;
            $this->value = $currentValues; // keep them in sync if needed

            // Persist in the database
            FormsData::updateOrCreate(
                [
                    'dossier_id' => $this->dossier_id,
                    'form_id' => $this->form_id,
                    'meta_key' => $this->conf->name
                ],
                [
                    'meta_value' => json_encode($currentValues) // Store as JSON if multiple files
                ]
            );
        }
    }

    // private function convertHeicToJpg($heicFilePath)
    // {
    //     try {
    //         $heicPath = storage_path("app/public/{$heicFilePath}");
    //         $image = new Imagick($heicPath);

    //         // Set the image format to JPG
    //         $image->setImageFormat('jpeg');

    //         // Generate a new file name
    //         $jpgFileName = pathinfo($heicFilePath, PATHINFO_FILENAME) . '.jpg';
    //         $jpgFilePath = "dossiers/{$jpgFileName}";

    //         // Save the converted image to the storage
    //         $outputPath = storage_path("app/public/{$jpgFilePath}");
    //         $image->writeImage($outputPath);

    //         // Cleanup
    //         $image->clear();
    //         $image->destroy();

    //         // Delete the original HEIC file if needed
    //         Storage::delete("public/{$heicFilePath}");

    //         return $jpgFilePath; // Return the new JPG file path
    //     } catch (\Exception $e) {
    //         // Log or handle errors during conversion
    //         \Log::error("HEIC to JPG conversion failed: " . $e->getMessage());
    //         return null;
    //     }
    // }

    public function mount($conf, $form_id, $dossier_id)
    {
        parent::mount($conf, $form_id, $dossier_id);
    
        $this->dossier = Dossier::find($dossier_id);
        $json_value = decode_if_json($this->value);
    
        if ($json_value) {
            $values = $json_value;
        } else {
            // If $this->value is a single string, wrap it in an array
            $values = [$this->value];
        }
  
        // Convert any existing HEIC to JPG
        $updatedValues = [];
        if (isset($values) && !empty($values)) {
            $updatedValues = [];
        
            foreach ($values as $originalPath) {
                // Skip null or empty values
                if (!$originalPath) {
                    continue;
                }
        
                $extension = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
                $thumbnailFileName = pathinfo($originalPath, PATHINFO_FILENAME) . '_thumbnail.' . $extension;
                $thumbnailAbsolutePath = storage_path("app/public/dossiers/".$this->dossier->folder."/{$thumbnailFileName}");
                $thumbnailPath = "dossiers/{$this->dossier->folder}/{$thumbnailFileName}";

        
  
                    if ($extension === 'heic') {
                        // Convert HEIC to JPG
                        $convertedPath = $this->convertHeicToJpg($originalPath);
            
                        // If conversion was successful, store the converted path
                        $updatedValues[] = $convertedPath ?: $originalPath;
                    } else {
                        // Keep original path for non-HEIC files
                        $updatedValues[] = $originalPath;
                    }
                
        

            }
        }
        
        // print_r($updatedValues);
        // Persist the updated list to the DB if changes happened
        if (!empty($updatedValues) && ($updatedValues !== $values)) {
            FormsData::updateOrCreate(
                [
                    'dossier_id' => $this->dossier_id,
                    'form_id' => $this->form_id,
                    'meta_key' => $this->conf->name ?? ''
                ],
                [
                    'meta_value' => json_encode($updatedValues)
                ]
            );
        }
    
        // Now update the component properties
        $this->values = $updatedValues;
        // $this->value = $updatedValues;

  
    }
    

    public function getErrorMessage()
    {
        return '';
    }

    protected function validateValue($value): bool
    {
        return true;
    }

    public function render()
    {
    
        return view('livewire.forms.photo');
    }
}
