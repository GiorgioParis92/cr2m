<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Client;
use App\Models\User;

use Illuminate\Support\Facades\Schema; // Import Schema for checking columns

class DossiersController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if($request->filter=='false') {
            return response()->json('no_filter');

        }

        $client = null;
        if ($request->client_id && $request->client_id > 0) {
            $client = Client::where('id', $request->client_id)->first();
        }

        if ($request->user_id && $request->user_id > 0) {
            $user = User::where('id', $request->user_id)->first();
        }

        if ($request->start) {
            $start = date('Y-m-d 00:00:00',strtotime(str_replace('/','-',$request->start)));
        } else {
            $start=date('1970-01-01 00:00:00');
        }
        if ($request->end) {
            $end = date('Y-m-d 23:59:59',strtotime(str_replace('/','-',$request->end)));
        } else {
            $end=date('Y-m-d 23:59:59',strtotime('now'));
        }
        
        $dossiers = Dossier::where('id', '>', 0);



        if ($request->status || $request->start || $request->end) {
            if ($request->status == -1) {
                // Get all dossiers that don't have any rdv
                $dossiers = $dossiers->whereDoesntHave('get_rdv');
            } else {
                // Get dossiers where rdv status matches the request status
                $dossiers = $dossiers->whereHas('get_rdv', function ($query) use ($request,$start,$end) {
                    $query->where('status', $request->status)
                    ->where('date_rdv','>=',$start)
                    ->where('date_rdv','<=',$end)
                    ;
                });
                
            }

        }


        if ($request->dossier_status) {
   
                // Get dossiers where rdv status matches the request status
                $dossiers = $dossiers->where('status_id', $request->dossier_status);
        
            
        }

        if ($request->installateur) {
            if ($request->installateur == -1) {
                // Get all dossiers that don't have any rdv
                $dossiers = $dossiers->whereDoesntHave('installateur');
            } else {
                $dossiers = $dossiers->whereHas('installateur', function ($query) use ($request) {
                    $query->where('id', $request->installateur);
                });
            }
        }
        if ($request->mar) {
            if ($request->mar == -1) {
                // Get all dossiers that don't have any rdv
                $dossiers = $dossiers->whereDoesntHave('mar');
            } else {
                $dossiers = $dossiers->whereHas('mar', function ($query) use ($request) {
                    $query->where('id', $request->mar);
                });
            }
        }

        if ($request->mandataire_financier) {
            if ($request->mandataire_financier == -1) {
                $dossiers = $dossiers->where(function ($query) {
                    $query->whereDoesntHave('mandataire_financier')
                        ->orWhere('mandataire_financier', 0);
                });
            } else {
                $dossiers = $dossiers->whereHas('mandataire_financier', function ($query) use ($request) {
                    $query->where('id', $request->mandataire_financier);
                });
            }
        }

        if ($request->search && strlen($request->search) > 2) {
            $dossiers = $dossiers->whereHas('beneficiaire', function ($query) use ($request) {
                $searchTerm = '%' . $request->search . '%';
                $query->where('nom', 'like', $searchTerm)
                    ->orWhere('prenom', 'like', $searchTerm)
                    ->orWhere('adresse', 'like', $searchTerm)
                    ->orWhere('cp', 'like', $searchTerm)
                    ->orWhere('ville', 'like', $searchTerm);
            });
        }

        $dossiers = $dossiers->with([
            'beneficiaire',
            'installateur',
            'mar',
            'mandataire_financier',
            'fiche',
            'etape',
            'status',
            'get_rdv' => function ($query) {
                $query->with('status');
            }
        ]);

        $dossiers = $dossiers->get();

        // foreach ($dossiers as $dossier) {

        //     $dossier->mar = $dossier->mar_client;
        //     $mandataireFinancierClient = $dossier->mandataire_financier_client; // Access the mandataire_financier client
        //     $installateur = $dossier->installateur_client; // Access the installateur client

        //     $dossier->mandataire_financier = $mandataireFinancierClient;
        //     if ($dossier->installateur_client) {
        //         $dossier->installateur = $installateur;
        //     }
        // }




        return response()->json($dossiers);
    }


}
