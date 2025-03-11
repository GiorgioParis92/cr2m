<?php 

// FormsConfigController.php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\FormConfig;
use Illuminate\Support\Facades\Validator;

class FormsConfigController extends \App\Http\Controllers\Controller
{
    // Fetch all forms configurations with dynamic filtering
    public function index(Request $request)
    {
        $query = FormConfig::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['form_id', 'name', 'title', 'type', 'required', 'class', 'ordering'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific form config by ID
    public function show($id)
    {
        $formConfig = FormConfig::findOrFail($id);
        return response()->json($formConfig);
    }

    // Create a new form config
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_id' => 'required|integer',
            'name' => 'required|string|max:200',
            'title' => 'required|string|max:400',
            'type' => 'required|string|max:200',
            'required' => 'nullable|boolean',
            'options' => 'required|string',
            'class' => 'nullable|string|max:200',
            'ordering' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $formConfig = FormConfig::create($request->all());
        return response()->json($formConfig, 201);
    }

    // Update a form config
    public function update(Request $request, $id)
    {
        $formConfig = FormConfig::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'form_id' => 'required|integer',
            'name' => 'required|string|max:200',
            'title' => 'required|string|max:400',
            'type' => 'required|string|max:200',
            'required' => 'nullable|boolean',
            'options' => 'required|string',
            'class' => 'nullable|string|max:200',
            'ordering' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $formConfig->update($request->all());
        return response()->json($formConfig);
    }

    // Delete a form config
    public function destroy($id)
    {
        $formConfig = FormConfig::findOrFail($id);
        $formConfig->delete();
        return response()->json(null, 204);
    }
}



// routes/api.php

