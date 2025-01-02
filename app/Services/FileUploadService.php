<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Add this line
use App\Http\Controllers\Api\OcrAnalyze; // Import the OcrAnalyze controller
use App\Models\Dossier;
use App\FormModel\FormData\Photo;
use App\FormModel\FormData\Table;
use Livewire\Livewire; // Import the Livewire facade
use PDF; // Assuming you have barryvdh/laravel-dompdf installed
use Image; // Assuming you have intervention/image installed
use setasign\Fpdi\Fpdi;
use FPDF;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class FileUploadService
{
    /**
     * Store an uploaded image in a specific folder structure.
     *
     * @param Request $request
     * @param string $folder
     * @param int $clientId
     * @param string $inputName
     * @return string|false
     */
    public function storeImage(Request $request, string $folder = null, int $clientId = null, string $inputName = 'file', bool $random_name = false)
    {
        // Other initialization logic...
        
        $file = $request->file('file');
    
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf', 'heic', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
    
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }
    
        $directory = "{$folder}/{$clientId}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
    
        // Determine file name
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        if ($request->has('template')) {
            $extension = $file->getClientOriginalExtension();
            $fileName = $random_name 
                ? $request->input('template') . time() . '.' . $extension 
                : $request->input('template') . '.' . $extension;
        } else {
            $fileName = $file->getClientOriginalName();
        }
    
        // Save the file to storage
        $filePath = $file->storeAs($directory, $fileName, 'public');
        $fullPath = storage_path('app/public/' . $filePath);
    
        // Set permissions for the uploaded file
        chmod($fullPath, 0775);
    
        // Check if the file is HEIC and convert it to JPG
        if ($extension === 'heic') {
            $convertedFilePath = $this->convertHeicToJpg($filePath);
    
            if ($convertedFilePath) {
                $filePath = $convertedFilePath;
            }
        }
    
        // Save a compressed thumbnail if applicable
        $thumbnailFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_thumbnail.jpg';
        $thumbnailPath = storage_path('app/public/' . $directory . '/' . $thumbnailFileName);
    
        if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif', 'webp'])) {
            $resizeCommand = "convert $fullPath -resize 800x600\\> $thumbnailPath";
            exec($resizeCommand, $output, $returnCode);
    
            if (file_exists($thumbnailPath)) {
                chmod($thumbnailPath, 0775);
            }
        }
    
        // Update forms_data table or any other required database operations
        // (Omitted for brevity but similar to your existing implementation)
    
        return $filePath;
    }
    
    private function convertHeicToJpg($filePath)
    {
        $heicPath = storage_path("app/public/{$filePath}");
        if (!file_exists($heicPath)) {
            return $filePath; // Return original if file doesn't exist
        }
    
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'heic') {
            return $filePath; // Return original if not HEIC
        }
    
        try {
            $image = new Imagick($heicPath);
            $image->setImageFormat('jpeg');
    
            // Generate new file path
            $jpgFileName = pathinfo($filePath, PATHINFO_FILENAME) . '.jpg';
            $jpgFilePath = "dossiers/{$jpgFileName}";
    
            // Save converted file
            $outputPath = storage_path("app/public/{$jpgFilePath}");
            $image->writeImage($outputPath);
    
            // Cleanup
            $image->clear();
            $image->destroy();
    
            // Optionally delete original HEIC file
            unlink($heicPath);
    
            return $jpgFilePath; // Return new file path
        } catch (\Exception $e) {
            \Log::error("HEIC to JPG conversion failed: " . $e->getMessage());
            return $filePath; // Fallback to original file
        }
    }
    
    private function getBestMatch($resultData, $pdfFileName)
    {


        // Find the key with the highest score
        $bestResultKey = array_keys($resultData[0], max($resultData[0]))[0];
        // If the best result is "other", return "other"
        if ($bestResultKey === "other") {
            return "other";
        }

        // Remove '_first' from the best result key
        $bestResultKeyClean = str_replace('_first', '', $bestResultKey);

        // Check if the cleaned key matches the PDF file name
        if ($bestResultKeyClean === $pdfFileName) {
            return $bestResultKeyClean;
        }

        // If no match, return "other"
        return "other";
    }
    protected function callOcrAnalyzeDirectly(Request $request)
    {
        $ocrController = new OcrAnalyze();
        $response = $ocrController->index($request);

        if ($response->getStatusCode() === 200) {
            $responseData = json_decode($response->getContent(), true);
            // Add your custom logic here based on the OCR response
            return $responseData;
        }

        return false;
    }
    public function deleteImage(Request $request)
    {


        $value = DB::table('forms_data')
            ->where('dossier_id', $request->dossier_id)
            ->where('meta_key', $request->tag)
            ->first();
        if ($value) {
            $json_value = json_decode($value->meta_value);


            if (is_array($json_value) && in_array($request->link, $json_value)) {
                // Remove the value from the array
                $json_value = array_filter($json_value, function ($item) use ($request) {
                    return $item !== $request->link;
                });

                // Reindex the array
                $json_value = array_values($json_value);
                // Encode back to JSON
                $new_json_value = json_encode($json_value);
                // Update the meta_value in the database
                if($new_json_value=='[]') {
                    $new_json_value='';
                }

         

                DB::table('forms_data')
                    ->where('id', $value->id) // Assuming 'id' is the primary key
                    ->update(['meta_value' => (!empty($new_json_value) ? $new_json_value : '')]);
            } else {
                DB::table('forms_data')
                    ->where('id', $value->id) // Assuming 'id' is the primary key
                    ->delete();
            }
        }
        if($request->dossier_id) {
            $dossier = Dossier::where('id', $request->dossier_id)->first();

            // $docs=getDocumentStatuses($dossier->id,$dossier->etape_number);
        }

        try {
            unlink(storage_path('app/public/' . $request->link));
        } catch (\Throwable $th) {
            //throw $th;
        }
        


    }
  function convertHeicToJpg($filePath)
    {
        $heicPath = storage_path("app/public/{$filePath}");
        if (!file_exists($heicPath)) {
            return $filePath; // Return original if file doesn't exist
        }
    
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'heic') {
            return $filePath; // Return original if not HEIC
        }
    
        try {
            $image = new Imagick($heicPath);
            $image->setImageFormat('jpeg');
    
            // Generate new file path
            $jpgFileName = pathinfo($filePath, PATHINFO_FILENAME) . '.jpg';
            $jpgFilePath = "dossiers/{$jpgFileName}";
    
            // Save converted file
            $outputPath = storage_path("app/public/{$jpgFilePath}");
            $image->writeImage($outputPath);
    
            // Cleanup
            $image->clear();
            $image->destroy();
    
            // Optionally delete original HEIC file
            unlink($heicPath);
    
            return $jpgFilePath; // Return new file path
        } catch (\Exception $e) {
            \Log::error("HEIC to JPG conversion failed: " . $e->getMessage());
            return $filePath; // Fallback to original file
        }
    }

    public function identify_doc($filePath)
    {

        // Get the real path of the file
        $file = $filePath;


        // Check if the file exists and get its real path
        if (!file_exists($file)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Use the file path directly
        $data = array(
            'service' => 'document_detection',
            'token' => 'd4a44a75-42b7-4ad9-adb4-1792de177384',
            'model' => 'atlas',
            'file' => new \CURLFile($file), // Use \CURLFile to send file via cURL
        );

        // Send the request
        $response = makeRequest('https://oceer.fr/api/document_detection', $data);

        return $response;
    }

}
