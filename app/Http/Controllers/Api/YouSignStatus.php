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
use App\Models\FormConfig;
use App\Models\FormsData;

class YouSignStatus extends Controller
{




  public function index(Request $request): \Illuminate\Http\JsonResponse
  {
    
    $url = 'http://192.168.100.40:5010/process_request?service=yousign';
    $data = json_encode([
      'request_type' => 'get_status',
      'service' => 'yousign', // Add the service field

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
        'data' => $data,
        'service' => 'yousign', // Add the service field


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
      if(isset($request->id)) {
        $dossier = Dossier::where('id', $request->id)->first();
      } else {
        $dossier = Dossier::where('folder', $request->dossier_id)->first();
      }
      // Process response
      $responseData = json_decode($response);
      if(!isset($request->signature) || (isset($request->signature) && $request->signature!=2)) {
        $suffix='';
      } else {
        $suffix=2;
      }

      if (
        isset($responseData->status) && (
          $responseData->status === 'done' ||
          $responseData->status === 'uuid not found' ||
          $responseData->status === 'error'
        )
      ) {

     
        if ($responseData->result->data->document->status == 'ongoing') {




              $update = FormsData::updateOrCreate(
              [
                  'dossier_id' => (string) $dossier->id,
                  'form_id' => (string) $request->form_id,
                  'meta_key' => 'signature_status'.$suffix,
              ],
              [
                  'meta_value' => 'ongoing',
                  'updated_at' => now(),
              ]
              );

          if ($dossier && $dossier->etape) {
            $orderColumn = $dossier->etape->order_column;
        } else {
            // Handle the case where $dossier or $dossier->etape is null
            $orderColumn = null;
        }
          $docs=getDocumentStatuses($dossier->id,$orderColumn);

          return response()->json('ongoing', 200);
        }



        if ($responseData->result->data->document->status == 'done') {

     $update = FormsData::updateOrCreate(
    [
        'dossier_id' => (string) $dossier->id,
        'form_id' => (string) $request->form_id,
        'meta_key' => 'signature_status'.$suffix,
    ],
    [
        'meta_value' => 'done',
        'updated_at' => now(),
    ]
);


          if ($dossier && $dossier->etape) {
            $orderColumn = $dossier->etape->order_column;
        } else {
            // Handle the case where $dossier or $dossier->etape is null
            $orderColumn = null;
        }
          $docs=getDocumentStatuses($dossier->id,$orderColumn);
          $url = 'http://192.168.100.40:5010/process_request?service=yousign';
          $data = json_encode([
            'request_type' => 'download_document',
            'service' => 'yousign', // Add the service field

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
              'data' => $data,
              'service' => 'yousign', // Add the service field


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
             
              $path = 'storage/dossiers/'.$dossier->folder.'/'.$request->template.'.pdf';

              $outputFile = public_path($path);

              $ch = curl_init();
          
              // Set cURL options
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_HTTPHEADER, [
                  'Accept: application/zip, application/pdf',
                  'Authorization: Bearer ' . $token,
              ]);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
              // Execute the request and fetch the response
              $response = curl_exec($ch);
              
              // Check for cURL errors
              if (curl_errno($ch)) {
                  echo 'cURL error: ' . curl_error($ch);
                
              } else {
                file_put_contents($outputFile, $response);

           $update = FormsData::updateOrCreate(
    [
        'dossier_id' => (string) $dossier->id,
        'form_id' => (string) $request->form_id,
        'meta_key' => 'signature_status'.$suffix,
    ],
    [
        'meta_value' => 'finish',
        'updated_at' => now(),
    ]
);

                if ($dossier && $dossier->etape) {
                  $orderColumn = $dossier->etape->order_column;
              } else {
                  // Handle the case where $dossier or $dossier->etape is null
                  $orderColumn = null;
              }
                $docs=getDocumentStatuses($dossier->id,$orderColumn);

    

                 
              }
          
              // Close the cURL session
              curl_close($ch);





        
              return response()->json('OK', 200);



            }




          }

          return response()->json('done', 200);
        }


  $update = FormsData::updateOrCreate(
    [
        'dossier_id' => (string) $dossier->id,
        'form_id' => (string) $request->form_id,
        'meta_key' => (string) $request->template,
    ],
    [
        'meta_value' => (string) $directPath,
        'created_at' => now(),
        'updated_at' => now(),
    ]
);





      }


      sleep(4);

    }




    // Sleep for a while before making the next request


    return response()->json($results, 200);

  }

}