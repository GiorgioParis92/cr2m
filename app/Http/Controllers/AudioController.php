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
                
   
                $htmlContent = "
                 
                    <p>" . e($transcription) . "</p>
                ";

                // (b) Load your HTML content
                $pdf = PDF::loadHTML($htmlContent);

 
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
                    $oceerResult = $this->sendPdfToOceer($pdfPath);


                    if($oceerResult) {

                        if (
                            isset($oceerResult['data']['results']['data']['identification_results']['results'])
                        ) {
                     
                            $oceerResult = $oceerResult['data']['results']['data']['identification_results'];
                      
                            $results = $oceerResult['results'];
                        
                        

                            foreach($results as $key=>$result) {
                   
                                if($result['score']>=0.8 && $result['value']!='') {
                                    $update = FormsData::updateOrCreate(
                                        [
                                            'dossier_id' => $dossier->id,
                                            'form_id' => $request->form_id,
                                            'meta_key' => $request->name.'.'.$result['id']
                                        ],
                                        [
                                            'meta_value' => $result['value']
                                        ]
                                    );
                                }

                              
                            }

                         
                        } else {
                           
                        }

                    }
           

                }



               
               
            }

            // 7. Return transcription (and possibly the PDF path) to the frontend
            return response()->json([
                'message'       => 'Audio transcription successful.',
                'transcription' => $transcription ?? '',
                'oceer_result'  => $oceerResult ?? '',

                // 'pdf_path'    => $pdfPath ?? null, // Optionally include the PDF path
            ]);

        } catch (\Exception $e) {
            // 8. Catch any exception and return a JSON error
            return response()->json([
                'message' => 'Exception when contacting OpenAI Whisper API.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function sendPdfToOceer(string $pdfPath)
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
            'api-key'       => 'R3SxEL6mbZ9UO9a',
        ];

        // Make the POST request using Laravel's HTTP Client:
        $response = Http::withHeaders($headers)
            ->attach(
                'document',
                file_get_contents($pdfPath), // file content
                basename($pdfPath)           // filename
            )
            ->post('https://app.oceer.fr/api/pipeline/start/229bdbbf-869a-49c9-83cb-ae069f1137ff');
   
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
