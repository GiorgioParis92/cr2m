<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;

use Illuminate\Support\Facades\Schema; // Import Schema for checking columns

class RdvController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $rdvs = DB::table('rdv')
            ->leftJoin('users', function ($join) {
                $join->on('rdv.user_id', '=', 'users.id')
                    ->where('rdv.user_id', '>', 0);
            })
            ->select('rdv.*', DB::raw("COALESCE(users.name, 'non attribué') as user_name"));

        if (isset($request->user_id) && $request->user_id > 0) {
            $rdvs = $rdvs->where('rdv.user_id', $request->user_id);
        }
        if (isset($request->dpt) ) {
            $rdvs = $rdvs->where(DB::raw('substr(rdv.cp, 1, 2)'), $request->dpt);
        }
        if (isset($request->rdv_id) ) {
            $rdvs = $rdvs->where('rdv.id',$request->rdv_id);
        }
        $rdvs = $rdvs->get();

        $data = $rdvs->map(function ($rdv) {
            $rdv->color = getColorForType($rdv->type_rdv);

            if ($rdv->date_rdv) {
                $rdv->french_date = date('d/m/Y',strtotime($rdv->date_rdv));
                $rdv->hour = date('H',strtotime($rdv->date_rdv));
                $rdv->minute = date('i',strtotime($rdv->date_rdv));
            } else {
                $rdv->french_date = null;
                $rdv->hour = null;
                $rdv->minute = null;
            }
            return $rdv;
        });

        return response()->json($data);
    }


    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isset($request->dossier_id)) {
            $id = $request->dossier_id;
            $dossier = Dossier::where('id', $id)
                ->with('beneficiaire', 'fiche', 'etape', 'status')
                ->first();
            $date = date('Y-m-d H:i:s', strtotime($request->start));
            $user_id = $request->user_id ?? 0;
            $type_rdv = $request->type_rdv ?? 0;
            $client_id = $dossier->client_id;


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
            ->leftJoin('users', function ($join) {
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
        return response()->json(['success' => true, 'id' => $rdvId, 'rdv' => $rdvs]);
    }



    
    
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        // Assuming rdvId is passed as a route parameter or can be retrieved from the request in some way.
        
        $rdvId = $request->input('rdv_id');

        if(empty($request->date_rdv)) {
            return response()->json(['success' => false, 'message' => 'Entrez une date'], 404);

        }

        if ($rdvId == 0 || !isset($rdvId)) {
            // Insert a new record and get the id
            $rdvId = DB::table('rdv')->insertGetId([
                'date_rdv' => date('Y-m-d 00:00:00',strtotime(str_replace('/','-',$request->date_rdv))) ?? '2024-01-01 00:00:00', // Insert default values, adjust as needed
                'user_id' => $request->user_id ?? 0,
                'type_rdv' => $request->type_rdv ?? 1,
                'dossier_id' => $request->dossier_id ?? 0,
                'client_id' => $request->client_id ?? 0,
                'nom' => $request->nom ?? '',
                'prenom' => $request->prenom ?? '',
                'adresse' => $request->adresse ?? '',
                'cp' => $request->cp ?? '',
                'ville' => $request->ville ?? '',
                'telephone' => $request->telephone ?? '',
                'email' => $request->email ?? '',
                // Other necessary fields with default values
            ]);
        }
    
        // Fetch the existing record from the 'rdv' table
        $rdv = DB::table('rdv')->find($rdvId);
        if (!$rdv) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        // $rdvs = DB::table('rdv')->find($rdvId); // Fetch the existing record, you might need to adjust this based on your logic
    
        // if (!$rdvs) {
        //     return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        // }
    
        $updateData = [];
    
        foreach ($request->all() as $key => $value) {
            if($key=='date_rdv') {
                $value=date('Y-m-d', strtotime(str_replace('/','-', $value))).' '.$request->hour.':'.$request->minute.':00';

            }


            if (Schema::hasColumn('rdv', $key)) {
                $updateData[$key] = $value;
            }
        }
    
        if (!empty($updateData)) {
            DB::table('rdv')->where('id', $rdvId)->update($updateData);
            // Refetch updated record for response
            $rdvs = DB::table('rdv')->find($rdvId);
        } else {
            return response()->json(['success' => false, 'message' => 'No valid fields to update'], 400);
        }
    
        return response()->json(['success' => true, 'id' => $rdvId, 'rdv' => $rdvs]);
    }
}
