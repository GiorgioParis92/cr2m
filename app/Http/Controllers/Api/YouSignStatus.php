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
use Illuminate\Support\Facades\Storage;

class YouSignStatus extends Controller
{




  public function index(Request $request): \Illuminate\Http\JsonResponse
  {

    $url = 'http://192.168.100.40:5010/process_request?service=yousign';
    $data = json_encode([
      'request_type' => 'get_status',
      'request_data' => [
        "signature_request_id" => $request->signature_request_id
      ]
    ]);


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
        'data' => $data

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
      $dossier = Dossier::where('folder', $request->dossier_id)->first();

      // Process response
      $responseData = json_decode($response);


      if (
        isset($responseData->status) && (
          $responseData->status === 'done' ||
          $responseData->status === 'uuid not found' ||
          $responseData->status === 'error'
        )
      ) {
        if ($responseData->result->data->document->status == 'ongoing') {

          $update = DB::table('forms_data')->updateOrInsert(
            [
              'dossier_id' => '' . $dossier->id . '',
              'form_id' => '' . $request->form_id . '',
              'meta_key' => 'signature_status'
            ],
            [
              'meta_value' => 'ongoing',
              'created_at' => now(),
              'updated_at' => now()
            ]
          );

          return response()->json('ongoing', 200);
        }



        if ($responseData->result->data->document->status == 'done') {

          $update = DB::table('forms_data')->updateOrInsert(
            [
              'dossier_id' => '' . $dossier->id . '',
              'form_id' => '' . $request->form_id . '',
              'meta_key' => 'signature_status'
            ],
            [
              'meta_value' => 'done',
              'created_at' => now(),
              'updated_at' => now()
            ]
          );


          $url = 'http://192.168.100.40:5010/process_request?service=yousign';
          $data = json_encode([
            'request_type' => 'download_document',
            'request_data' => [
              "signature_request_id" => $request->signature_request_id,
              "document_id" => $request->document_id
            ]


          ]);


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
              'data' => $data

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


              $url = $responseData->result->data->url_info->url;
              $token = $responseData->result->data->url_info->token; // Replace YOUR_BEARER_TOKEN with your actual token

              $path = 'storage/dossiers/'.$request->dossier_id.'/'.$request->template.'.pdf';

              $outputFile = public_path($path);

              $ch = curl_init();
          
              // Set cURL options
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_HTTPHEADER, [
                  'Accept: application/zip, application/pdf',
                  'Authorization: Bearer ' . $token,
              ]);
          
              // Execute the request and fetch the response
              $response = curl_exec($ch);
          
              // Check for cURL errors
              if (curl_errno($ch)) {
                  echo 'cURL error: ' . curl_error($ch);
              } else {
                file_put_contents($outputFile, $response);

                $update = DB::table('forms_data')->updateOrInsert(
                  [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $request->form_id . '',
                    'meta_key' => 'signature_status'
                  ],
                  [
                    'meta_value' => 'finish',
                    'created_at' => now(),
                    'updated_at' => now()
                  ]
                );


    

                  dd($response);
              }
          
              // Close the cURL session
              curl_close($ch);





              dd($responseData);
              return response()->json('OK', 200);



            }




          }

          return response()->json('done', 200);
        }


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