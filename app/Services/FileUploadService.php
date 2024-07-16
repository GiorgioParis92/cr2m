<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Add this line
use App\Http\Controllers\Api\OcrAnalyze; // Import the OcrAnalyze controller
use App\Models\Dossier;
use App\FormModel\FormData\Photo;

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
    public function storeImage(Request $request, string $folder = null, int $clientId = null, string $inputName = 'file', bool $random_name=false )
    {
     
        if ($request->folder == 'dossiers') {
            if (isset($request->analyze)) {
                // $ocrResponse = $this->callOcrAnalyzeDirectly($request);
            } else {
                // $ocrResponse = false;
            }

            // 

            // if (!$ocrResponse) {
            //     return false; // Handle OCR failure as needed
            // } else {
            //     $result = $ocrResponse['result']['data']['analyze_result'];
            //     $array_result = $result;
            //     foreach ($array_result as $key => $value) {
            //         $meta_value='';

            //         // Check if the value is an array and convert to JSON string if true
            //         if(isset($value['value'])) {

            //             if (is_array($value['value'])) {
            //                 $meta_value = json_encode($value['value']);
            //             } else {
            //                 $meta_value = $value['value'];
            //             }
            //         }


            //         \DB::table('dossiers_data')->updateOrInsert(
            //             [
            //                 'dossier_id' => $request->clientId,
            //                 'meta_key' => $key
            //             ],
            //             [
            //                 'meta_value' => $meta_value,
            //                 'created_at' => now(),
            //                 'updated_at' => now()
            //             ]
            //         );
            //     }

            // }
        }

        if (isset($request->folder)) {
            $folder = $request->folder;

        }
        if (isset($request->clientId)) {
            $clientId = $request->clientId;

            if(isset($request->folder) && $request->folder=='dossiers') {
                if(is_numeric($request->clientId)) {
                    $dossier=Dossier::where('id',$request->clientId)->first();
                } else {
                    $dossier=Dossier::where('folder',$request->clientId)->first();
                }
                
                $clientId=$dossier->folder;
            }
           

        }
        dd($request);


        if (isset($request->$random_name)) {
            $random_name = true;
        }

        if (isset($request->form_id)) {
            $form_id = $request->form_id;
        }
       
        
            $file = $request->file('file');

            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf'];
            $extension = strtolower($file->getClientOriginalExtension());
          


            if (!in_array($extension, $allowedExtensions)) {
                return false;
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
                if($random_name) {
                    $fileName = $request->input('template') .time(). '.' . $extension;

                } else {
                    $fileName = $request->input('template') . '.' . $extension;

                }
            } else {
                $fileName = $file->getClientOriginalName();
            }
     
            // Save the new file
            $filePath = $file->storeAs($directory, $fileName, 'public');
            
            DB::enableQueryLog();

            $update=DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => ''.$dossier->id.'',
                    'form_id' => ''.$form_id.'',
                    'meta_key' => ''.$request->input('template').''
                ],
                [
                    'meta_value' => ''.$filePath.'',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            // $photo = new Photo($form_id, $request->input('template'), $filePath, $this);
            // $photo->save_value();

            return $filePath;
        


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
}
