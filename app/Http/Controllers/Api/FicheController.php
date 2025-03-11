<?php 

// FicheController.php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Fiche;
use Illuminate\Support\Facades\Validator;

class FicheController extends \App\Http\Controllers\Controller
{
    // Fetch all fiches with dynamic filtering
    public function index(Request $request)
    {
        $query = Fiche::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['fiche_name'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific fiche by ID
    public function show($id)
    {
        $fiche = Fiche::findOrFail($id);
        return response()->json($fiche);
    }

    // Create a new fiche
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fiche_name' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $fiche = Fiche::create($request->all());
        return response()->json($fiche, 201);
    }

    // Update a fiche
    public function update(Request $request, $id)
    {
        $fiche = Fiche::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fiche_name' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $fiche->update($request->all());
        return response()->json($fiche);
    }

    // Delete a fiche
    public function destroy($id)
    {
        $fiche = Fiche::findOrFail($id);
        $fiche->delete();
        return response()->json(null, 204);
    }
}


// routes/api.php

