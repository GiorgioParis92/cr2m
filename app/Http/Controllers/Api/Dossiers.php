<?php 

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Models\Dossier;
use Illuminate\Support\Facades\Validator;

class Dossiers extends \App\Http\Controllers\Controller
{
    // Fetch all dossiers with dynamic filtering
    public function index(Request $request)
    {
                $query = Dossier::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['folder', 'beneficiaire_id', 'reference_unique', 'client_id', 'fiche_id', 'etape_number', 'etape_id', 'status_id', 'mar', 'mandataire_financier', 'installateur', 'lat', 'lng'])) {
                $query->where($field, $value);
            }
        }

   

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch a specific dossier by ID
    public function show($id)
    {
        $dossier = Dossier::findOrFail($id);
        return response()->json($dossier);
    }

    // Create a new dossier
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folder' => 'required|string|max:200',
            'beneficiaire_id' => 'required|integer',
            'client_id' => 'required|integer',
            'fiche_id' => 'required|integer',
            'etape_number' => 'required|integer',
            'etape_id' => 'required|integer',
            'status_id' => 'required|integer',
            'mar' => 'nullable|integer',
            'mandataire_financier' => 'nullable|integer',
            'installateur' => 'nullable|integer',
            'lat' => 'nullable|string|max:200',
            'lng' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $dossier = Dossier::create($request->all());
        return response()->json($dossier, 201);
    }

    // Update a dossier
    public function update(Request $request, $id)
    {
        $dossier = Dossier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'folder' => 'required|string|max:200',
            'beneficiaire_id' => 'required|integer',
            'client_id' => 'required|integer',
            'fiche_id' => 'required|integer',
            'etape_number' => 'required|integer',
            'etape_id' => 'required|integer',
            'status_id' => 'required|integer',
            'mar' => 'nullable|integer',
            'mandataire_financier' => 'nullable|integer',
            'installateur' => 'nullable|integer',
            'lat' => 'nullable|string|max:200',
            'lng' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $dossier->update($request->all());
        return response()->json($dossier);
    }

    // Delete a dossier
    public function destroy($id)
    {
        $dossier = Dossier::findOrFail($id);
        $dossier->delete();
        return response()->json(null, 204);
    }
}



