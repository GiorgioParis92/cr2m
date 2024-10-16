<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;

class ClientController extends \App\Http\Controllers\Controller
{
    // Fetch all beneficiaires with dynamic filtering
    public function index(Request $request)
    {
        $query = Client::query();

        // Apply filters dynamically based on request
        foreach ($request->all() as $field => $value) {
            if (in_array($field, ['id', 'client_title',  'adresse', 'cp', 'ville', 'telephone',  'email', 'siret'])) {
                $query->where($field, $value);
            }
        }

        // Apply pagination or return all results
        if ($request->has('paginate')) {
            return response()->json($query->paginate($request->input('paginate')));
        }

        return response()->json($query->get());
    }

    // Fetch a specific beneficiaire by ID
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    // Create a new beneficiaire
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_title' => 'required|string|max:200',
  
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $client = Client::create($request->all());
        return response()->json($client, 201);
    }

    // Update a beneficiaire
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_title' => 'required|string|max:200',
           
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $client->update($request->all());
        return response()->json($client);
    }

    // Delete a beneficiaire
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(null, 204);
    }
}


