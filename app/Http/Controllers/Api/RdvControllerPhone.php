<?php 

// RdvController.php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Rdv;
use Illuminate\Support\Facades\Validator;

class RdvControllerPhone extends \App\Http\Controllers\Controller
{
    // Fetch all RDVs with dynamic filtering
    public function index(Request $request)
    {
        $query = Rdv::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['type_rdv', 'status', 'dossier_id', 'user_id', 'client_id', 'nom', 'prenom', 'ville'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific RDV by ID
    public function show($id)
    {
        $rdv = Rdv::findOrFail($id);
        return response()->json($rdv);
    }

    // Create a new RDV
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_rdv' => 'required|integer',
            'duration' => 'nullable|numeric',
            'status' => 'nullable|integer',
            'dossier_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'date_rdv' => 'required|date',
            'client_id' => 'nullable|integer',
            'nom' => 'nullable|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:200',
            'cp' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:250',
            'telephone' => 'nullable|string|max:20',
            'telephone_2' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:100',
            'observations' => 'nullable|string',
            'lat' => 'nullable|string|max:250',
            'lng' => 'nullable|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $rdv = Rdv::create($request->all());
        return response()->json($rdv, 201);
    }

    // Update an RDV
    public function update(Request $request, $id)
    {
        $rdv = Rdv::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type_rdv' => 'required|integer',
            'duration' => 'nullable|numeric',
            'status' => 'nullable|integer',
            'dossier_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'date_rdv' => 'required|date',
            'client_id' => 'nullable|integer',
            'nom' => 'nullable|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:200',
            'cp' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:250',
            'telephone' => 'nullable|string|max:20',
            'telephone_2' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:100',
            'observations' => 'nullable|string',
            'lat' => 'nullable|string|max:250',
            'lng' => 'nullable|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $rdv->update($request->all());
        return response()->json($rdv);
    }

    // Delete an RDV
    public function destroy($id)
    {
        $rdv = Rdv::findOrFail($id);
        $rdv->delete();
        return response()->json(null, 204);
    }
}

// routes/api.php

