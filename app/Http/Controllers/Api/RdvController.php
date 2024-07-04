<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Client;

use Illuminate\Support\Facades\Schema; // Import Schema for checking columns

class RdvController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $user = auth()->user();
        $client='';
        if ($request->client) {
            $client = Client::where('id', $request->client_id)->first();
        }


        $rdvs = DB::table('rdv')
            ->leftJoin('users', function ($join) {
                $join->on('rdv.user_id', '=', 'users.id')
                    ->where('rdv.user_id', '>', 0);
            })
            ->leftJoin('rdv_status', function ($join) {
                $join->on('rdv.status', '=', 'rdv_status.id');
            })
            ->leftJoin('dossiers', function ($join) {
                $join->on('rdv.dossier_id', '=', 'dossiers.id');
            })
            ->select('rdv.*', DB::raw("COALESCE(users.name, 'non attribué') as user_name"), DB::raw("rdv_status.id as status"));

        if (isset($client) && !empty($client)) {
            if ($client->id > 0 && ($client->type_client == 1)) {
                $rdvs = $rdvs->where('dossiers.client_id', $client->id);
            }
            if ($client->id > 0 && ($client->type_client == 2)) {
                $rdvs = $rdvs->where('dossiers.mandataire_financier', $client->id);
            }
            if ($client->id > 0 && ($client->type_client == 3)) {
                $rdvs = $rdvs->where('dossiers.installateur', $client->id);
            }
        }


        if (isset($request->user_id) && $request->user_id > 0) {
            $rdvs = $rdvs->where('rdv.user_id', $request->user_id);
        }
        if (isset($request->dpt)) {
            $rdvs = $rdvs->where(DB::raw('substr(rdv.cp, 1, 2)'), $request->dpt);
        }
        if (isset($request->rdv_id)) {
            $rdvs = $rdvs->where('rdv.id', $request->rdv_id);
        }
        $rdvs = $rdvs->get();

        $data = $rdvs->map(function ($rdv) {
            $rdv->color = stringToColorCode($rdv->user_name);

            if ($rdv->date_rdv) {
                $rdv->french_date = date('d/m/Y', strtotime($rdv->date_rdv));
                $rdv->hour = date('H', strtotime($rdv->date_rdv));
                $rdv->minute = date('i', strtotime($rdv->date_rdv));
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

        if ($type_rdv == 1) {
            DB::table('dossiers_data')->updateOrInsert(
                [
                    'dossier_id' => $dossier->id,
                    'meta_key' => 'date_1ere_visite'
                ],
                [
                    'meta_value' => date('d/m/Y', strtotime($request->start)) ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            DB::table('forms_data')
                ->where([
                    ['dossier_id', '=', $dossier->id],
                    ['meta_key', '=', 'date_1ere_visite']
                ])
                ->update([
                    'meta_value' => date('d/m/Y', strtotime($request->start)) ?? null,
                    'updated_at' => now()
                ]);
        }
        // Return a JSON response
        return response()->json(['success' => true, 'id' => $rdvId, 'rdv' => $rdvs]);
    }





    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        // Assuming rdvId is passed as a route parameter or can be retrieved from the request in some way.

        $rdvId = $request->input('rdv_id');

        if (empty($request->date_rdv)) {
            return response()->json(['success' => false, 'message' => 'Entrez une date'], 200);

        }

        if ($rdvId == 0 || !isset($rdvId)) {
            // Insert a new record and get the id
            $rdvId = DB::table('rdv')->insertGetId([
                'date_rdv' => date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $request->date_rdv))) ?? '2024-01-01 00:00:00', // Insert default values, adjust as needed
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
                'status' => $request->status ?? '',
                // Other necessary fields with default values
            ]);
        }

        // Fetch the existing record from the 'rdv' table
        $rdv = DB::table('rdv')->find($rdvId);
        if (!$rdv) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }



        if ($request->type_rdv == 1) {
            DB::table('dossiers_data')->updateOrInsert(
                [
                    'dossier_id' => $request->dossier_id,
                    'meta_key' => 'date_1ere_visite'
                ],
                [
                    'meta_value' => date('d/m/Y', strtotime(str_replace('/', '-', $request->date_rdv))) ?? '2024-01-01 00:00:00',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );


            DB::table('forms_data')
                ->where([
                    ['dossier_id', '=', $request->dossier_id],
                    ['meta_key', '=', 'date_1ere_visite']
                ])
                ->update([
                    'meta_value' => date('d/m/Y', strtotime(str_replace('/', '-', $request->date_rdv))) ?? '2024-01-01 00:00:00',
                    'updated_at' => now()
                ]);
        }
        $updateData = [];

        foreach ($request->all() as $key => $value) {
            if ($key == 'date_rdv') {
                $value = date('Y-m-d', strtotime(str_replace('/', '-', $value))) . ' ' . $request->hour . ':' . $request->minute . ':00';

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
