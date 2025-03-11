<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\Dossier;
use App\Models\ClientLinks;
use App\Models\Departement;
use App\Models\Status;
use App\Models\DossiersData;
use App\Models\FormsData;
use App\Models\Etape;
use App\Models\RdvStatus;
use App\Models\User;
use App\FormModel\FormConfigHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\CardCreationService;

class MailboxController extends Controller
{


    public function index()
    {


        return view('emails.emails');
    }


}
