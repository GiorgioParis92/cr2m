<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Beneficiaire;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class UploadFile extends \App\Http\Controllers\Controller
{
    // Fetch all beneficiaires with dynamic filtering
    public function index(Request $request)
    {
        if (!isset($request->dossier_id)) {
            return response()->json('dossier_id required',200);

        } else {
            $dossier=Dossier::where('id',$request->dossier_id)->first();
            $folder=$dossier->folder;
        }
        if (!isset($request->name)) {
            return response()->json('name required',200);

        }
        if (!isset($request->file)) {
            return response()->json('file required',200);

        }
        $file = $request->file('file');
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf', 'heic', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());



        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        $directory = "dossiers/{$folder}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Directory path where files will be stored
        $directoryPath = storage_path('app/public/' . $directory);
        $fileName = $request->name;

        $filePath = $file->storeAs($directory, $fileName, 'public');
        return response()->json($filePath);

    }
}


