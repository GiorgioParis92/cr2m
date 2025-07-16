<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use App\Models\Beneficiaire;
use App\Models\FormsConfig;
use App\Models\FormsData;
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
    public function index(Request $request, $modelName)
    {
        $model = $this->getModelInstance($modelName);
        $query = $model::query();
   
        // Vérifier si la relation forms_data existe
        $relations = $this->getAllRelations($model);
        $hasFormsDataRelation = in_array('forms_data', $relations);

        // Charger la relation forms_data seulement si elle existe
        if ($hasFormsDataRelation) {
            $query->with('forms_data');
        }

        if(!(isset($request->relations) || $request->relations==true)) {
            // Eager load all relationships if any
            if (!empty($relations)) {
                $query->with($relations);
            }
        }

        if ($request->has('forms_data')) {
            if (!$hasFormsDataRelation) {
                return response()->json([
                    'success' => false,
                    'message' => 'La relation forms_data n\'existe pas pour ce modèle',
                    'error' => 'RELATION_ERROR'
                ], 422);
            }

            $formsData = json_decode($request->forms_data, true);
            
            if (!is_array($formsData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le champ forms_data doit être un tableau JSON valide',
                    'error' => 'FORMAT_ERROR'
                ], 422);
            } else {
                if (isset($formsData['meta_key'])) {
                    if (isset($formsData['meta_value']) && !empty($formsData['meta_value'])) {
                        // Si meta_value est défini et non vide, rechercher la valeur exacte
                        $query->whereHas('forms_data', function($q) use ($formsData) {
                            $q->where('meta_key', $formsData['meta_key'])
                              ->where('meta_value', $formsData['meta_value']);
                        });
                    } else {
                        // Si meta_value n'est pas défini ou est vide
                        $query->where(function($query) use ($formsData) {
                            $query->whereHas('forms_data', function($q) use ($formsData) {
                                $q->where('meta_key', $formsData['meta_key'])
                                  ->where(function($subQuery) {
                                      $subQuery->whereNull('meta_value')
                                              ->orWhere('meta_value', '');
                                  });
                            })
                            ->orWhereDoesntHave('forms_data', function($q) use ($formsData) {
                                $q->where('meta_key', $formsData['meta_key']);
                            });
                        });
                    }
                }
            }
        }
    

        if ($request->has('etapes')) {
            $etapesData = json_decode($request->etapes, true);
            
            if (!is_array($etapesData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le champ etapes doit être un tableau JSON valide',
                    'error' => 'FORMAT_ERROR'
                ], 422);
            }

            // Vérifier si etape_minimum est spécifié
            if (isset($etapesData['etape_minimum'])) {
                $query->where('etape_icon', '>=', $etapesData['etape_minimum']);
            } 
        }



        if ($request->has('etape_minimum')) {
   

            // Vérifier si etape_minimum est spécifié
     
                $query->where('etape_icon', '>=', $request->etape_minimum);
            
        }


        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if ($field != 'relations' && $field != 'forms_data' && $field != 'dossiers_data' && $field != 'etapes') {
                $query->where($field, $value);
            }
        }
    
        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }
    
        return response()->json($query->get());
    }
    
    /**
     * Get all relationship methods of a model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    private function getAllRelations($model)
    {
        $relations = [];
        $reflection = new \ReflectionClass($model);
 
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            // Skip methods not declared in this class
            // dump($method);
            if ($method->class != get_class($model)) {
                continue;
            }
    
            // Skip methods with parameters
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }
    
            try {
                // Invoke the method to check if it returns a Relation instance
                $returnValue = $method->invoke($model);
                if ($returnValue instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    $relations[] = $method->getName();
                }
            } catch (\Throwable $e) {
                // Ignore methods that cannot be invoked
            }
        }
        // dd($reflection);
        return $relations;
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

public function updateOrInsertValue(Request $request, $modelName)
{

    
    if(isset($request->form_id)) {
        $formId=$request->form_id;
    } else {
        $config=FormsConfig::where('name',$request->name)->first();
        $formId= $config->form_id;
    }

    try {
        $model = $this->getModelInstance($modelName);
    
        $update = $model->updateOrInsert(
            [
                'dossier_id' => $request->dossier_id,
                'form_id' => $formId,
                'meta_key' => $request->name
            ],
            [
                'meta_value' => $request->value ?? null,
                'updated_at' => now()
            ]
        );
    
 

        return response()->json(['message' => 'Operation successful'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}

