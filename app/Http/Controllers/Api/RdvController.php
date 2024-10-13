<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Schema; // Import Schema for checking columns

class RdvController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $client = null;
    
        // Fetch client if client_id is provided
        if ($request->filled('client_id') && $request->client_id > 0) {
            $client = Client::find($request->client_id);
        }
    
        // Fetch user if user_id is provided
        if ($request->filled('user_id') && $request->user_id > 0) {
            $user = User::find($request->user_id);
        }
    
        // Initialize the RDV query
        $rdvs = DB::table('rdv')
            ->leftJoin('users', function ($join) {
                $join->on('rdv.user_id', '=', 'users.id')
                    ->where('rdv.user_id', '>', 0);
            })
            ->leftJoin('rdv_status', 'rdv.status', '=', 'rdv_status.id')
            ->leftJoin('rdv_type', 'rdv.type_rdv', '=', 'rdv_type.id')
            ->leftJoin('dossiers', 'rdv.dossier_id', '=', 'dossiers.id')
            ->select(
                'rdv.*',
                DB::raw("COALESCE(users.name, 'non attribué') as user_name"),
                'rdv_type.title as type_rdv_title',
                'rdv.status as rdv_status_id',
                'dossiers.folder as dossier_folder'
            );
    
        // Apply client-specific filters
        if (!is_null($client)) {
            if ($client->id > 0) {
                if ($client->type_client == 1) {
                    // Uncomment and adjust if needed
                    // $rdvs = $rdvs->where('dossiers.client_id', $client->id);
                } elseif ($client->type_client == 2) {
                    $rdvs = $rdvs->where('dossiers.mandataire_financier', $client->id);
                } elseif ($client->type_client == 3) {
                    $rdvs = $rdvs->where('dossiers.installateur', $client->id);
                }
            }
        }
    
        // Apply user-specific filters
        if ($request->filled('user_id') && $request->user_id > 0 && $user->type_id == 4) {
            $rdvs = $rdvs->where('rdv.user_id', $request->user_id);
        }
    
        // Filter by department (dpt)
        if ($request->filled('dpt')) {
            $rdvs = $rdvs->where(DB::raw('substr(rdv.cp, 1, 2)'), $request->dpt);
        }
        if ($request->filled('type_rdv') && $request->type_rdv > 0) {
            $rdvs = $rdvs->where('rdv.type_rdv', $request->type_rdv);
        }
        // Filter by specific RDV ID
        if ($request->filled('rdv_id')) {
            $rdvs = $rdvs->where('rdv.id', $request->rdv_id);
        }
    
        // Include date range filtering using $start and $end
        if ($request->filled('start') && $request->filled('end')) {
            $start = Carbon::parse($request->start)->toDateTimeString();
            $end = Carbon::parse($request->end)->toDateTimeString();
            $rdvs = $rdvs->whereBetween('rdv.date_rdv', [$start, $end]);
        }
    
        // Exclude RDVs with status 2
        $rdvs = $rdvs->where('rdv.status', '!=', 2);
    
        // Retrieve the RDVs
        $rdvs = $rdvs->get();
    
        // Collect unique dossier IDs
        $dossierIds = $rdvs->pluck('dossier_id')->filter()->unique();
    
        // Fetch all dossiers in one query
        $dossiers = Dossier::whereIn('id', $dossierIds)
            ->with('beneficiaire', 'fiche', 'etape', 'status', 'mandataire_financier', 'mar')
            ->get()
            ->keyBy('id');
    
        // Map RDVs with their dossiers and format dates
        $data = $rdvs->map(function ($rdv) use ($dossiers) {
            $rdv->color = stringToColorCode($rdv->user_name);
    
            if ($rdv->date_rdv) {
                $dateRdv = Carbon::parse($rdv->date_rdv);
                $rdv->french_date = $dateRdv->format('d/m/Y');
                $rdv->hour = $dateRdv->format('H');
                $rdv->minute = $dateRdv->format('i');
            } else {
                $rdv->french_date = null;
                $rdv->hour = null;
                $rdv->minute = null;
            }
    
            // Attach the dossier to the RDV
            if ($rdv->dossier_id) {
                $dossier = $dossiers->get($rdv->dossier_id);
                if ($dossier) {
                    $rdv->dossier = $dossier;
                }
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

            $datas=DB::table('dossiers_data')->where('dossier_id',$dossier->id)->get();
            foreach($datas as $data) {
                if(isset($data) && !empty($data->meta_value)) {
                    $dossier[$data->meta_key]=$data->meta_value;

                }
            }

        }
      


        $data = [
            'type_rdv' => $type_rdv,
            'user_id' => $user_id,
            'date_rdv' => $date,
            'client_id' => $client_id,
            'nom' => $dossier->beneficiaire->nom ?? '',
            'prenom' => $dossier->beneficiaire->prenom ?? '',
            'adresse' => ($dossier['numero_voie'] ?? '').''.$dossier->beneficiaire->adresse ?? '',
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
  
        if(isset($request->type_indispo)) {
            $request->type_rdv=$request->type_indispo;
        }
        

        if (empty($request->date_rdv)) {
            return response()->json(['success' => false, 'message' => 'Entrez une date'], 200);

        }

        if (isset($request->dossier_id)) {
            $id = $request->dossier_id;
            $dossier = Dossier::where('id', $id)
                ->with('beneficiaire', 'fiche', 'etape', 'status')
                ->first();
            $date = date('Y-m-d H:i:s', strtotime($request->start));
            $user_id = $request->user_id ?? 0;
            $type_rdv = $request->type_rdv ?? 0;
            $client_id = $dossier->client_id;

            $datas=DB::table('dossiers_data')->where('dossier_id',$dossier->id)->get();
            foreach($datas as $data) {
                if(isset($data) && !empty($data->meta_value)) {
                    $dossier[$data->meta_key]=$data->meta_value;

                }
            }

        }


        if(isset($request->date_fin)) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->date_rdv);
            $endDate = Carbon::createFromFormat('d/m/Y', $request->date_fin);
            
            // Create a CarbonPeriod to iterate from start date to end date
            $period = CarbonPeriod::create($startDate, $endDate);
        
            // Create an array to hold the dates in dd/mm/YYYY format
            $dates = [];
        
            // Loop through the CarbonPeriod and add formatted dates to the array
            foreach ($period as $date) {
                $dates[] = $date->format('d/m/Y');
            }
        } else {
            $dates[]=$request->date_rdv;
        }
        
        foreach($dates as $date) {

            $rdvId = $request->input('rdv_id');


        if ($rdvId == 0 || !isset($rdvId)) {
            // Insert a new record and get the id
            $rdvId = DB::table('rdv')->insertGetId([
                'date_rdv' => date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $date))) ?? '2024-01-01 00:00:00', // Insert default values, adjust as needed
                'user_id' => $request->user_id ?? 0,
                'type_rdv' => $request->type_rdv ?? 1,
                'dossier_id' => $request->dossier_id ?? 0,
                'client_id' => $request->client_id ?? 0,
                'nom' => $request->nom ?? '',
                'prenom' => $request->prenom ?? '',
                'adresse' => ($dossier['numero_voie'] ?? '').' '.$request->adresse ?? '',
                'cp' => $request->cp ?? '',
                'ville' => $request->ville ?? '',
                'telephone' => $request->telephone ?? '',
                'email' => $request->email ?? '',
                'status' => $request->status ?? 0,
                'observations' => $request->observations ?? '',
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
            // if ($key == 'date_rdv') {
            //     $value = date('Y-m-d', strtotime(str_replace('/', '-', $date))) . ' ' . $hour . ':' . $minute . ':00';
            // }
            if($key=='type_indispo')
            {
                $key='type_rdv';
            }
            if (isset($key) && Schema::hasColumn('rdv', $key) && $key!='date_rdv') {
        
                $updateData[$key] = $value;
      
            }
        }


        if(isset($request->type_indispo)) {

            if(isset($request['hour'])) {
                $hour=$request['hour'];
            } else {
                $hour='08';
            }

          
            $minute='00';
            $client_id=0;
        } else {
            $hour=$request->hour;
            $minute=$request->minute;  
            $duration=2; 
            $client_id=$request->client_id; 
        }

        if(isset($request['duration']) && !empty($request['duration'])) {
            $updateData['duration'] = $request['duration'];
        }
        $updateData['client_id'] = $client_id;
        $updateData['date_rdv']=date('Y-m-d', strtotime(str_replace('/', '-', $date))) . ' ' . $hour . ':' . $minute . ':00';
        if (!empty($updateData)) {
            DB::table('rdv')->where('id', $rdvId)->update($updateData);
            // Refetch updated record for response
            $rdvs = DB::table('rdv')->find($rdvId);
        } else {
            return response()->json(['success' => false, 'message' => 'No valid fields to update'], 400);
        }
    }
   
        return response()->json(['success' => true, 'id' => $rdvId, 'rdv' => $rdvs]);
    }
}
