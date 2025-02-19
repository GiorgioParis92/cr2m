<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploadService;
use App\Models\ClientLinks;
use App\Models\ClientType;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Notifications\UserCreatedNotification;
use App\Models\User;

class ClientController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index()
    {

        if(auth()->user()->client_id>0) {abort(403);}
        $clients = Client::with('type')->get();
        return view('clients.index', compact('clients'));
    }

    public function uploadLogo(Request $request)
    {
        if ($request->hasFile('file')) {
            // Get the file from the request
            $file = $request->file('file');
        
            // Define the path
            $tempPath = 'public/clients/temp';
            $clientFolder = "public/clients/{$request->client_id}";
        
            // Ensure the client directory exists
            if (!Storage::exists($clientFolder)) {
                Storage::makeDirectory($clientFolder);
            }
        
            // Store the file in the temp directory
            $tempFilePath = $file->store($tempPath);
        
            // Move the file to the client directory
            $finalPath = str_replace('temp/', "{$request->client_id}/", $tempFilePath);
            Storage::move($tempFilePath, $finalPath);
        
            // Remove 'public/' from the final path to make it accessible via URL
            $finalPath = str_replace('public/', '', $finalPath);
        
            // Find the client by ID
            $client = Client::findOrFail($request->client_id);
        
            // Update the main logo path
            $client->main_logo = $finalPath;
            $client->save();
        
            // Return the file path in the response
            return response()->json(['file_path' => $finalPath]);
        }
        

        return response()->json(['error' => 'File not uploaded'], 400);
    }

    public function create()
    {
        $clientTypes = ClientType::all();
        return view('clients.create', compact('clientTypes'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'client_title' => 'required|string|max:255',
            'type_client' => 'required|integer|exists:clients_type,id',
            'email' => 'required|email|unique:users,email', // Ensure email is unique
            // Add other validation rules as needed
        ]);
    
        // Manually create the client data array, excluding '_token'
        $clientData = $request->except('_token', 'main_logo', 'client_email');
    
        // Store the client data and get the client ID
        $client = Client::create($clientData);
        $clientId = $client->id;
        $finalPath = '';
    
        if ($request->main_logo) {
            $tempPath = $request->input('main_logo');
            $finalPath = str_replace('temp/', "{$clientId}/", $tempPath);
            Storage::disk('public')->move($tempPath, $finalPath);
        }
    
        // Update the client with the final path
        $client->main_logo = $finalPath;
        $client->save();
    
        // Generate a temporary password
        $temporaryPassword = Str::random(12);
    
        // Create the user associated with the client
        $user = User::create([
            'name' => $clientData['client_title'],
            'email' => $request->input('email'),
            'password' => Hash::make($temporaryPassword),
            'client_id' => $clientId,
            'type_id' => 2,
            // Add other fields as necessary
        ]);
    
        // Send email notification to the user
        Notification::route('mail', $user->email)->notify(new UserCreatedNotification($user, $temporaryPassword));
    
        return redirect()->route('clients.index')->with('success', 'Client and associated user created successfully.');
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $installateurs = Client::where('type_client',3)->get();

        $clientTypes = ClientType::all();
        $has_parent = ClientLinks::where('client_id', $id)->pluck('client_parent')->toArray();
        $has_child = ClientLinks::where('client_parent', $id)->pluck('client_id')->toArray();
      
        $has_parent = ClientLinks::where('client_id', $id)
        ->with('client_parent') // Load the related client model
        ->get(['client_parent']) // Only select the client_parent field
        ->toArray();

        $has_child = ClientLinks::where('client_parent', $id)
        ->with('client_child') // Load the related client model
        ->get(['client_id']) // Only select the client_parent field
        ->toArray();


        return view('clients.edit', compact('client','installateurs', 'clientTypes','has_child','has_parent'));
    }

    public function update(Request $request, $id)
    {
        // Validate the form data
        $request->validate([
            'client_title' => 'required|string|max:255',
            'type_client' => 'required|integer|exists:clients_type,id',
            // Add other validation rules as needed
        ]);
        dd($request);
        // Find the client
        $client = Client::findOrFail($id);

        // Manually create the client data array, excluding '_token' and 'main_logo'
        $clientData = $request->except('_token', 'main_logo');

        // Update the client data
        $client->update($clientData);

        // Handle the logo file
        if ($request->main_logo && !empty($request->input('main_logo')) && $request->input('main_logo') != $client->main_logo) {
            // Move the temporary file to the final location
            $tempPath = $request->input('main_logo');
            $finalPath = str_replace('temp/', "clients/{$client->id}/", $tempPath);

            Storage::disk('public')->move($tempPath, $finalPath);

            // Update the client with the final path
            $client->main_logo = $finalPath;
            $client->save();
        }

        return redirect()->route('clients.index')->with('success', __('messages.client_updated'));
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        // Delete the client's main logo file from storage
        if ($client->main_logo && Storage::disk('public')->exists($client->main_logo)) {
            Storage::disk('public')->delete($client->main_logo);
        }

        // Delete the client record from the database
        $client->delete();

        return redirect()->route('clients.index')->with('success', __('messages.client_deleted').'.');
    }
    public function removeParent(Request $request)
    {
    
        $clientParent = ClientLinks::where('client_id',$request->id)->where('client_parent',$request->parent);
        $clientParent->delete();
    
        return back()->with('success', 'Client parent removed successfully.');
    }
    public function addParent(Request $request, $id)
{
    $request->validate([
        'client_parent' => 'required',
    ]);

    ClientLinks::create([
        'client_id' => $id,
        'client_parent' => $request->client_parent,
    ]);

    return redirect()->route('clients.edit', $id)->with('success', 'Client parent added successfully.');
}

public function addchild(Request $request, $id)
{
    $request->validate([
        'client_child' => 'required',
    ]);

    ClientLinks::create([
        'client_id' => $request->client_child,
        'client_parent' => $id,
    ]);

    return redirect()->route('clients.edit', $id)->with('success', 'Client parent added successfully.');
}
}

