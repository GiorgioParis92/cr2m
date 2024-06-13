<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;


class RdvController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
       
        $rdvs = DB::table('rdv')
        ->leftJoin('users', function($join) {
            $join->on('rdv.user_id', '=', 'users.id')
                 ->where('rdv.user_id', '>', 0);
        })
        ->select('rdv.*', DB::raw("COALESCE(users.name, 'non attribué') as user_name"));
    
    if (isset($request->user_id) && $request->user_id > 0) {
        $rdvs = $rdvs->where('rdv.user_id', $request->user_id);
    }
    
    
    $rdvs = $rdvs->get();

        $data=$rdvs;

        return response()->json($data);
    }


    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
       if(isset($request->dossier_id)) {
        $id=$request->dossier_id;
        $dossier = Dossier::where('id', $id)
        ->with('beneficiaire', 'fiche', 'etape', 'status')
        ->first();
        $date=date('Y-m-d H:i:s',strtotime($request->start));
        $user_id=$request->user_id ?? 0;
        $type_rdv=$request->type_rdv ?? 0;
        $client_id=$dossier->client_id;
       
      
       }
        $data = [
            'type_rdv' => $type_rdv,
            'user_id' => $user_id,
            'date_rdv' => $date,
            'client_id' => $client_id,
            'nom' => $dossier->beneficiaire->nom ?? '',
            'prenom' => $dossier->beneficiaire->prenom ?? '',
            'adresse' => $dossier->beneficiaire->adresse ?? '',
            'cp' => $dossier->beneficiaire->cp ?? '',
            'ville' => $dossier->beneficiaire->ville ?? '',
            'telephone' => $dossier->beneficiaire->telephone ?? '',
            'telephone_2' => $dossier->beneficiaire->telephone_2 ?? '',
            'email' => $dossier->beneficiaire->email ?? '',
            'lat' => $dossier->beneficiaire->lat ?? '',
            'lng' => $dossier->beneficiaire->lng ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    
        // Insert the data into the database
        $rdvId = DB::table('rdv')->insertGetId($data);
        $rdvs = DB::table('rdv')
        ->leftJoin('users', function($join) {
            $join->on('rdv.user_id', '=', 'users.id')
                 ->where('rdv.user_id', '>', 0);
        })
        ->select('rdv.*', DB::raw("COALESCE(users.name, 'non attribué') as user_name"));
    
    if (isset($request->user_id) && $request->user_id > 0) {
        $rdvs = $rdvs->where('rdv.user_id', $request->user_id);
    }
    $rdvs = $rdvs->where('rdv.id', $rdvId);

    $rdvs = $rdvs->first();
        // Return a JSON response
        return response()->json(['success' => true, 'id' => $rdvId,'rdv' => $rdvs]);
    }
}
