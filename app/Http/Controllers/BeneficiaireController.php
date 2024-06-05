<?php 

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BeneficiaireController extends Controller
{
    public function index()
    {
        $beneficiaires = Beneficiaire::with('dossiers.fiche')->get();
        return view('beneficiaires.index', compact('beneficiaires'));
    }

    public function create()
    {
        $fiches = Fiche::all();
        $financiers = Client::where('type_client',1)->get();
        $administratifs = Client::where('type_client',2)->get();
        return view('beneficiaires.create',compact('fiches','financiers','administratifs'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|max:200',
            'prenom' => 'required|max:200',
            'adresse' => 'required|max:250',
            'cp' => 'required|max:10',
            'ville' => 'required|max:200',
            'telephone' => 'required|max:20',
            'telephone_2' => 'nullable|max:20',
            'email' => 'required|email|max:200',
            'menage_mpr' => 'required|in:bleu,jaune,violet,rose',
            'chauffage' => 'required|in:gaz,fioul,bois,charbon,electricite',
            'occupation' => 'required|in:locataire,proprietaire',
        ]);

        $beneficiaire = Beneficiaire::create($validated);

        if ($request->has('fiche_id')) {
            $dossier = Dossier::create([
                'beneficiaire_id' => $beneficiaire->id,  // This will be updated once the beneficiaire is created
                'fiche_id' => $request->input('fiche_id'),
                'etape_id' => 1,
                'status_id' => 1,
                'client_id' => $request->input('client_id') ?? 0,
                'mandataire_administratif' => $request->input('mandataire_administratif') ?? 0,
                'mandataire_financier' => $request->input('mandataire_financier') ?? 0,
            ]);
            $dossier_id = $dossier->id;
        }


        return response()->json(['success' => 'Beneficiaire created successfully.', 'beneficiaire' => $beneficiaire], 201);
    }

    public function show(Beneficiaire $beneficiaire)
    {
        return view('beneficiaires.show', compact('beneficiaire'));
    }

    public function edit(Beneficiaire $beneficiaire)
    {
        return view('beneficiaires.edit', compact('beneficiaire'));
    }

    public function update(Request $request, Beneficiaire $beneficiaire)
    {
        $validated = $request->validate([
            'nom' => 'required|max:200',
            'prenom' => 'required|max:200',
            'adresse' => 'required|max:250',
            'cp' => 'required|max:10',
            'ville' => 'required|max:200',
            'telephone' => 'required|max:20',
            'telephone_2' => 'nullable|max:20',
            'email' => 'required|email|max:200',
            'menage_mpr' => 'required|in:bleu,jaune,violet,rose',
            'chauffage' => 'required|in:gaz,fioul,bois,charbon,electricite',
            'occupation' => 'required|in:locataire,proprietaire',
        ]);
    
        $beneficiaire->update($validated);
        return redirect()->route('beneficiaires.index')->with('success', 'Beneficiaire mis à jour.');
    }

    public function destroy(Beneficiaire $beneficiaire): JsonResponse
    {
        $beneficiaire->delete();
        return response()->json(['success' => 'Beneficiaire deleted successfully.'], 200);
    }
}
