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

        // 2. Check if the file actually exists
        if (!file_exists($absolutePath)) {
            return response()->json([
                'message' => 'Audio file not found.',
            ], 404);
        }

        // 3. Retrieve API key
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'message' => 'OpenAI API key not set.',
            ], 500);
        }

        try {
            // 4. Send request to OpenAI Whisper
            $response = Http::withToken($apiKey)
                ->attach(
                    'file',
                    file_get_contents($absolutePath),
                    basename($absolutePath)
                )
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1',
                    // 'prompt' => 'Custom prompt if needed',
                    // 'language' => 'fr' // if you know the language
                ]);

            if ($response->failed()) {
                return response()->json([
                    'message' => 'OpenAI Whisper API request failed.',
                    'error'   => $response->json(),
                ], $response->status());
            }

            // 5. Extract the transcription text from OpenAI's response
            $result         = $response->json();
            $transcription  = $result['text'] ?? '';

            // 6. (Optional) Check if transcription is not empty; if so, generate a PDF
            if (!empty($transcription)) {
                // Give the PDF a name that matches the audio filename but with .pdf extension
                



                $htmlContent='';
                $htmlContent='### CONTEXTE ####';

                $htmlContent.='Voici la retranscription audio de l\'inspection : ';
                $htmlContent.='### FIN DU CONTEXTE ####';
       // Map of the table key to the corresponding model class
       $models = [
        // 'type_combles'             => \App\Models\TypeCombles::class,
        // 'composition_mur'          => \App\Models\TypeCompositionsMurs::class,
        // 'type_epaisseur_vitrage'   => \App\Models\TypeEpaisseurVitre::class,
        // 'type_fenetre'             => \App\Models\TypeFenetres::class,
        // 'orientation_facade'       => \App\Models\TypeOrientations::class,
        // 'type_ouverture'           => \App\Models\TypeOuverture::class,
        'type_piece'               => \App\Models\TypePieces::class,
        // 'type_portes'              => \App\Models\TypePortes::class,
        // 'type_radiateur'           => \App\Models\TypeRadiateurs::class,
        // 'type_vitrage'             => \App\Models\TypeVitrage::class,
        // 'type_vmc'                 => \App\Models\TypeVmc::class,
    ];


    // foreach ($models as $key => $modelClass) {
    //     // Retrieve all entries from the corresponding model
    //     $records = $modelClass::all();

    //     // Build a list like "Name : ID , Name2 : ID2 , ..."
    //     $formattedValues = $records->map(function ($record) {
    //         // Replace 'name' with the actual attribute you want to show
    //         return "{$record->nom_piece} : {$record->id}";
    //     })->implode(' , ');

    //     // Append the formatted string to $htmlContent
    //     // Adjust brackets/braces if you want a different format
    //     $htmlContent .= "Valeurs à renvoyer pour {$key} : [ {$formattedValues} ]<br>";
    // }

                // $htmlContent.='### FIN DU CONTEXTE ####';
              
                $htmlContent .= $transcription;

                // (b) Load your HTML content
                $pdf = Pdf::loadHTML($htmlContent)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    // marges en millimètres (ou en points selon la config DomPDF)
                    'margin-top'    => 20,
                    'margin-right'  => 20,
                    'margin-bottom' => 20,
                    'margin-left'   => 20,
                ]);
 
                $dossier = Dossier::where('id', $request->dossier_id)->first();

                $directory = "dossiers/{$dossier->folder}";
    
    
          
                $pdfName = $request->name.'_pdf_' . time() . '.pdf';
                $pdfPath = storage_path('app/public/dossiers/'.$dossier->folder.'/' . $pdfName);
                $pdf->save($pdfPath);
                
        
                if($pdf) {
                    $update = FormsData::updateOrCreate(
                        [
                            'dossier_id' => $dossier->id,
                            'form_id' => $request->form_id,
                            'meta_key' => $request->name.'_pdf'
                        ],
                        [
                            'meta_value' => 'dossiers/'.$dossier->folder.'/'.$pdfName
                        ]
                    );


                    $oceerResult = $this->sendPdfToOceer($pdfPath,$request->api_link);


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
           

                }



               
               
            }

            return response()->json([
                'message'       => 'Audio transcription successful.',
                'transcription' => $transcription ?? '',
                'oceer_result'  => $oceerResult ?? '',
                'request-name'  => $request->name ?? '',
                'api_link'  => $request->api_link ?? '',
            ]);

        } catch (\Exception $e) {
            // 8. Catch any exception and return a JSON error
            return response()->json([
                'message' => 'Exception when contacting OpenAI Whisper API.',
                'error'   => $e->getMessage(),
            ], 500);
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
                'document',
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
