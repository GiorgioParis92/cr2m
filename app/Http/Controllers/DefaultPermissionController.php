<?php

namespace App\Http\Controllers;

use App\Models\DefaultPermission;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\Client;
use App\Models\Etape;
use App\Models\ClientType;

class DefaultPermissionController extends Controller
{
    public function index(Request $request)
    {
        $permissions = DefaultPermission::with(['userType', 'clientType']);
        $etapes = Etape::all();
        $user_types = UserType::all();
  
        // Filter based on request parameters
        if ($request->filled('permission_name')) {
            $permissions->where('permission_name', 'like', '%' . $request->permission_name . '%');
        }
        if ($request->filled('type_id')) {
            $permissions->where('type_id', $request->type_id);
        }
        if ($request->filled('type_client')) {
            $permissions->where('type_client', $request->type_client);
        }
    
        $permissions = $permissions->orderBy('permission_name')->get();

        foreach($permissions as $permission) {
            $permission_array[$permission->permission_name][$permission->type_id][$permission->type_client]=$permission->is_active;
        }

        return view('permissions.index', compact('permissions','etapes','user_types'));
    }
    

    public function create()
    {
        $users = UserType::all();
        $clients = ClientType::all();
    
        return view('permissions.create', compact('users', 'clients'));
    }
    

    public function store(Request $request)
    {

        $data = $request->all();

        // Loop through each data element
        foreach ($data as $key => $value) {
            // If the value is null, set it to 0
            if (is_null($value)) {
                $data[$key] = 0;
            }
        }
      
    
        DefaultPermission::create($data);
    
        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }
    

    public function show(DefaultPermission $permission)
    {
        return view('permissions.show', compact('permission'));
    }

    public function edit(DefaultPermission $permission)
    {
        $users = UserType::all();
        $clients = ClientType::all();
    
   
        return view('permissions.edit', compact('permission', 'users', 'clients'));
    }
    

    public function update(Request $request, DefaultPermission $permission)
    {

        $data = $request->all();

        // Loop through each data element
        foreach ($data as $key => $value) {
            // If the value is null, set it to 0
            if (is_null($value)) {
                $data[$key] = 0;
            }
        }
        $permission->update($data);
    
        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }
    

    public function destroy(DefaultPermission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
