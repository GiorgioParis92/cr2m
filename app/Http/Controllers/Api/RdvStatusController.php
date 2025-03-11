<?php 

// RdvStatusController.php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\RdvStatus;
use Illuminate\Support\Facades\Validator;

class RdvStatusController extends \App\Http\Controllers\Controller
{
    // Fetch all RDV statuses with dynamic filtering
    public function index(Request $request)
    {
        $query = RdvStatus::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['rdv_desc', 'rdv_style'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific RDV status by ID
    public function show($id)
    {
        $rdvStatus = RdvStatus::findOrFail($id);
        return response()->json($rdvStatus);
    }

    // Create a new RDV status
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rdv_desc' => 'required|string|max:200',
            'rdv_style' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $rdvStatus = RdvStatus::create($request->all());
        return response()->json($rdvStatus, 201);
    }

    // Update RDV status
    public function update(Request $request, $id)
    {
        $rdvStatus = RdvStatus::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'rdv_desc' => 'required|string|max:200',
            'rdv_style' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $rdvStatus->update($request->all());
        return response()->json($rdvStatus);
    }

    // Delete RDV status
    public function destroy($id)
    {
        $rdvStatus = RdvStatus::findOrFail($id);
        $rdvStatus->delete();
        return response()->json(null, 204);
    }
}

// routes/api.php

