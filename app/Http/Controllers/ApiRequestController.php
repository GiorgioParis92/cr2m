<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiRequestController extends Controller
{
    public function sendApiRequest(Request $request)
    {
        $client = new Client();
  
        try {
            $multipart = [
                [
                    'name'     => 'login',
                    'contents' => $request->input('login'),
                ],
                [
                    'name'     => 'password',
                    'contents' => $request->input('password'),
                ],
                // Add other form data similarly
                // ...

                // Handle file uploads
                // Check if files are uploaded
               
            ];
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $multipart[] = [
                        'name'     => 'file[]',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ];
                }
            }
            $response = $client->request('POST', 'http://elite/api/demandes/new', [
                'headers' => [
                    'Accept'        => '*/*',
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJh... (rest of the token)',
                    // Include other headers as necessary
                ],
                'multipart' => $multipart,
            ]);

            // Handle the response
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            // You can process the response as needed
            return redirect()->back()->with('success', 'API request successful.');

        } catch (RequestException $e) {
            // Handle exception
            return redirect()->back()->with('error', 'API request failed: ' . $e->getMessage());
        }
    }
}
