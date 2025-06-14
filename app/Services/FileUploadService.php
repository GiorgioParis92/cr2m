<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Add this line
use App\Http\Controllers\Api\OcrAnalyze; // Import the OcrAnalyze controller
use App\Models\Dossier;
use App\Models\Beneficiaire;
use App\Models\FormsConfig;
use App\FormModel\FormData\Photo;
use App\FormModel\FormData\Table;
use Livewire\Livewire; // Import the Livewire facade
use PDF; // Assuming you have barryvdh/laravel-dompdf installed
use Image; // Assuming you have intervention/image installed
use setasign\Fpdi\Fpdi;
use FPDF;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Imagick;

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

      
        $random_name = false;
        if ($request->folder == 'dossiers') {

        }

        if (isset($request->folder)) {
            $folder = $request->folder;

        }

        if (isset($request->clientId)) {
            $clientId = $request->clientId;

            if (isset($request->folder) && $request->folder == 'dossiers') {
               
                if (is_numeric($request->clientId)) {
                    $dossier = Dossier::where('id', $request->clientId)->first();
                } else {
                    $dossier = Dossier::where('folder', $request->clientId)->first();
                }

                $clientId = $dossier->folder;
            }


        }



        if (isset($request->random_name) && $request->random_name=='true') {

            $random_name = true;
        }

        if (isset($request->form_id)) {
            $form_id = $request->form_id;
        }

        $file = $request->file('file');

        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf', 'heic', 'webp'];
        $imagesExtensions = ['jpeg', 'jpg', 'png', 'gif', 'heic', 'webp'];

        $extension = strtolower($file->getClientOriginalExtension());



        if (!in_array($extension, $allowedExtensions)) {
            return false;
        } else {

        }

        $directory = "{$folder}/{$clientId}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Directory path where files will be stored
        $directoryPath = storage_path('app/public/' . $directory);
       
        // Find all files with the same base name in the directory
        foreach (glob($directoryPath . '/' . $originalFileName . '.*') as $existingFile) {

            unlink($existingFile);
        }

        // Prepare the new file name with the template if provided
        if ($request->has('template')) {
            $extension = $file->getClientOriginalExtension();
            if ($random_name) {
                $fileName = $request->input('template') . time() .uniqid() . '.' . $extension;
                // $fileName = $request->input('template') . '.' . $extension;

            } else {
                $fileName = $request->input('template') . '.' . $extension;

            }
        } else {
            $fileName = $file->getClientOriginalName();
        }
      
        // Save the new file
        $filePath = $file->storeAs($directory, $fileName, 'public');


        $fullPath = storage_path('app/public/' . $filePath);
        // Set the file permissions to 775
        // chmod($fullPath, 0775);
        
        // Convert HEIC to JPG if applicable
        $filePath = $this->convertHeicToJpg($filePath);
        
        // Save the compressed thumbnail version
        $thumbnailFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_thumbnail.' . $extension;

        //use this linux command to compress filepath filepath thumbnail

        //  convert $fileName -resize 800x600\> $thumbnailFileName

        $thumbnail_path='';
            if (in_array($extension, $imagesExtensions)) {
                $path = storage_path('app/public/' . $filePath);
                $thumbnail_path = storage_path('app/public/' . $directory . '/' . $thumbnailFileName);
                $resizeCommand = "convert $path -resize 800x600\> $thumbnail_path";
                exec($resizeCommand, $output, $returnCode);
                if(file_exists($thumbnail_path)) {
                    // chmod($thumbnail_path, 0775);
                }
     
            }
        


        DB::enableQueryLog();
        $index = '';

        $template = $request->input('template');

            if(isset($request->random_name) && $request->random_name=='false') {
                $random_name=false;
              
            }
            

          


        if ($random_name == true) {

            $index = '';
        
            $explode = (explode('.', $request->input('template')));
        
            if (is_array($explode) && count($explode) > 1) {
                $array = explode('.', $request->input('template'));
                $template = $request->input('template');
                $index = $array[2];
                $field = $array[3];
                
            } else {

                $template = $request->input('template');
                $index='';
            }
            $value = DB::table('forms_data')
                ->where('meta_key', $template)
                ->where("form_id", $form_id)
                ->where("dossier_id", $dossier->id)
                ->first();




            if ($value) {
                $json_value = json_decode($value->meta_value,true);
                if ($json_value) {
                    array_push($json_value, $filePath);
                } else {
                    $json_value = [];
                    array_push($json_value, $filePath);
                }
                $updatedJsonString = json_encode($json_value);
                if ($index != '') {
                
                    $json_array = json_decode($value->meta_value, true);
                
                    // Check if 'value' exists and is an array
                    if (!isset($json_array) ) {
                        // If 'value' is not set or not an array, initialize it as an array
                        $currentValue = $json_array ?? '';
                        $json_array = $currentValue !== '' ? [$currentValue] : [];
                    }
                
                    // Now safely append the file path
                    // $json_array[$index][$field]['value'][] = $filePath;

                    if($filePath!= false) {
                        $json_array[] = $filePath;

                    }
                    $updatedJsonString = json_encode($json_array);
                }
                
              
           
           
                $update = DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => '' . $dossier->id . '',
                        'form_id' => '' . $form_id . '',
                        'meta_key' => '' . $template . ''
                    ],
                    [
                        'meta_value' => '' . $updatedJsonString . '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
           
                if($update) {
                    // $docs=getDocumentStatuses($dossier->id,$dossier->etape_number);
                }

            } else {
                $json_value = [];
                if($filePath!= false) {
                array_push($json_value, $filePath);
                }
                $update = DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => '' . $dossier->id . '',
                        'form_id' => '' . $form_id . '',
                        'meta_key' => '' . $request->input('template') . ''
                    ],
                    [
                        'meta_value' => '' . json_encode($json_value) . '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                if($update) {
                    // $docs=getDocumentStatuses($dossier->id,$dossier->etape_number);
                }
            
            }
        } else {
   
            if (isset($request->upload_image) && $file->isValid() && in_array(strtolower($extension), ['jpeg', 'jpg', 'png', 'gif', 'bmp','webp'])) {
                $image = Image::make($file);
    
                $exif = @exif_read_data($file->getPathname());
                if ($exif && isset($exif['Orientation'])) {
    
    
    
                    switch ($exif['Orientation']) {
                        case 3:
                            $image->rotate(180);
                            break;
                        case 6:
                            $image->rotate(-90);
                            break;
                        case 8:
                            $image->rotate(90);
                            break;
                        case 4:
                            $image->rotate(-90);
                            break;
                    }
                }
    
                // Get the width and height of the image
                $width = $image->width();
                $height = $image->height();
    
    
    
                // Standardize the image orientation and dimensions
                if ($width > $height) {
                    // Rotate the image if it's wider than it is tall (landscape)
                    $image->rotate(90);
    
                }
    
                $image = $image->fit(595, 842); // 595x842 pixels corresponds to 210x297mm at 72dpi
    
                $tempImagePath = storage_path('app/public/' . $directory . '/temp_image.jpg');
                $image->save($tempImagePath);
    
                // Define the PDF file name and path
                $pdfFileName = $request->input('template') . '.pdf';
                $pdfFilePath = storage_path('app/public/' . $directory . '/' . $pdfFileName);
    
               
    
                if (file_exists($pdfFilePath)) {
                    // Append to existing PDF
                    $pdf = new FPDI();
                    $pageCount = $pdf->setSourceFile($pdfFilePath);
    
                    // Import existing pages
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($templateId);
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $pdf->useTemplate($templateId);
                    }
    
                    // Add a new page for the new image
                    $pdf->AddPage('P', 'A4');
                    $pdf->Image($tempImagePath, 0, 0, 210, 297);
                } else {
                    // Create a new PDF
                    $pdf = new FPDF();
                    $pdf->AddPage('P', 'A4');
                    $pdf->Image($tempImagePath, 0, 0, 210, 297);
    
    
                }
    
                // Save the updated or new PDF
                $pdf->Output($pdfFilePath, 'F');
    
              
    
                // Optionally, delete the temporary image file
                unlink($tempImagePath);
    
    
    
                $update = DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => '' . $dossier->id . '',
                        'form_id' => '' . $form_id . '',
                        'meta_key' => '' . $template . ''
                    ],
                    [
                        'meta_value' => '' . $directory . '/' . $pdfFileName . '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                if($update) {
                    // $docs=getDocumentStatuses($dossier->id,$dossier->etape_number);
                 }
                return $directory . '/' . $pdfFileName;
            }


            if(file_exists($thumbnail_path)) {
                $filePath=$thumbnail_path;
            }

            $update = DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $form_id . '',
                    'meta_key' => '' . $template . ''
                ],
                [
                    'meta_value' => '' . $filePath . '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            if($update) {
                // $docs=getDocumentStatuses($dossier->id,$dossier->etape_number);
            }

        }



      


        // $config = \DB::table('forms_config')
        //     ->where('form_id', $form_id)
        //     ->where('name', $template)
        //     ->first();

        // $table = new Table($config, $template, $form_id, $dossier->id);
        // $table->save_value();

        $pdfFileName = $request->input('template') . '.pdf';
        $pdfFilePath = storage_path('app/public/' . $directory . '/' . $pdfFileName);



 

        if ($request->identify) {
     
            
            
        if($request->fill_values) {
            $config=FormsConfig::where('form_id',$form_id)->where('name',$request->input('template'))->first();
            if($config) {
                $options=json_decode($config->options,true);

            }

            if($options) {
       
                foreach($options['fill_values'] as $key => $value) {
                    $values_to_fill[$key] = $value;
                }


            }
           
        }

        if(file_exists(storage_path("app/public/{$directory}/{$pdfFileName}")) ) {


            $update = DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $form_id . '',
                    // 'meta_key' => '' . $request->input('template') . '_identify'
                    'meta_key' => '' . $request->input('template') . '_identify'
                ],
                [
                    'meta_value' => '',
                    // 'meta_value' => 'ok',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

        $fullFilePath = storage_path("app/public/{$directory}/{$pdfFileName}");
    
        $response = Http::withHeaders([
            'User-Agent' => 'insomnia/10.2.0',
            'api-key' => 'R3SxEL6mbZ9UO9a',
            'secret' => '92ed7089d57110113239fb02750be52a',
            'Authorization' => 'Bearer b4rk74HZa15hfXKyrMI0fkC2sSRjGPBrS9rRxh6NhmjAj5EN7eNvugxTA0a2',
        ])->attach('document', file_get_contents($fullFilePath), $fileName)
          ->post('https://app.oceer.fr/api/pipeline/start/ce082c1e-e940-4b92-b7c8-9ac3effc6602');

        $apiResponse = $response->json();

        if($apiResponse) {
            $update = DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $form_id . '',
                    // 'meta_key' => '' . $request->input('template') . '_identify'
                    'meta_key' => '' . $request->input('template') . '_identify'
                ],
                [
                    'meta_value' => '' . json_encode($apiResponse) . '',
                    // 'meta_value' => 'ok',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
   


        }

     




    }

        $beneficiaire=Beneficiaire::where('id',$dossier->beneficiaire_id)->first();
 

        if (in_array($extension, $allowedExtensions) ) {
        
        $response = Http::withHeaders([
            'User-Agent'      => 'laravel-app',
            'X-CEERTIF-KEY'   => '430324fb959d9a45790c03d7d4338c57',
            'X-CEERTIF-SECRET'=> '92ed7089d57110113239fb02750be52a',
            'Cookie'          => 'PHPSESSID=ck2ch79ith81tl4c8taqn9uoqm',
        ])->attach(
            'file',
            file_get_contents(storage_path('app/public/' . $directory . '/' . $fileName)),
            $fileName
        )->asMultipart()->post('https://app.ceertif.com/api-v2/upload/upload.php', [
            'address'        => ($beneficiaire->numero_voie ?? '').' '.($beneficiaire->adresse ?? '').' '.($beneficiaire->cp ?? '').' '.($beneficiaire->ville ?? ''),
            'upload_time'    => now()->format('Y-m-d H:i:s'),
            'timestamp_type' => 'both',
            'callback_url'   => 'https://crm.genius-market.fr/server-callback',
            'opportunity_id' => $dossier->id,
        ]);

   

        if (!$response->successful()) {
            throw new \Exception('Ceertif API error: ' . $response->body());
        } else {
            // return $response;
        }
        }

        return $filePath;



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

                    return 'deleted';
            } else {
        DB::table('forms_data')
                    ->where('id', $value->id)->where('meta_value',$request->link) // Assuming 'id' is the primary key
                    ->delete();
                return $json_value;
        
            }
        }
        if($request->dossier_id) {
            $dossier = Dossier::where('id', $request->dossier_id)->first();

            // $docs=getDocumentStatuses($dossier->id,$dossier->etape_number);
        }

        try {

            if($request->link && ($request->link=='' || $request->link=='dossiers' || $request->link=='dossiers/')) {
                return 'nique ta mere';
            }
            unlink(storage_path('app/public/' . $request->link));
        } catch (\Throwable $th) {
            //throw $th;
        }
        


    }
    private function convertHeicToJpg($filePath)
    {
        $heicPath = storage_path("app/public/{$filePath}");
        
        if (!file_exists($heicPath)) {
            return $filePath; // Return original path if file doesn't exist
        }
    
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($extension !== 'heic') {
            return $filePath; // Return original path if not HEIC
        }
    
        try {
            // Load the HEIC file
            $image = new Imagick($heicPath);
            $image->setImageFormat('jpeg');
    
            // Extract the folder and filename
            $dirName = pathinfo($filePath, PATHINFO_DIRNAME);
            $baseName = pathinfo($filePath, PATHINFO_FILENAME);
            $jpgFileName = $baseName . '.jpg';
            $jpgFilePath = "{$dirName}/{$jpgFileName}";
            $outputPath = storage_path("app/public/{$jpgFilePath}");
    
            // Save the converted file
            $image->writeImage($outputPath);
    
            // Cleanup
            $image->clear();
            $image->destroy();
    
            // Optionally delete the original HEIC file
            unlink($heicPath);
    
            return $jpgFilePath; // Return the new file path
        } catch (\Exception $e) {
            \Log::error("HEIC to JPG conversion failed: " . $e->getMessage());
            return $filePath; // Fallback to the original path
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
