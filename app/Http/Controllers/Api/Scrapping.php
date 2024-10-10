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

class Scrapping extends Controller
{




  public function index(Request $request): \Illuminate\Http\JsonResponse
  {

    $url = 'http://192.168.100.40:5010/process_request?service=scrapping';

    if (isset($request->reference_unique)) {
      $reference_unique = $request->reference_unique;
    } else {

      return response()->json('error', 401);

    }


    // return response()->json($reference_unique, 200);

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
        'data' => '{
  "pipeline_name":"anah",
  "login":"' . $request->login . '",
  "password":"' . $request->password . '",
  "scrapping_config":[
    {
      "element_screenshot":true,
      "url":"https://monprojet.anah.gouv.fr/dossiers/' . $reference_unique . '",
      "data":{
        "Statut du dossier":{
          "type":"class",
          "name":"page-heading"
        },
        "Infos dossier":{
          "type":"class",
          "name":"c--app--tag__element"
        }
      }
    },
    {
      "element_screenshot":true,
      "url":"https://monprojet.anah.gouv.fr/dossiers/' . $reference_unique . '/contacts",
      "data":{
       
        "Demandeur":{
          "type":"class",
          "split":{
            "split_char":"\n",
            "element_to_keep":[
      {"index":1,"title":"<i class=\'fa fa-comments-dollar\'></i>Bénéficiaire","tag_to_match":["nom","prenom"]},  
      {"index":2,"title":"<i class=\'fa fa-envelope\'></i>E-mail","tag_to_match":["email"]},  
      {"index":3,"title":"<i class=\'fa fa-phone\'></i>Téléphone","tag_to_match":["telephone"]} 
     
            ]
          },
          "name":"contact-details-item",
          "indexes":[
            0
          ]
        },
        "instructeur":{
          "type":"class",
          "name":"contact-details-item",
          "key_words":[
            "Instructeur"
          ],
          "operator":"and"
        },
        "Mandataire":{
          "type":"class",
          "name":"contact-details-item",
          "key_words":[
            "Mandataire"
          ],
          "operator":"and"
        }
      }
    },
    {
      "element_screenshot":true,
      "url":"https://monprojet.anah.gouv.fr/dossiers/' . $reference_unique . '/messages",
      "data":{
        "Messages":{
          "type":"class",
          "display":"false",
          "name":"_messages--list"
        }
      }
    }
  ]
}',
        'service' => 'scrapping'
      ],
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
      die('Curl error: ' . curl_error($curl)); // Output cURL error
    }

    $responseData = json_decode($response, true);

    // Extracting UUID

    $uuid = $responseData['request_uuid'];

    while (true) {


      $url = 'http://192.168.100.40:5010/get_packet_result';
      $params = array(
        'uuid_packet' => $uuid,
        'service' => 'scrapping'
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

        return response()->json($resultData, 200);


      }



      sleep(0.5);

    }




    // Sleep for a while before making the next request




  }

}