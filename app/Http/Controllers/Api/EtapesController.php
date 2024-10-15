<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Etape;
use Illuminate\Support\Facades\Validator;

class EtapesController extends \App\Http\Controllers\Controller
{
    // Fetch all etapes with dynamic filtering
    public function index(Request $request)
    {
        $query = Etape::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['etape_name', 'etape_desc', 'etape_style', 'order_column', 'etape_icon'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific etape by ID
    public function show($id)
    {
        $etape = Etape::findOrFail($id);
        return response()->json($etape);
    }

    // Create a new etape
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'etape_name' => 'required|string|max:100',
            'etape_desc' => 'required|string|max:100',
            'etape_style' => 'nullable|string|max:100',
            'order_column' => 'required|integer',
            'etape_icon' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $etape = Etape::create($request->all());
        return response()->json($etape, 201);
    }

    // Update an etape
    public function update(Request $request, $id)
    {
        $etape = Etape::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'etape_name' => 'required|string|max:100',
            'etape_desc' => 'required|string|max:100',
            'etape_style' => 'nullable|string|max:100',
            'order_column' => 'required|integer',
            'etape_icon' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $etape->update($request->all());
        return response()->json($etape);
    }

    // Delete an etape
    public function destroy($id)
    {
        $etape = Etape::findOrFail($id);
        $etape->delete();
        return response()->json(null, 204);
    }
}


// routes/api.php

