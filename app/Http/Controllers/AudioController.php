<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Dossier;
use App\Models\FormsData;
use Illuminate\Support\Facades\Http; // Add this line

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
      
       

        $audioPath = $request->value; // example path
        $absolutePath = storage_path('app/public/' . $audioPath);
dd($absolutePath);
        // 2. Check if the file actually exists
        if (!file_exists($absolutePath)) {
            return response()->json([
                'message' => 'Audio file not found.'
            ], 404);
        }


        
        $apiKey = env('OPENAI_API_KEY'); // store your API key in .env
        if (!$apiKey) {
            return response()->json([
                'message' => 'OpenAI API key not set.'
            ], 500);
        }

        try {
            // 4. Send request to OpenAI
            $response = Http::withToken($apiKey)
                ->attach(
                    'file',
                    file_get_contents($absolutePath),
                    basename($absolutePath)
                )
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1', // OpenAI model name
                    // Optionally add other parameters like 'prompt', 'language', or 'temperature'
                    // 'prompt' => 'Custom prompt if needed',
                    // 'language' => 'fr', // If you know the language is French
                ]);

            if ($response->failed()) {
                return response()->json([
                    'message' => 'OpenAI Whisper API request failed.',
                    'error' => $response->json()
                ], $response->status());
            }

            // 5. Extract the transcription text from OpenAI's response
            //    According to OpenAI docs, the JSON looks like { text: "transcribed text" }
            $result = $response->json(); 
            $transcription = $result['text'] ?? '(No transcription)';

            // 6. Return the transcription to the frontend
            return response()->json([
                'message' => 'Audio transcription successful.',
                'transcription' => $transcription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Exception when contacting OpenAI Whisper API.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show() {
        return view('audio.show');

    }
}
