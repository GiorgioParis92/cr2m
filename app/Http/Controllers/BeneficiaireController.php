<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\User;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http; 
use Illuminate\Http\RedirectResponse;

class BeneficiaireController extends Controller
{
    public function index()
    {
        $beneficiaires = Beneficiaire::with('dossiers.fiche')->get();
        return view('beneficiaires.index', compact('beneficiaires'));
    }

    public function create()
    {
        $user = User::where('id', auth()->user()->id)->with('client')->first();
        $fiches = Fiche::all();
        $financiers = Client::where('type_client', 1)->get();
        $administratifs = Client::where('type_client', 2)->get();
        $installateurs = Client::where('type_client', 3)->get();
        return view('beneficiaires.create', compact('fiches', 'financiers', 'administratifs', 'installateurs', 'user'));
    }

    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|max:200',
            'prenom' => 'required|max:200',
            'numero_voie' => 'required|max:250',
            'adresse' => 'required|max:250',
            'cp' => 'required|max:10',
            'ville' => 'required|max:200',
            'telephone' => 'required|max:20',
            'telephone_2' => 'nullable|max:20',
            'email' => 'required|email|max:200',
            'menage_mpr' => '',
            'chauffage' => '',
            'occupation' => '',
            'installateur' => '',
            'lat' => '',
            'lng' => '',
        ]);
        $validated['lat'] = 0;
        $validated['lng'] = 0;
        $new_lat = 0;
        $new_lng = 0;
       
        
        $fullAddress = urlencode("{{$validated['cp']}+{$validated['ville']} France");
        $nominatimUrl = "https://nominatim.openstreetmap.org/search?q={$fullAddress}&format=geojson";
        // Send the request to Nominatim API
        try {
            $response = Http::withOptions(['verify' => false])->get($nominatimUrl);

            if ($response->successful()) {

                $geoData = $response->json();

                $latitude = $geoData['features'][0]['geometry']['coordinates'][1] ?? null;
                $longitude = $geoData['features'][0]['geometry']['coordinates'][0] ?? null;

                if($latitude!=0) {
                    $new_lat = $latitude;
                    $new_lng = $longitude;
                }
            }
        
        } catch (\Exception $e) {

        }
      

        $fullAddress = urlencode("{{$validated['adresse']}+{$validated['cp']}+{$validated['ville']}+France");
        $nominatimUrl = "https://nominatim.openstreetmap.org/search?q={$fullAddress}&format=geojson";



        // Send the request to Nominatim API
        try {
            $response = Http::withOptions(['verify' => false])->get($nominatimUrl);
         

            if ($response->successful()) {
                $geoData = $response->json();
                
                $latitude = $geoData['features'][0]['geometry']['coordinates'][1] ?? null;
                $longitude = $geoData['features'][0]['geometry']['coordinates'][0] ?? null;
             

                if($latitude!=0) {
                    $new_lat = $latitude;
                    $new_lng = $longitude;
                }

            }
        
        } catch (\Exception $e) {

        }
      
        // Concatenate and encode the full address
        $fullAddress = urlencode("{$validated['numero_voie']}+{$validated['adresse']}+{$validated['cp']}+{$validated['ville']}+France");
        $nominatimUrl = "https://nominatim.openstreetmap.org/search?q={$fullAddress}&format=geojson";


        // Send the request to Nominatim API
        try {
            $response = Http::withOptions(['verify' => false])->get($nominatimUrl);
       

            if ($response->successful()) {
                $geoData = $response->json();
                $latitude = $geoData['features'][0]['geometry']['coordinates'][1] ?? null;
                $longitude = $geoData['features'][0]['geometry']['coordinates'][0] ?? null;

                if($latitude!=0) {
                    $new_lat = $latitude;
                    $new_lng = $longitude;
                }
            }
        
        } catch (\Exception $e) {

        }
     
        $validated['lat']=$new_lat;
        $validated['lng']=$new_lng;
    
        // Create the beneficiaire
        $beneficiaire = Beneficiaire::create($validated);
   

        // Create the dossier if fiche_id is provided
        if ($request->has('fiche_id')) {
            $dossier = Dossier::create([
                'beneficiaire_id' => $beneficiaire->id,
                'fiche_id' => $request->input('fiche_id'),
                'folder' => generateRandomString(),
                'etape_id' => 1,
                'status_id' => 1,
                'client_id' => $request->input('mar') ?? 0,
                'mandataire_financier' => $request->input('mandataire_financier') ?? 0,
                'mar' => $request->input('mar') ?? 0,
                'installateur' => $request->input('installateur') ?? 0,
                'lat' => $validated['lat'] ?? 0,
                'lng' => $validated['lng'] ?? 0,
            ]);
    
            foreach($validated as $key=>$value) {
                \DB::table('dossiers_data')->updateOrInsert(
                    [
                        'dossier_id' => $dossier->id,
                        'meta_key' => $key
                    ],
                    [
                        'meta_value' => $value ?? '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            // Ensure dossier is created before redirecting
            if ($dossier) {
                return redirect()->route('dossiers.show', ['id' => $dossier->folder])
                    ->with('success', 'Beneficiaire et dossier créés avec succès.');
            } else {
                // Handle failure to create dossier
                return redirect()->back()->with('error', 'Failed to create dossier.');
            }
        }
    
        // Fallback if no dossier is created
        return redirect()->back()->with('success', 'Beneficiaire created successfully, but no dossier was created.');
    }
    

    public function show($id)
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
