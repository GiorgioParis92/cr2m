<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\Dossier;
use App\Models\Etape;
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
        $dossiers = Dossier::where('id', '>', 0);

        if (auth()->user()->client_id > 0) {
            $dossiers = $dossiers->where('client_id', auth()->user()->client_id);
        }

        $dossiers = $dossiers->with('beneficiaire', 'fiche', 'etape', 'status');
        $dossiers = $dossiers->get();

        $etapes = Etape::all();



        return view('dossiers.index', compact('dossiers', 'etapes'));
    }

    public function show($id)
    {


        $dossier = Dossier::where('id', $id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first();

        $etapes = Etape::all();

        $forms = DB::table('forms')->where('fiche_id', $dossier->fiche_id)->get();
        $this->forms_configs = [];
        foreach ($forms as $form) {
            $this->forms_configs[$form->id] = new FormConfigHandler($dossier, $form);
        }
        session()->forget('forms_configs');
        $cached_forms_configs = session('forms_configs', []);

        if (!in_array($id, $cached_forms_configs)) {
            $cached_forms_configs[$id] = [];
        }

        $cached_forms_configs[$id] = $this->forms_configs;

        session(['forms_configs' => $cached_forms_configs]);

        $forms_configs = $this->forms_configs;



        $distinctEtapes = DB::table('forms')
            ->select('etape_id', DB::raw('MIN(id) as min_id'))
            ->groupBy('etape_id');

        $etapes = DB::table('forms')
            ->join('etapes', 'forms.etape_id', '=', 'etapes.id')
            ->joinSub($distinctEtapes, 'distinctEtapes', function ($join) {
                $join->on('forms.id', '=', 'distinctEtapes.min_id');
            })
            ->select('forms.*', 'etapes.etape_name', 'etapes.etape_desc')
            ->orderBy('forms.etape_number')
            ->get();


        return view('dossiers.show', compact('dossier', 'etapes', 'forms', 'forms_configs'));
    }
    public function save_form(Request $request)
    {


        $cached_forms_configs = session('forms_configs', []);

        if (!array_key_exists($request->dossier_id, $cached_forms_configs)) {
            $cached_forms_configs[$request->dossier_id] = [];
        }
        $this->forms_configs = $cached_forms_configs[$request->dossier_id];

        foreach ($request->all() as $key => $data) {
            if ($key != "_token" && $key != "form_id" && $key != "dossier_id") {
                $this->forms_configs[$request->form_id]->formData[$key]->value = $data;
            }
        }

        $result_save = $this->forms_configs[$request->form_id]->save();



        return redirect()->route('dossiers.show', ['id' => $request->dossier_id])->with('result', json_encode($result_save));

    }
    public function next_step($id)
    {

        $dossier = Dossier::where('id', $id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first();
       
        $distinctEtapes = DB::table('forms')
            ->select('etape_id', DB::raw('MIN(id) as min_id'))
            ->groupBy('etape_id');

        $etapes = DB::table('forms')
            ->join('etapes', 'forms.etape_id', '=', 'etapes.id')
            ->joinSub($distinctEtapes, 'distinctEtapes', function ($join) {
                $join->on('forms.id', '=', 'distinctEtapes.min_id');
            })
            ->select('forms.*', 'etapes.etape_name', 'etapes.etape_desc')
            ->orderBy('forms.etape_number','desc')
            ->first();
    
        $next_etape = $dossier->etape_number + 1;
        if ( ($next_etape <= $etapes->etape_number)) {
            Dossier::where('id',$id)->update(['etape_number'=>$next_etape]);
        }
        return redirect()->route('dossiers.show', ['id' => $id]);

    }
}
