<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Include your models here
use App\Models\Dossier;
use App\Models\Message;
use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
class SearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->input('query');
        $user = auth()->user();
        $client = Client::where('id', $user->client_id)->first();
    
        $dossiers = Dossier::where('id', '>', 0)
            ->where(function($q) {
                $q->whereNull('annulation')
                  ->orWhere('annulation', 0);
            });
    
        if ($user->client_id > 0) {
            if ($client->type_client == 1) {
                $dossiers->where('mar', $user->client_id);
            } elseif ($client->type_client == 2) {
                $dossiers->where('mandataire_financier', $user->client_id);
            } elseif ($client->type_client == 3) {
                $dossiers->where('installateur', $user->client_id);
            }
        }
    
        if ($searchTerm) {
            $dossiers->whereHas('beneficiaire', function ($q) use ($searchTerm) {
                $q->where('nom', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('prenom', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('telephone_2', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('telephone', 'LIKE', "%{$searchTerm}%");
            });
        }
    
        $dossiers = $dossiers->with('beneficiaire', 'fiche', 'etape', 'status')->get();
    
        $dossiers->each(function ($dossier) {
            $dossier->url = route('dossiers.show', $dossier->folder);
        });
    
        $results = [
            'dossiers' => $dossiers,
        ];
    
        if ($request->ajax()) {
            return response()->json($results);
        }
    
        return view('search.results', compact('results'));
    }
    
}
