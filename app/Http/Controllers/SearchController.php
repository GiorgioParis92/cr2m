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
        $query = $request->input('query');

        $user = auth()->user();
        $client = Client::where('id', $user->client_id)->first();
        // Example of searching in different models


        $dossiers = Dossier::where('id', '>', 0);
        if (auth()->user()->client_id > 0) {
            $client = auth()->user()->client; // Assuming you have a client relationship
            if ($client->type_client == 1) {
                $dossiers = $dossiers->where('mar', auth()->user()->client_id)
                ;
            } elseif ($client->type_client == 2) {
                $dossiers = $dossiers->where('mandataire_financier', auth()->user()->client_id);
            } elseif ($client->type_client == 3) {
                $dossiers = $dossiers->where('installateur', auth()->user()->client_id);
            }
        }

        if ($query) {
            $dossiers = $dossiers->whereHas('beneficiaire', function ($q) use ($query) {
                $q->where('nom', 'LIKE', "%{$query}%")
                  ->orWhere('prenom', 'LIKE', "%{$query}%");
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
