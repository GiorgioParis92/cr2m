<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Client;
use App\Models\User;

use Illuminate\Support\Facades\Schema; // Import Schema for checking columns

class VRP extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

     
  
        return response()->json(['success' => true]);
    }
}
