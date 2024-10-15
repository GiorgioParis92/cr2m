<?php 

// FormDataController.php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\FormData;
use Illuminate\Support\Facades\Validator;

class FormsDataController extends \App\Http\Controllers\Controller
{
    // Fetch all forms data with dynamic filtering
    public function index(Request $request)
    {
        $query = FormData::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['dossier_id', 'form_id', 'meta_key', 'meta_value'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific form data by ID
    public function show($id)
    {
        $formData = FormData::findOrFail($id);
        return response()->json($formData);
    }

    // Create a new form data entry
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dossier_id' => 'required|integer',
            'form_id' => 'required|integer',
            'meta_key' => 'required|string|max:200',
            'meta_value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $formData = FormData::create($request->all());
        return response()->json($formData, 201);
    }

    // Update a form data entry
    public function update(Request $request, $id)
    {
        $formData = FormData::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'dossier_id' => 'required|integer',
            'form_id' => 'required|integer',
            'meta_key' => 'required|string|max:200',
            'meta_value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $formData->update($request->all());
        return response()->json($formData);
    }

    // Delete a form data entry
    public function destroy($id)
    {
        $formData = FormData::findOrFail($id);
        $formData->delete();
        return response()->json(null, 204);
    }
}



