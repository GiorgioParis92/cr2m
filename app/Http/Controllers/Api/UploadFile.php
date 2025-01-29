<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Beneficiaire;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Intervention\Image\Facades\Image; // Use Intervention Image
use App\Services\CardCreationService;

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
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf', 'heic', 'webp'];
        $imagesExtensions = ['jpeg', 'jpg', 'png', 'gif', 'heic', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            return response()->json('Invalid file type', 400);
        }

        $directory = "dossiers/{$folder}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $fileName = $request->name;
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
   
            // Delete the original uploaded file
            // Storage::disk('public')->delete($filePath);
        } catch (\Exception $e) {
            // If Intervention Image fails, log the error
            error_log("Thumbnail creation error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to create thumbnail'], 500);
        }
        }


        $this->cardService->checkAndCreateCard($fileName, $filePath, $dossier->id, auth()->user()->id);

        return response()->json([
            'file_path' => $directory . '/' . $fileName
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
