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

    // Fetch all records with relations
    public function index($modelName)
    {
        $model = $this->getModelInstance($modelName);

        // Get the relationships defined in the model
        $relations = $this->getModelRelations($model);

        // Fetch records with the relations
        $records = $model::with($relations)->get();

        return view("index", compact('records', 'modelName'));
    }

    // Helper method to get relations from a model
    protected function getModelRelations($model)
    {
        $relations = [];
   
        // Use Reflection to get the methods defined in the model
        $methods = (new \ReflectionClass($model))->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            // Skip inherited methods
            if ($method->class !== get_class($model)) {
                continue;
            }

            // Ensure the method returns a relationship
            $returnType = $method->getReturnType();
            if (!$returnType || !is_a($returnType->getName(), 'Illuminate\Database\Eloquent\Relations\Relation', true)) {
                continue;
            }

            // Add the method name (relation) to the list
            $relations[] = $method->name;
        }

        return $relations;
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

  

        $record = $model::create($request->all());
        return response()->json($record, 201);
    }

    // Update a record
    public function update(Request $request, $modelName, $id)
    {
        $model = $this->getModelInstance($modelName);
        $record = $model::findOrFail($id);


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


public function updateOrInsert(Request $request, $modelName)
{

    // Validate if the necessary fields are present
    $this->validate($request, [
        'conditions' => 'required|string',
        'update_data' => 'required|string',
    ]);

    // Retrieve conditions and update data from the request
    $conditions = json_decode($request->input('conditions'),true); // Array of conditions for the 'where' clause
    $updateData = json_decode($request->input('update_data'),true); // Array of columns and their new values

    $updateData['updated_at'] = now();
    if (!isset($updateData['created_at'])) {
        $updateData['created_at'] = now();
    }

    // Perform updateOrInsert operation on the specified table
    try {
        $model = $this->getModelInstance($modelName);

        $update = $model->updateOrInsert($conditions, $updateData);

        return response()->json(['message' => 'Operation successful'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}

