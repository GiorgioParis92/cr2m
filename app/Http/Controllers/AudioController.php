<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Dossier;
use App\Models\FormsData;
use Illuminate\Support\Facades\Http; // Add this line
use PDF; // This alias is typically registered by barryvdh/laravel-dompdf
use Illuminate\Support\Str;
class AudioController extends Controller
{
    public function store(Request $request)
    {
        if ($request->hasFile('audio')) {

            $dossier = Dossier::where('id', $request->dossier_id)->first();

            $directory = "dossiers/{$dossier->folder}";


            $file = $request->file('audio');
            $filename = $request->name.'_' . time() . '.wav';
            // $path = $file->storeAs('public/audio', $filename);
            $path = $file->storeAs($directory, $filename, 'public');

            $update = FormsData::updateOrCreate(
                [
                    'dossier_id' => $dossier->id,
                    'form_id' => $request->form_id,
                    'meta_key' => $request->name
                ],
                [
                    'meta_value' => $path
                ]
            );

            return response()->json([
                'message' => 'Audio saved successfully!',
                'file_path' => $path,
            ]);
        }

        return response()->json(['message' => 'No audio file received'], 400);
    }

    public function analyse(Request $request)
    {

    
        // 1. Extract audio path from the request
        $audioPath = $request->value; // example path: "recordings/audio1.wav"
        $absolutePath = storage_path('app/public/' . $audioPath);
        $dossier = Dossier::where('id', $request->dossier_id)->first();

        // 2. Check if the file actually exists
        if (!file_exists($absolutePath)) {
        
            dd($absolutePath);

            try {
    

                    $oceerResult = $this->sendPdfToOceer($absolutePath,$request->api_link);


                    if ($oceerResult) {
                        if (
                            isset($oceerResult['data']['results']['data']['identification_results']['results'])
                        ) {
                            // On récupère la partie qui nous intéresse
                            $oceerResult = $oceerResult['data']['results']['data']['identification_results'];
                    
                            // 1. Nettoyer $request->name (suppression du dernier segment)
                            $segments = explode('.', $request->name);
                            array_pop($segments);
                            $request->name = implode('.', $segments);
                    
                            // 2. Mettre à jour l'ID dans le tableau $results
                            $results = $oceerResult['results'];
                            foreach ($results as $key => $result) {
                                // Construire le nouvel ID
                                $newId = ($request->name ? $request->name . '.' : '') . $result['id'];
                                $results[$key]['id'] = $newId;
                    
                                // Exemple de filtrage et update en base
                                if ($result['score'] >= 0.8 && $result['value'] != '') {
                                    FormsData::updateOrCreate(
                                        [
                                            'dossier_id' => $dossier->id,
                                            'form_id'    => $request->form_id,
                                            'meta_key'   => $newId // On utilise maintenant $newId
                                        ],
                                        [
                                            'meta_value' => $result['value']
                                        ]
                                    );
                                }
                            }
                    
                            // 3. Réaffecter les résultats mis à jour dans $oceerResult
                            $oceerResult['results'] = $results;
                        }
                    }
           

                    return response()->json([
                        'message'       => 'Audio transcription successful.',
                        'transcription' => $transcription ?? '',
                        'oceer_result'  => $oceerResult ?? '',
                        'request-name'  => $request->name ?? '',
                        'api_link'  => $request->api_link ?? '',
                    ]);



               
               
            }



         catch (\Exception $e) {

        }
    }
}


    public function sendPdfToOceer(string $pdfPath,$api_link)
    {

        
        $result = [
            'success' => false,
            'data'    => null,
            'error'   => null,
        ];
        // Make sure the file exists before sending:
        if (!file_exists($pdfPath)) {
            // You might log an error or throw an exception.
            // We'll quietly return here for demonstration:
            return;
        }

        // Build your headers:
        $headers = [
            'User-Agent'    => 'insomnia/10.2.0',
            'api-key'       => 'i1XmSNfkueLu3AE',
        ];

        $api_link=$api_link ?? 'https://app.oceer.fr/api/pipeline/start/229bdbbf-869a-49c9-83cb-ae069f1137ff';
        // Make the POST request using Laravel's HTTP Client:
        $response = Http::withHeaders($headers)
            ->attach(
                'audio',
                file_get_contents($pdfPath), // file content
                basename($pdfPath)           // filename
            )
            ->post($api_link);
           
            if ($response->successful()) {
                $result['success'] = true;
                $result['data']    = $response->json(); // or the entire response object
            } else {
                $result['error'] = $response->json() ?: 'Failed to send PDF to Oceer.';
            }
    
            return $result;
     
    }

    public function show() {
        return view('audio.show');

    }
}
