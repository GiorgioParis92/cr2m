<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->updatePushSubscription(
                $request->input('endpoint'),
                $request->input('keys.p256dh'),
                $request->input('keys.auth'),
                $request->input('content_encoding') // Include content_encoding
            );
        } else {
            // Handle unauthenticated user
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        return response()->json(['success' => true]);
    }
}

