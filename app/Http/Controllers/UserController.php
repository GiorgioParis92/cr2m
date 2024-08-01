<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\FileUploadService;
use App\Models\ClientType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Notifications\UserCreatedNotification;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{

    public function index(Request $request)
    {

        if(auth()->user()->type_id>2) {
            abort('403');
        }
        $users = User::with('client.type')->with('type');
        if (auth()->user()->client_id > 0) {
            $users = $users->where('client_id', auth()->user()->client_id);
        }
        $users = $users->get();

        return view('users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $clients = Client::where('id','>',0)->get();
        $types = DB::table('users_type')->where('type_client_id', '>', 0)->get();



        return view('users.create', compact('clients', 'types'));
    }
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            // Redirect back with input and error messages
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
      
        $temporaryPassword = Str::random(12);
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($temporaryPassword),
            'client_id' => $request->input('client_id'),
            'type_id' => $request->input('type_id'),
            // Add other fields as necessary
        ]);

        // Send email notification to the user
        Notification::route('mail', $user->email)->notify(new UserCreatedNotification($user, $temporaryPassword));

        return redirect()->route('users.index')->with('success', 'Utilisateur créé.');
    }
    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        $clients = Client::where('id','>',0)->get();
        $types = DB::table('users_type')->where('type_client_id', '>', 0)->get();
        return view('users.edit', compact('user', 'clients', 'types'));

    }
    public function editUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('client_id')) {
            $user->client_id = $request->client_id;
        }
        if ($request->has('type_id')) {
            $user->type_id = $request->type_id;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Utilisateur modifié.');
    }

    
    public function destroy(Request $request,$id)
    {


        $user = User::find($id);

    

        $user->delete();

   

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }
    public function showTemporaryPasswordForm($email)
    {
        // Decode email to handle URL encoding
        $email = urldecode($email);
    
        // Find the user by email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            // Handle the case where the user is not found
            return redirect()->route('login')->with('error', 'User not found.');
        }
    
        // Check if the authenticated user is different from the user in the URL
        if (auth()->check() && auth()->user()->email !== $email) {
            // Capture intended URL
            session(['url.intended' => route('temporary.password.reset', ['email' => $email])]);
    
            // Log out the current user
            auth()->logout();
    
            // Redirect to login page with message
            return redirect()->route('login')->with('error', 'You need to log in with the correct account to reset the password.');
        }
    
        // Set a session flag to indicate that this is a temporary password reset
        session(['is_temporary_password' => true, 'user_email' => $email]);
    
        // Show the password reset form
        return view('auth.reset-temporary-password', compact('email'));
    }
    
    
    

    public function updateTemporaryPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear the temporary password flag
        $request->session()->forget('is_temporary_password');

        return redirect()->route('dashboard')->with('success', 'Password has been reset successfully.');
    }
}
