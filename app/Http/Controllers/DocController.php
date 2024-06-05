<?php 

namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\Campagne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
}
