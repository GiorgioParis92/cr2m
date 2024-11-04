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

class YouSign extends Controller
{




  public function index(Request $request): \Illuminate\Http\JsonResponse
  {

    $url = 'http://192.168.100.40:5010/process_request?service=yousign';

    $dossier = Dossier::where('folder', $request->dossier_id);
    $dossier = $dossier->with('beneficiaire', 'fiche', 'etape', 'status', 'get_rdv')->first();

    // if(auth()->user()->id==1) {
    //     dd($request);
    //   }

    if ($dossier) {



      $data = json_encode([
        'request_type' => 'create_document',
        'service' => 'yousign', // Add the service field

        'request_data' => [
          'service' => 'yousign', // Add the service field

          'signature_name' => $request->name ?? '',
          'delivery_mode' => 'email',
          'signature_level' => 'electronic_signature',
          'fields' => json_decode(json_encode($request->fields), true),
          'signer_info' => [
            'first_name' => $dossier->beneficiaire->prenom ?? '',
            'last_name' => $dossier->beneficiaire->nom ?? '',
            'email' => $dossier->beneficiaire->email ? str_replace(' ','',$dossier->beneficiaire->email) :  '',
            'phone_number' => formatFrenchPhoneNumber($dossier->beneficiaire->telephone) ?? ''
          ]
        ]
      ]);
      // if(auth()->user()->id==1) {
      //   dd($data);
      // }
      $path = 'storage/dossiers/' . $request->dossier_id . '/' . $request->name . '.pdf';

      $fullPath = public_path($path);
    }

    // Check if the file exists


    $file = new \CURLFile($fullPath, 'application/pdf');

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
        'file' => $file,
        'service' => 'yousign'
      ],
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
      die('Curl error: ' . curl_error($curl)); // Output cURL error
    }

    $responseData = json_decode($response, true);

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
      
        $resultData = $responseData->result->data->result ?? null;

        if ($resultData->signature_request_id) {
          $signatureRequestId = $resultData->signature_request_id ?? '';
          $documentId = $resultData->document_id ?? '';


          $update = DB::table('forms_data')->updateOrInsert(
            [
              'dossier_id' => '' . $dossier->id . '',
              'form_id' => '' . $request->form_id . '',
              'meta_key' => 'signature_request_id'
            ],
            [
              'meta_value' => $signatureRequestId,
              'created_at' => now(),
              'updated_at' => now()
            ]
          );

          $update = DB::table('forms_data')->updateOrInsert(
            [
              'dossier_id' => '' . $dossier->id . '',
              'form_id' => '' . $request->form_id . '',
              'meta_key' => 'document_id'
            ],
            [
              'meta_value' => $documentId,
              'created_at' => now(),
              'updated_at' => now()
            ]
          );
          if ($dossier && $dossier->etape) {
            $orderColumn = $dossier->etape->order_column;
        } else {
            // Handle the case where $dossier or $dossier->etape is null
            $orderColumn = null;
        }
          $docs=getDocumentStatuses($dossier->id,$orderColumn);
          return response()->json($resultData, 200);
        }
      }


      sleep(0.15);


    }




    // Sleep for a while before making the next request



  }

}