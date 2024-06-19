<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Add this line
use App\Http\Controllers\Api\OcrAnalyze; // Import the OcrAnalyze controller

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
    public function storeImage(Request $request, string $folder = null, int $clientId = null, string $inputName = 'file')
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
        }
        if (isset($request->form_id)) {
            $form_id = $request->form_id;
        }
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf'];
            $extension = strtolower($file->getClientOriginalExtension());


            if (!in_array($extension, $allowedExtensions)) {
                return false;
            }

            $directory = "{$folder}/{$clientId}";
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            $fileName = $file->getClientOriginalName();
            if ($request->has('template')) {

                $fileName = $request->input('template') . '.' . $extension;
            }
            // dump($file);
            // dump($extension);
            // dump($form_id);
            // dump($request->input('template'));
            // dd($fileName);
            $filePath = $file->storeAs($directory, $fileName, 'public');



            $update=DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => ''.$clientId.'',
                    'form_id' => ''.$form_id.'',
                    'meta_key' => ''.$request->input('template').''
                ],
                [
                    'meta_value' => $fileName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        




            return $filePath;
        }

        return false;
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
