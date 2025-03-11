<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Client;
use App\Models\User;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Schema; // Import Schema for checking columns

class VRP extends Controller
{
    public function getRdvFromApi(Request $request)
    {
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
        ->select('rdv.*', DB::raw("COALESCE(users.name, 'non attribué') as user_name"), DB::raw("rdv_status.id as status"), DB::raw("dossiers.folder as dossier_folder"));



    if (isset($request->user_id) && $request->user_id > 0) {
        $rdvs = $rdvs->where('rdv.user_id', $request->user_id);
    }
   

    if (isset($request->start_date) && isset($request->end_date)) {
        $rdvs = $rdvs->whereDate('rdv.date_rdv', '>=', $request->start_date)
        ->whereDate('rdv.date_rdv', '<=', $request->end_date);}

    $rdvs = $rdvs->get();
 
        return response()->json($rdvs);
    
    }

    public function getInspectorRdv($inspector_id, $date)
    {
        $parameters = [
            'user_id' => $inspector_id,
            'start_date' => $date,
            'end_date' => $date,
        ];

        $response = $this->getRdvFromApi(new Request($parameters));

        if ($response->getStatusCode() !== 200 || empty($response->getData())) {
            return null;
        }

        return $response->getData();
    }

    public function convertRdvDataVrp(Request $request)
    {
        $final_data = [
            "address_to_visit" => [],
            "inspectors" => [],
            "forced_visits" => new \stdClass()
        ];
    
        $date = $request->input('dateTime');
    
        $inspectorSelect = $request->input('inspectorSelect');
        $inspectorSelect = json_decode($inspectorSelect);
    
        foreach ($inspectorSelect as $inspector_id) {
            if ($inspector_id !== 'auto') {
                $position = $this->getInspectorPosition($inspector_id, $date);
                $final_data["inspectors"][$inspector_id] = $position;
    
                $inspector_rdv = $this->getInspectorRdv($inspector_id, $date);
                if ($inspector_rdv !== null) {
                    foreach ($inspector_rdv as $rdv) {
                        $final_data["address_to_visit"][] = [
                            "loc" => $rdv->adresse . " " . $rdv->ville . " " . $rdv->cp,
                            "time" => $rdv->date_rdv,
                            "duration" => "1:00"
                        ];
                        $rdv_index = count($final_data["address_to_visit"]) - 1;
                        $final_data["forced_visits"]->$rdv_index = $inspector_id;
                    }
                }
            }
        }
    
        return response()->json($final_data);
    }
    

    private function getDayBefore($dateString)
    {
        $date = new \DateTime($dateString);
        $date->modify('-1 day');

        return [
            'date' => $date->format('Y-m-d'),
            'dayIndex' => $date->format('w')
        ];
    }

    private function getLastRdvOfTheDay($rdvArray)
    {
        if (empty($rdvArray)) {
            return null;
        }

        usort($rdvArray, function ($a, $b) {
            return new \DateTime($a->date_rdv) <=> new \DateTime($b->date_rdv);
        });

        return end($rdvArray);
    }

    public function getInspectorPosition($inspector_id, $date)
    {
        $inspector_address = "28 Rue de Solférino 92100 Boulogne-Billancourt";
        $date_info = $this->getDayBefore($date);

        if ($date_info['dayIndex'] == 0) {
            return $inspector_address;
        }

        $parameters = [
            'user_id' => $inspector_id,
            'start_date' => $date_info["date"],
            'end_date' => $date_info["date"],
        ];

        $response = $this->getRdvFromApi(new Request($parameters));

        if ($response->getStatusCode() !== 200 || empty($response->getData())) {
            return $inspector_address;
        }

        $lastRdv = $this->getLastRdvOfTheDay($response->getData());

        return $lastRdv->adresse . " " . $lastRdv->ville . " " . $lastRdv->cp;
    }

    public function index(Request $request)
    {


        $formData = $request->validate([
            'dateTime' => 'required|string',
            'inspectorSelect' => 'required|string',
            'rdvTime' => 'nullable|string',
            'startingHourMin' => 'required|string',
            'startingHourMax' => 'required|string',
            'hoursMinForUnknown' => 'required|string',
            'hoursMaxForUnknown' => 'required|string',
            'hoursDividerAddForUnknown' => 'required|string',
            'timeWindowSize' => 'required|string',
            'maxIterationToSolve' => 'required|string'
        ]);

        if (empty($formData['dateTime'])) {
            return response()->json(['error' => 'Date time is invalid'], 400);
        }
       
        $converted_data = $this->convertRdvDataVrp($request);

        return response()->json($converted_data);
    }
}
