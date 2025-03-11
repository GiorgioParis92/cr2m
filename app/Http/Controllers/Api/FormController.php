<?php 

// FormController.php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Validator;

class FormController extends \App\Http\Controllers\Controller
{
    // Fetch all forms with dynamic filtering
    public function index(Request $request)
    {
        $query = Form::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['fiche_id', 'etape_id', 'etape_number', 'version_id', 'form_title', 'type'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific form by ID
    public function show($id)
    {
        $form = Form::findOrFail($id);
        return response()->json($form);
    }

    // Create a new form
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fiche_id' => 'required|integer',
            'etape_id' => 'required|integer',
            'etape_number' => 'required|integer',
            'version_id' => 'required|integer',
            'form_title' => 'required|string|max:200',
            'type' => 'required|in:form,document,rdv,conversation',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $form = Form::create($request->all());
        return response()->json($form, 201);
    }

    // Update a form
    public function update(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fiche_id' => 'required|integer',
            'etape_id' => 'required|integer',
            'etape_number' => 'required|integer',
            'version_id' => 'required|integer',
            'form_title' => 'required|string|max:200',
            'type' => 'required|in:form,document,rdv,conversation',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $form->update($request->all());
        return response()->json($form);
    }

    // Delete a form
    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        $form->delete();
        return response()->json(null, 204);
    }
}


// routes/api.php

