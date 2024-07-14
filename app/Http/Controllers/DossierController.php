<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\Dossier;
use App\Models\Etape;
use App\Models\Status;
use App\Models\RdvStatus;
use App\Models\User;
use App\FormModel\FormConfigHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DossierController extends Controller
{

    public $forms_configs = [];

    public function index()
    {


        $user = auth()->user();
        $client = Client::where('id', $user->client_id)->first();


        $dossiers = Dossier::where('id', '>', 0);

        if (auth()->user()->client_id > 0 && ($client->type_client == 1)) {
            $dossiers = $dossiers->where('client_id', auth()->user()->client_id);
        }
        if (auth()->user()->client_id > 0 && ($client->type_client == 2)) {
            $dossiers = $dossiers->where('mandataire_financier', auth()->user()->client_id);
        }
        if (auth()->user()->client_id > 0 && ($client->type_client == 3)) {
            $dossiers = $dossiers->where('installateur', auth()->user()->client_id);
        }
        $dossiers = $dossiers->with('beneficiaire', 'fiche', 'etape', 'status');
        $dossiers = $dossiers->get();

        foreach ($dossiers as $dossier) {
            $dossier->mar = $dossier->mar_client;
            $mandataireFinancierClient = $dossier->mandataire_financier_client; // Access the mandataire_financier client
            $installateur = $dossier->installateur_client; // Access the installateur client

            $dossier->mandataire_financier = $mandataireFinancierClient;
            if ($dossier->installateur_client) {
                $dossier->installateur = $installateur;
            }
        }

        $etapes = Etape::orderBy('order_column')->get() ;
        $mars = Client::where('type_client', 1)->get();
        $financiers = Client::where('type_client', 2)->get();
        $installateurs = Client::where('type_client', 3)->get();
        $fiches = Fiche::all();

        $status = Status::select(DB::raw('MIN(id) as id'), 'status_desc')
            ->groupBy('status_desc')
            ->get();

        return view('dossiers.index', compact('dossiers', 'etapes', 'status', 'mars', 'financiers', 'fiches', 'installateurs'));
    }

    public function show($id)
    {

        $dossier_by_folder = Dossier::where('folder', $id)
        ->with('beneficiaire', 'fiche', 'etape', 'status')
        ->first();

        if(! $dossier_by_folder) {
            if(auth()->user()->client_id==0) {
                $dossier = Dossier::where('id', $id)
                ->with('beneficiaire', 'fiche', 'etape', 'status')
                ->first();
            }
 
        } else {
            $dossier = Dossier::where('id', $dossier_by_folder->id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first(); 
        }
        if(isset($dossier->id)) {
            $id= $dossier->id;
        }
        
        if(!isset($dossier)) {
            abort(404);
        }

        $auditeurs=User::where('type_id',4)->get();
        $rdv_status=RdvStatus::all();

        return view('dossiers.show', compact('id','auditeurs','dossier','rdv_status'));
    }
    public function save_form(Request $request)
    {


        $cached_forms_configs = session('forms_configs', []);

        if (!array_key_exists($request->dossier_id, $cached_forms_configs)) {
            $cached_forms_configs[$request->dossier_id] = [];
        }
        $this->forms_configs = $cached_forms_configs[$request->dossier_id];

        foreach ($request->all() as $key => $data) {
         
            if ($key != "_token" && $key != "form_id" && $key != "dossier_id"  && $key != "etape_id") {
                $this->forms_configs[$request->form_id]->formData[$key]->value = $data;

            }
        }

        $result_save = $this->forms_configs[$request->form_id]->save();
        
        return redirect()->route('dossiers.show', ['id' => $request->dossier_id]);
    }
    public function next_step($id)
    {

        $dossier = Dossier::where('id', $id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first();
       
            $current=DB::table('etapes')->where('id', $dossier->etape_number)->first();
        
            $current_plus=(($current->order_column)+1);
           

        $next=DB::table('etapes')->where('order_column', ($current->order_column+1))->first();
        
        if($next) {
            $next_etape = $next->id;

       
            Dossier::where('id', $id)->update(['etape_number' => $next_etape]);
        }

          
        return redirect()->route('dossiers.show', ['id' => $dossier->folder]);

    }
}
