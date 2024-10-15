<?php 

// RdvTypeController.php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\RdvType;
use Illuminate\Support\Facades\Validator;

class RdvTypeController extends \App\Http\Controllers\Controller
{
    // Fetch all RDV types with dynamic filtering
    public function index(Request $request)
    {
        $query = RdvType::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['title'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific RDV type by ID
    public function show($id)
    {
        $rdvType = RdvType::findOrFail($id);
        return response()->json($rdvType);
    }

    // Create a new RDV type
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $rdvType = RdvType::create($request->all());
        return response()->json($rdvType, 201);
    }

    // Update RDV type
    public function update(Request $request, $id)
    {
        $rdvType = RdvType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $rdvType->update($request->all());
        return response()->json($rdvType);
    }

    // Delete RDV type
    public function destroy($id)
    {
        $rdvType = RdvType::findOrFail($id);
        $rdvType->delete();
        return response()->json(null, 204);
    }
}

// routes/api.php

