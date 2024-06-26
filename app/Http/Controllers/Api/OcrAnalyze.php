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

class OcrAnalyze extends Controller
{




  public function index(Request $request): \Illuminate\Http\JsonResponse
  {



    $data = array(
      'service' => 'ocr',
      'data' => $request['data'] ? json_encode($request['data'], JSON_NUMERIC_CHECK) : '{}',
      'file' => $request->file,

    );

    $response = makeRequest('http://192.168.100.40:5010/process_request', $data);
    // Extracting UUID
    $responseData = json_decode($response, true);

    $uuid = $responseData['request_uuid'];



    while (true) {


      $url = 'http://192.168.100.40:5010/get_packet_result';
      $params = array(
        'uuid_packet' => $uuid,
        'service' => 'ocr'
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

        $document = $responseData->result->data->oceer_document;

        $request_data = [
          "document_name" => "avis_impot",
          "operation_id" => 24,
          "document" => $document,
        ];
        $data = array(
          'service' => 'fast_oceer',
          'data' => json_encode($request_data),
          "file" => null

        );
        $response = makeRequest('http://192.168.100.40:5010/process_request', $data);
        // Extracting UUID
        $responseData = json_decode($response, true);
        $uuid = $responseData['request_uuid'];
        while (true) {


          $url = 'http://192.168.100.40:5010/get_packet_result';
          $params = array(
            'uuid_packet' => $uuid,
            'service' => 'fast_oceer'
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

            return response()->json($responseData, 200);

          }




        }



      }




    }




    // Sleep for a while before making the next request


    return response()->json($results, 200);

  }

}