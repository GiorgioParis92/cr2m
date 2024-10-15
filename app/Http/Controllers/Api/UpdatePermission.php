<?php 


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\DefaultPermission;

class UpdatePermission extends \App\Http\Controllers\Controller
{
    // Fetch all RDV statuses with dynamic filtering
    public function index(Request $request)
    {

        $permission = DefaultPermission::updateOrCreate(
            [
                'permission_name' => $request->name,
                'type_id' => $request->type_user,
                'type_client' => $request->type_client,
            ],
            [
                'is_active' => $request->value,
            ]
        );


        return response()->json('ok');
    }

}

// routes/api.php

