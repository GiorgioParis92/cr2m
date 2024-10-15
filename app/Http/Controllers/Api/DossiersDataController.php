<?php 
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DossiersData;
use Illuminate\Support\Facades\Validator;

class DossiersDataController extends \App\Http\Controllers\Controller
{
    // Fetch all dossiers data with dynamic filtering
    public function index(Request $request)
    {
        $query = DossiersData::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['dossier_id', 'meta_key', 'meta_value', 'user_id'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific dossier data by ID
    public function show($id)
    {
        $dossierData = DossierData::findOrFail($id);
        return response()->json($dossierData);
    }

    // Create a new dossier data entry
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dossier_id' => 'required|integer',
            'meta_key' => 'required|string|max:200',
            'meta_value' => 'required|string',
            'user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $dossierData = DossiersData::create($request->all());
        return response()->json($dossierData, 201);
    }

    // Update dossier data
    public function update(Request $request, $id)
    {
        $dossierData = DossiersData::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'dossier_id' => 'required|integer',
            'meta_key' => 'required|string|max:200',
            'meta_value' => 'required|string',
            'user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $dossierData->update($request->all());
        return response()->json($dossierData);
    }

    // Delete dossier data
    public function destroy($id)
    {
        $dossierData = DossiersData::findOrFail($id);
        $dossierData->delete();
        return response()->json(null, 204);
    }
}



