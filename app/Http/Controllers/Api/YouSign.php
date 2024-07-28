<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use App\Models\Dossier;
use App\Models\Users;
use Illuminate\Http\RedirectResponse;
use App\Models\Tags;
use App\Models\RequiredDocs;
use phpseclib3\Net\SSH2;
use phpseclib3\Net\SFTP;

class YouSign extends Controller
{




  public function index(Request $request): \Illuminate\Http\JsonResponse
  {


    $url = 'http://192.168.100.40:5010/process_request?service=yousign';
    $data = json_encode([
        'request_type' => 'create_document',
        'request_data' => [
            'signature_name' => 'Attestation de visite',
            'delivery_mode' => 'email',
            'signature_level' => 'electronic_signature',
            'fields' => [
                [
                    'type' => 'signature',
                    'page' => 1,
                    'width' => 180,
                    'x' => 400,
                    'y' => 650
                ]
            ],
            'signer_info' => [
                'first_name' => 'Georges',
                'last_name' => 'KALFON',
                'email' => 'genius.market.fr@gmail.com',
                'phone_number' => '+33651980838'
            ]
        ]
    ]);

    
    $file = new \CURLFile($request->file, 'application/pdf');

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'Content-Type: multipart/form-data'
        ],
        CURLOPT_POSTFIELDS => [
            'data' => $data,
            'file' => $file
        ],
    ]);

    $response = curl_exec($curl);


    // Extracting UUID
    $responseData = json_decode($response, true);

    $uuid = $responseData['request_uuid'];



    while (true) {


      $url = 'http://192.168.100.40:5010/get_packet_result';
      $params = array(
        'uuid_packet' => $uuid,
        'service' => 'yousign'
      );

      // Initialize cURL
      $ch = curl_init();

      // Set URL
      curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));

      // Set method to POST
      curl_setopt($ch, CURLOPT_POST, true);

      // Set headers
      curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
          'accept: application/json'
        )
      );

      // Set data
      curl_setopt($ch, CURLOPT_POSTFIELDS, '');

      // Set return transfer to true
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      // Execute cURL request
      $response = curl_exec($ch);

      // Check for errors
      if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
      }

      // Close cURL session
      curl_close($ch);

      // Process response
      $responseData = json_decode($response);


      if (
        isset($responseData->status) && (
          $responseData->status === 'done' ||
          $responseData->status === 'uuid not found' ||
          $responseData->status === 'error'
        )
      ) {
        dd($responseData);

      
        $update = DB::table('forms_data')->updateOrInsert(
          [
              'dossier_id' => '' . $dossier->id . '',
              'form_id' => '' . $request->form_id . '',
              'meta_key' => '' . $request->template . ''
          ],
          [
              'meta_value' => '' . $directPath . '',
              'created_at' => now(),
              'updated_at' => now()
          ]
      );
       



      }




    }




    // Sleep for a while before making the next request


    return response()->json($results, 200);

  }

}