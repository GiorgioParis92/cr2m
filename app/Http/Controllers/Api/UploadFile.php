<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Beneficiaire;
use App\Models\FormsConfig;
use App\Models\FormsData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Intervention\Image\Facades\Image; // Use Intervention Image
use App\Services\CardCreationService;
use Illuminate\Support\Facades\Http;

class UploadFile extends \App\Http\Controllers\Controller
{

    protected $cardService;

    public function __construct(CardCreationService $cardService)
    {
        $this->cardService = $cardService;
    }
    
    // Fetch all beneficiaires with dynamic filtering
    public function index(Request $request)
    {
       
        if (!isset($request->dossier_id)) {

            if(isset($request->folder)) {
                $dossier = Dossier::where('folder', $request->folder)->first();
                $folder = $dossier->folder ?? 'default';
            } else {
                return response()->json('dossier_id required', 200);
            }

         
        } else {
            $dossier = Dossier::where('id', $request->dossier_id)->first();
            $folder = $dossier->folder ?? 'default';
        }

        if (!isset($request->name)) {
            return response()->json('name required', 200);
        }

        if (!isset($request->file)) {
            return response()->json('file required', 200);
        }

        $file = $request->file('file');
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf', 'heic', 'webp','wav'];
        $imagesExtensions = ['jpeg', 'jpg', 'png', 'gif', 'heic', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            return response()->json('Invalid file type', 400);
        }

        $directory = "dossiers/{$folder}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }


        $fileName = $request->name.'.'.$extension;
        $filePath = $file->storeAs($directory, $fileName, 'public');

        // Save the compressed thumbnail version with a temporary name
        $tempThumbnailFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_thumbnail_temp.' . $extension;
       
        if (in_array($extension, $imagesExtensions)) {
        try {
            $image = Image::make($file);
            $image->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $tempThumbnailPath = storage_path('app/public/' . $directory . '/' . $tempThumbnailFileName);
            $image->save($tempThumbnailPath, 75); // 75 is the quality parameter

            // Set correct permissions for the temporary thumbnail
            chmod($tempThumbnailPath, 0775);


            // Rename the temporary thumbnail to the original file name
            $finalThumbnailPath = storage_path('app/public/' . $directory . '/' . $fileName);
            rename($tempThumbnailPath, $finalThumbnailPath);
   
            $response = Http::withHeaders([
                'User-Agent'      => 'laravel-app',
                'X-CEERTIF-KEY'   => '430324fb959d9a45790c03d7d4338c57',
                'X-CEERTIF-SECRET'=> '92ed7089d57110113239fb02750be52a',
                'Cookie'          => 'PHPSESSID=ck2ch79ith81tl4c8taqn9uoqm',
            ])->attach(
                'file',
                file_get_contents($finalThumbnailPath),
                $fileName
            )->asMultipart()->post('https://app.ceertif.com/api-v2/upload/upload.php', [
                'address'        => "90 chaussée de l'etang 94160 SAINT MANDE",
                'upload_time'    => now()->format('Y-m-d H:i:s'),
                'timestamp_type' => 'both',
                'callback_url'   => 'https://crm.genius-market.fr/server-callback',
                'opportunity_id' => $dossier->id,
            ]);
    
            if(auth()->user()->id==1) {
                return response()->json($response);
            }

            if (!$response->successful()) {
                throw new \Exception('Ceertif API error: ' . $response->body());
            }

 
        } catch (\Exception $e) {
            // If Intervention Image fails, log the error
            error_log("Thumbnail creation error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to create thumbnail'], 500);
        }
        }

        if(!isset($request->application) || $request->application==false) {
            $config=FormsConfig::where('name',$request->name)->first();
            $formId= $config->form_id ?? '';
    
            if($formId) {
                $update = FormsData::updateOrCreate(
                    [
                        'dossier_id' => $dossier->id,
                        'form_id' => $formId,
                        'meta_key' => $request->name
                    ],
                    [
                        'meta_value' => $filePath ?? null,
                    ]
                );
            }
        }





        $this->cardService->checkAndCreateCard($fileName, $filePath, $dossier->id, auth()->user()->id);


   
    
        return response()->json([
            'file_path' => $directory . '/' . $fileName,
      
        ], 200);
    }

    public function deleteFile(Request $request)
{
    // Check if dossier_id and name are present in the request
    if (!isset($request->dossier_id)) {
        return response()->json('dossier_id required', 200);
    }

    if (!isset($request->name)) {
        return response()->json('name required', 200);
    }

    // Fetch the folder associated with the dossier_id
    $dossier = Dossier::where('id', $request->dossier_id)->first();
    $folder = $dossier->folder ?? 'default';

    // File path to be deleted
    $directory = "dossiers/{$folder}";
    $fileName = $request->name;
    $filePath = $directory . '/' . $fileName;
    // Check if the file exists
    if (!Storage::disk('public')->exists($filePath)) {
        return response()->json('File not found', 404);
    }

    // Attempt to delete the file
    try {
        Storage::disk('public')->delete($filePath);
        return response()->json('File deleted successfully', 200);
    } catch (\Exception $e) {
        // Log the error if the deletion fails
        error_log("File deletion error: " . $e->getMessage());
        return response()->json(['error' => 'Failed to delete file'], 500);
    }
}

}
