<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\User;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http; 
use Illuminate\Http\RedirectResponse;

class MapController extends Controller
{
    public function index()
    {
        return view('map');
    }

}
