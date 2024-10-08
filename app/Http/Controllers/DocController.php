<?php 

namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\Campagne;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocController extends Controller
{
    public function upload(Request $request, $campagne_id)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB Max
        ]);

        $file = $request->file('file');
        $path = $file->store('docs', 'public');

        $doc = new Doc();
        $doc->doc_title = $file->getClientOriginalName();
        $doc->doc_type = $file->getClientOriginalExtension();
        $doc->doc_name = $path;
        $doc->campagne_id = $campagne_id;
        $doc->save();

        return response()->json(['path' => $path, 'doc_id' => $doc->doc_id], 200);
    }

    public function downloadAllDocs($dossier_id)
    {
        // Fetch the documents for the given dossier_id
        $documentPaths = $this->fetchDocumentPaths($dossier_id);
        $dossier = Dossier::with('beneficiaire', 'fiche', 'etape', 'status','mar_client')->find($dossier_id);

        if (empty($documentPaths)) {
            return back()->withErrors(['No documents available for download.']);
        }
    
        // Create a new ZipArchive instance
        $zip = new \ZipArchive();
        $zipFileName =  $dossier->beneficiaire->nom .' '.$dossier->beneficiaire->prenom .  ' Documents.zip';
    
        // Create the zip file in the system's temporary directory
        $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
    
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($documentPaths as $filePath) {
                if (file_exists($filePath)) {
                    // Add the file to the zip archive
                    $zip->addFile($filePath, basename($filePath));
                }
            }
            $zip->close();
        } else {
            return back()->withErrors(['Could not create zip file.']);
        }
    
        // Return the zip file as a download response
        return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
    }
    

    private function fetchDocumentPaths($dossier_id)
    {
        // Replicate the logic to fetch document paths
        $results = DB::table('forms_config')
            ->leftJoin('forms_data', function ($join) use ($dossier_id) {
                $join->on('forms_config.name', '=', 'forms_data.meta_key')
                     ->where('forms_data.dossier_id', $dossier_id);
            })
            ->join('forms', 'forms.id', '=', 'forms_config.form_id')
            ->join('etapes', 'etapes.id', '=', 'forms.etape_id')
            ->whereIn('forms_config.type', ['generate', 'fillable', 'upload', 'generateConfig'])
            ->whereIn('forms_config.id', function ($query) {
                $query->select(DB::raw('MIN(id)'))
                      ->from('forms_config')
                      ->groupBy('name');
            })
            ->orderBy('etapes.order_column')
            ->get();
    
        $documentPaths = [];
    
        foreach ($results as $doc) {
            if (isset($doc->meta_value) && !empty($doc->meta_value)) {
                $documentPaths[] = storage_path('app/public/' . $doc->meta_value);
            }
        }
    
        return $documentPaths;
    }
    

}
