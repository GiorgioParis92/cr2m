<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Beneficiaire;
use Illuminate\Support\Facades\Validator;

class BeneficiaireController extends \App\Http\Controllers\Controller
{
    // Fetch all beneficiaires with dynamic filtering
    public function index(Request $request)
    {
        $query = Beneficiaire::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['nom', 'prenom', 'numero_voie', 'adresse', 'cp', 'ville', 'telephone', 'telephone_2', 'email', 'menage_mpr', 'chauffage', 'occupation', 'lat', 'lng'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch a specific beneficiaire by ID
    public function show($id)
    {
        $beneficiaire = Beneficiaire::findOrFail($id);
        return response()->json($beneficiaire);
    }

    // Create a new beneficiaire
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:200',
            'prenom' => 'required|string|max:200',
            'adresse' => 'required|string|max:250',
            'cp' => 'required|string|max:10',
            'ville' => 'required|string|max:200',
            'telephone' => 'required|string|max:20',
            'email' => 'required|string|email|max:200',
            'menage_mpr' => 'nullable|in:bleu,jaune,violet,rose',
            'chauffage' => 'nullable|in:gaz,fioul,bois,charbon,electricite',
            'occupation' => 'nullable|string|max:200',
            'lat' => 'nullable|string|max:200',
            'lng' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $beneficiaire = Beneficiaire::create($request->all());
        return response()->json($beneficiaire, 201);
    }

    // Update a beneficiaire
    public function update(Request $request, $id)
    {
        $beneficiaire = Beneficiaire::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:200',
            'prenom' => 'required|string|max:200',
            'adresse' => 'required|string|max:250',
            'cp' => 'required|string|max:10',
            'ville' => 'required|string|max:200',
            'telephone' => 'required|string|max:20',
            'email' => 'required|string|email|max:200',
            'menage_mpr' => 'nullable|in:bleu,jaune,violet,rose',
            'chauffage' => 'nullable|in:gaz,fioul,bois,charbon,electricite',
            'occupation' => 'nullable|string|max:200',
            'lat' => 'nullable|string|max:200',
            'lng' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $beneficiaire->update($request->all());
        return response()->json($beneficiaire);
    }

    // Delete a beneficiaire
    public function destroy($id)
    {
        $beneficiaire = Beneficiaire::findOrFail($id);
        $beneficiaire->delete();
        return response()->json(null, 204);
    }
}


