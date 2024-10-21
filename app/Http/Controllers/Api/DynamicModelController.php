<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class DynamicModelController extends \App\Http\Controllers\Controller
{
    // Resolve the model name dynamically
    protected function getModelInstance($modelName)
    {
  
        $modelClass = 'App\\Models\\' . Str::studly($modelName);
        if (!class_exists($modelClass)) {
            throw new ModelNotFoundException("Model $modelName not found.");
        }

        return new $modelClass;
    }

    // Fetch all records with dynamic filtering
    public function index(Request $request, $modelName)
    {

        
        $model = $this->getModelInstance($modelName);
        $query = $model::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if ($model->isFillable($field)) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch specific record by ID
    public function show($modelName, $id)
    {
        $model = $this->getModelInstance($modelName);
        $record = $model::findOrFail($id);
        return response()->json($record);
    }

    // Create a new record
    public function store(Request $request, $modelName)
    {
        $model = $this->getModelInstance($modelName);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100', // Customize validation as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $record = $model::create($request->all());
        return response()->json($record, 201);
    }

    // Update a record
    public function update(Request $request, $modelName, $id)
    {
        $model = $this->getModelInstance($modelName);
        $record = $model::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100', // Customize validation as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $record->update($request->all());
        return response()->json($record);
    }

    // Delete a record
    public function destroy($modelName, $id)
    {
        $model = $this->getModelInstance($modelName);
        $record = $model::findOrFail($id);
        $record->delete();
        return response()->json(null, 204);
    }
}

