<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
    public function storeImage(Request $request, string $folder=null, int $clientId=null, string $inputName = 'file')
    {
      
        if(isset($request->folder)) {
            $folder=$request->folder;
            
        }
        if(isset($request->clientId)) {
            $clientId=$request->clientId;
        }
        if(isset($request->form_id)) {
            $form_id=$request->form_id;
        }
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf'];
            $extension = $file->getClientOriginalExtension();

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

            $filePath = $file->storeAs($directory, $fileName, 'public');


            if(isset($request->form_id)) {
                DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => $clientId,
                        'form_id' => $form_id,
                        'meta_key' => $request->input('template')
                    ],
                    [
                        'meta_value' => $filePath,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

        

            }

            return $filePath;
        }

        return false;
    }
}
