<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\Dossier;
use App\Models\Etape;
use App\Models\Status;
use App\Models\User;
use App\FormModel\FormConfigHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EtapesController extends Controller
{

 

    public function show()
    {
        if(auth()->user()->client_id>0){
            abort('403');
        }
        return view('etapes.show');
    }

    public function edit($id)
    {
        if(auth()->user()->client_id>0){
            abort('403');
        }

        return view('etapes.edit',compact('id'));
    }

}