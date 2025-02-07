<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    public function store(Request $request)
    {
        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $filename = 'audio_' . time() . '.wav';
            $path = $file->storeAs('public/audio', $filename);

            return response()->json([
                'message' => 'Audio saved successfully!',
                'file_path' => Storage::url($path),
            ]);
        }

        return response()->json(['message' => 'No audio file received'], 400);
    }



    public function show() {
        return view('audio.show');

    }
}
