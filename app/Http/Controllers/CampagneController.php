<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\CampagneStatus;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampagneController extends Controller
{
    public function create()
    {
        $clients = Client::all();
        $status = CampagneStatus::all();
        return view('campagnes.create', compact('clients','status'));
    }

    public function store(Request $request)
{
    $request->validate([
        'client_id' => 'required',
        'campagne_title' => 'required',
        'campagne_status' => 'required',
        'main_image_path' => 'required', // Ensure the main image path is present
    ]);

    // Create the campagne with the validated data
    $campagne = new Campagne();
    $campagne->client_id = $request->client_id;
    $campagne->campagne_title = $request->campagne_title;
    $campagne->campagne_status = $request->campagne_status;
    $campagne->main_image = $request->main_image_path; // Save the image path
    $campagne->save();

    return redirect()->route('dashboard2')->with('success', 'Campagne created successfully.');
}

public function edit($id)
{
    $campagne = Campagne::with('docs')->findOrFail($id);
    $clients = Client::all();
    $status = CampagneStatus::all();
    return view('campagnes.edit', compact('campagne','clients','status'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'client_id' => 'required',
        'campagne_title' => 'required',
        'campagne_status' => 'required',
        'main_image_path' => 'required', // Ensure the main image path is present
    ]);

    $campagne = Campagne::findOrFail($id);
    $campagne->client_id = $request->client_id;
    $campagne->campagne_title = $request->campagne_title;
    $campagne->campagne_status = $request->campagne_status;
    $campagne->main_image = $request->main_image_path;
    $campagne->save();

    return redirect()->route('dashboard2')->with('success', 'Campagne updated successfully.');
}


public function upload(Request $request)
{
    $request->validate([
        'file' => 'required|image|max:2048', // Validate the file
    ]);

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $path = $file->store('uploads', 'public'); // Save the file and get the path

        return response()->json(['path' => $path], 200);
    }

    return response()->json(['message' => 'Upload failed'], 400);
}


}
