<?php 
// app/Http/Controllers/CalendarController.php
namespace App\Http\Controllers;

use Livewire\Component;
use App\Models\Dossier;
use App\Models\Etape;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\Rdv;
use App\Models\User;
use App\FormModel\FormConfigHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class RdvController extends Controller
{
    
    public function index()
    {


        $rdvs=Rdv::with('status')->get();

  


        $auditeurs=User::where('type_id',4);
        

        if(auth()->user()->client_id>0) {
            // $auditeurs=$auditeurs->where('client_id',auth()->user()->client_id);
        }

        $auditeurs=$auditeurs->get();
        $departments = DB::table('departement')->get()->map(function($department) {
            return (array) $department; // Convert stdClass to array
        })->toArray();

        return view('rdv.planning',compact('auditeurs','departments'));
    }


    public function rdvs()
    {
        $user = auth()->user();
        $apiToken = $user->api_token;

        $rdvs=Rdv::with('status')->get();

  
        $status=DB::table('rdv_status')->get();
        $dossier_status=DB::table('status')->where('etape_id',2)->orderBy('status_desc')->get();

        $auditeurs=User::where('type_id',4);
        

        if(auth()->user()->client_id>0) {
            // $auditeurs=$auditeurs->where('client_id',auth()->user()->client_id);
        }

        $auditeurs=$auditeurs->get();
        $departments = DB::table('departement')->get()->map(function($department) {
            return (array) $department; // Convert stdClass to array
        })->toArray();


        $etapes = Etape::orderBy('order_column')->get() ;
        $mars = Client::where('type_client', 1)->get();
        $financiers = Client::where('type_client', 2)->get();
        $installateurs = Client::where('type_client', 3)->get();
        $fiches = Fiche::all();

        return view('rdv.index',compact('auditeurs','departments','apiToken','status','dossier_status','etapes','mars','financiers','installateurs','fiches'));
    }


}
