<?php

use App\Models\User;
use App\Models\Dossier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\UserPermission;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;



if (!function_exists('is_user_forbidden')) {
     function is_user_forbidden($permission_name)
    {
   
        $user = Auth::user();

   
        $permission = DB::table('forbidden_actions_type_users')
        ->where('type_user', $user->type_id)
        ->where('permission_name', $permission_name)
        ->first();
        // if($permission) {
        //     print_r($permission);
        // }
        
        if ($permission && $permission->is_active == 0) {
            return true;
        }
        return false;
    }
}

if (!function_exists('is_user_allowed')) {
    /**
     * Check if the authenticated user has the given permission.
     *
     * @param string $permission_name
     * @return array
     */
    function is_user_allowed($permission_name)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $userPermission = UserPermission::where('user_id', $user->id)
        ->where('permission_name', $permission_name)
        ->first();
        // if (!$userPermission) {
        //     return false;
        // }
    if ($userPermission && $userPermission->is_active == 1) {
        return true;
    }
    if ($userPermission && $userPermission->is_active == 0) {
        return false;
    }


        if ($user->client_id == 0) {
            $defaultPermission = DB::table('default_permission')->where('type_id', $user->type_id)
                ->where('permission_name', $permission_name)
                ->where('type_client', 0)

                ->first();


            if ($defaultPermission && $defaultPermission->is_active == 0) {
                return false;
            }
            if ($defaultPermission && $defaultPermission->is_active == 1) {
                return true;
            }
            return true;
        }
     

        if ($userPermission) {
            if ($userPermission->is_active == 0) {
                return false;
            }
        } else {

            // cas spécifique pour un type de user quand client
            $defaultPermission = DB::table('default_permission')
                ->where('type_id', $user->type_id)
                ->where('permission_name', $permission_name)
                ->where('type_client', $user->client->type_client)

                ->first();


            if ($defaultPermission && $defaultPermission->is_active == 0) {
                return false;
            }
            if ($defaultPermission && $defaultPermission->is_active == 1) {
                return true;
            }

            // cas général pour tout type de user quand client

            if (isset($user->client->type_client)) {
                $defaultPermission = DB::table('default_permission')
                    ->where('type_client', $user->client->type_client)
                    ->where('permission_name', $permission_name)
                    ->where('type_id', 0)

                    ->first();



                if ($defaultPermission && $defaultPermission->is_active == 0) {
                    return false;
                }
                if ($defaultPermission && $defaultPermission->is_active == 1) {
                    return true;
                }
            }










        }



        return true;
    }
}

function generateRandomString($length = 12)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function compressImage($filePath, $quality = 20)
{
    $imageInfo = getimagesize($filePath);
    if ($imageInfo['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($filePath);
        ob_start();
        imagejpeg($image, null, $quality);  // Compress the image
        $compressedImage = ob_get_clean();
        imagedestroy($image);
        return base64_encode($compressedImage);
    } elseif ($imageInfo['mime'] == 'image/png') {
        $image = imagecreatefrompng($filePath);
        ob_start();
        imagepng($image, null, 4);  // Reduce quality for PNG
        $compressedImage = ob_get_clean();
        imagedestroy($image);
        return base64_encode($compressedImage);
    }
    return base64_encode(file_get_contents($filePath));  // Fallback for other formats
}
function makeRequest($url, $data)
{
    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);

    // Build the multipart/form-data payload
    $multipartData = [];

    foreach ($data as $key => $value) {
        if ($key === 'file' && $value instanceof \CURLFile) {
            // Use the provided CURLFile instance
            $multipartData[$key] = $value;
        } else {
            // Add other data as usual
            $multipartData[$key] = $value;
        }
    }

    curl_setopt($ch, CURLOPT_POSTFIELDS, $multipartData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    return $response;
}




function getColorForType($type)
{
    $colors = [
        '1' => '#cb0c9f',
        '2' => '#8392AB',
        '3' => '#3357FF',
        // Add more mappings as necessary
    ];

    return $colors[$type] ?? '#FF5733'; // Default color if type is not found
}


if (!function_exists('format_date')) {
    function format_date($value)
    {
        $value = str_replace('/', '-', $value);

        $value = str_replace('.', '-', $value);


        $value = strtotime($value);

        $value = date("d/m/Y", $value);
        return $value;
    }
}


if (!function_exists('percent_difference')) {
    function percent_difference($value1, $value2)
    {

        if ($value2 == 0) {
            return '0%';
        }

        $value = ($value1 - $value2) / $value2 * 100;
        $value = number_format($value,2) . '%';
        return $value;
    }
}

if (!function_exists('strtotime_date')) {
    function strtotime_date($value)
    {
        $value = str_replace('/', '-', $value);
        // $value = str_replace('.', '-', $value);
        // $value = str_replace('', '-', $value);
        $value = strtotime($value);
        return $value;
    }
}

if (!function_exists('store_image')) {
    /**
     * Store an uploaded image in a specific folder structure.
     *
     * @param Request $request
     * @param string $folder
     * @param int $clientId
     * @param string $inputName
     * @return string|false
     */
    if (!function_exists('store_image')) {
        /**
         * Store an uploaded image in a specific folder structure.
         *
         * @param UploadedFile $file
         * @param string $folder
         * @param string $inputName
         * @return string|false
         */
        function store_image(UploadedFile $file, string $folder, string $clientId = null)
        {
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf', 'heic'];
            $extension = $file->getClientOriginalExtension();

            if (!in_array($extension, $allowedExtensions)) {
                return false;
            }

            $directory = $clientId ? "{$folder}/{$clientId}" : "{$folder}/temp";
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            return $file->store($directory, 'public');
        }
    }
}

if (!function_exists('is_client')) {
    /**
     * Check if the user's client ID is greater than 0.
     *
     * @param int $userId
     * @return bool
     */
    function is_client($userId)
    {
        $user = User::find($userId);
        return $user ? $user->client_id > 0 : false;
    }
}

function formatFrenchPhoneNumber($phoneNumber)
{
    // Regular expression for French phone number
    $pattern = '/^(?:(?:\+|00)33|0)[1-9](?:[\s\.\-]?\d{2}){4}$/';

    // Check if the phone number matches the regex pattern
    if (preg_match($pattern, $phoneNumber)) {
        // Remove any spaces, dots, or dashes
        $cleanedNumber = preg_replace('/[\s\.\-]/', '', $phoneNumber);

        // Replace the leading 0 with +33 or remove +33 if already present
        if (substr($cleanedNumber, 0, 1) === '0') {
            $cleanedNumber = '+33' . substr($cleanedNumber, 1);
        } elseif (substr($cleanedNumber, 0, 2) === '00') {
            $cleanedNumber = '+' . substr($cleanedNumber, 2);
        } elseif (substr($cleanedNumber, 0, 3) === '+33') {
            // Already in the correct format
            return $cleanedNumber;
        }

        return $cleanedNumber;
    } else {
        // Return false or throw an error if the number doesn't match the pattern
        return false;
    }
}

if (!function_exists('load_all_dossier_data')) {
    function load_all_dossier_data($dossier)
    {
        $results = [
            "form_data" => [],
            "beneficiaire_data" => [],
            "dossier_data" => [],
            "client_data" => []
        ];

        $forms = DB::table('forms')->where('fiche_id', $dossier->fiche_id)->get();

        foreach ($forms as $form) {
            $datas = DB::table('forms_data')->where('dossier_id', $dossier->id)->get();

            foreach ($datas as $data) {
                if (!isset($results["form_data"][$data->form_id])) {
                    $results["form_data"][$data->form_id] = [];
                }
                if(!empty($data->meta_value)) {
                    $results["form_data"][$data->form_id][$data->meta_key] = $data->meta_value;

                }
            }
        }



        $datas = DB::table('dossiers_data')->where('dossier_id', $dossier->id)->get();
        $results["dossiers_data"][0] = [];
        foreach ($datas as $data) {
       
            $results["dossiers_data"][0][$data->meta_key] = $data->meta_value;
        }


        $beneficiaire = DB::table('beneficiaires')->where('id', $dossier->beneficiaire_id)->first();
        $results["beneficiaire_data"][0] = [];
        if ($beneficiaire) {
            foreach ($beneficiaire as $key => $value) {
                $results["beneficiaire_data"][0][$key] = $value;
            }
        }

        return $results;
    }
}



function strtoupper_extended($string)
{
    // Mapping of accented characters to their uppercase equivalents
    $accents = [
        'à' => 'À',
        'á' => 'Á',
        'â' => 'Â',
        'ã' => 'Ã',
        'ä' => 'Ä',
        'å' => 'Å',
        'æ' => 'Æ',
        'ç' => 'Ç',
        'è' => 'È',
        'é' => 'É',
        'ê' => 'Ê',
        'ë' => 'Ë',
        'ì' => 'Ì',
        'í' => 'Í',
        'î' => 'Î',
        'ï' => 'Ï',
        'ð' => 'Ð',
        'ñ' => 'Ñ',
        'ò' => 'Ò',
        'ó' => 'Ó',
        'ô' => 'Ô',
        'õ' => 'Õ',
        'ö' => 'Ö',
        'ø' => 'Ø',
        'ù' => 'Ù',
        'ú' => 'Ú',
        'û' => 'Û',
        'ü' => 'Ü',
        'ý' => 'Ý',
        'þ' => 'Þ',
        'ÿ' => 'Ÿ'
    ];

    // First, use the standard strtoupper to convert ASCII characters
    $string = strtoupper($string);

    // Then, replace accented characters with their uppercase equivalents
    $string = strtr($string, $accents);

    return $string;
}


if (!function_exists('generate_num_devis')) {

    function generate_num_devis($client_id, $dossier_id)
    {
        $check_num_devis = DB::table('dossiers_data')->where('dossier_id', $dossier_id)->where('meta_key', 'numero_devis')->first();
        if (!isset($check_num_devis)) {

            $check_devis = DB::table('numerotation_devis')
                ->where('year', date('Y', strtotime('now')))
                ->where('month', date('m', strtotime('now')))
                ->where('client_id', $client_id)->first();

            if (!isset($check_devis)) {
                DB::table('numerotation_devis')
                    ->insert(['year' => date('Y', strtotime('now')), 'month' => date('m', strtotime('now')), 'client_id' => $client_id, 'increment' => 1]);
                $increment = 1;
            } else {

                $increment = $check_devis->increment + 1;

                DB::table('numerotation_devis')->where('year', date('Y', strtotime('now')))
                    ->where('month', date('m', strtotime('now')))->where('client_id', $client_id)->update(['increment' => $increment]);
            }




            $increment = str_pad($increment, 5, '0', STR_PAD_LEFT);
            $num = "DE-" . date('Y', strtotime('now')) . date('m', strtotime('now')) . $increment;

            DB::table('dossiers_data')->insert(['dossier_id' => $dossier_id, 'meta_key' => 'numero_devis', 'meta_value' => $num]);
        }

    }
}

function stringToColorCode($str)
{
    // Generate a hash from the string
    $hash = md5($str);

    // Extract the first 6 characters from the hash
    $color = substr($hash, 0, 6);

    // Return the color code
    return '#' . $color;
}



function couleur_menage($couleur)
{
    // Generate a hash from the string
    $array = [
        'bleu' => 'bleu',
        'jaune' => 'jaune',
        'violet' => 'violet',
        'rose' => 'rose',
    ];
    return $array[$couleur] ?? '';
}



function texte_menage($couleur)
{
    // Generate a hash from the string
    $array = [
        'bleu' => 'Bleu : Très modestes',
        'jaune' => 'Jaune : Modestes',
        'violet' => 'Violet : Intermédiaires',
        'rose' => 'Rose : Aisés',
    ];
    return $array[$couleur] ?? '';
}


function decode_if_json($value)
{
    if ($value == '') {
        return [];
    }
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        // Check if json_decode succeeded
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
    }

    if (is_array($value)) {
        return $value;
    }

    return $value;
}

function convertToArray($data)
{
    if (is_object($data)) {
        $data = (array) $data;
    }
    if (is_array($data)) {
        return array_map('convertToArray', $data);
    }
    return $data;
}

function getDocumentStatuses($dossier_id, $last_etape_order = 1)
{
    // Initialize arrays to hold documents in each category
    $missingDocs = [];
    $waitingForSignatureDocs = [];
    $signedDocs = [];
    $noSignatureRequested = [];

    // Retrieve documents and necessary data using an optimized query
    // Step 1: Get the minimum forms_config.id per name
    $formsConfigMinIds = DB::table('forms_config')
        ->select(DB::raw('MIN(id) as id'))
        ->whereIn('type', ['generate', 'fillable', 'upload', 'generateConfig'])
        ->groupBy('name');

    // Step 2: Join the forms_config table with the subquery of minimum ids
    $results = DB::table('forms_config')
        ->joinSub($formsConfigMinIds, 'fc_min', function ($join) {
            $join->on('forms_config.id', '=', 'fc_min.id');
        })
        ->leftJoin('forms_data as forms_data_meta', function ($join) use ($dossier_id) {
            $join->on('forms_config.name', '=', 'forms_data_meta.meta_key')
                ->where('forms_data_meta.dossier_id', $dossier_id);
        })
        ->leftJoin('forms_data as forms_data_signature_request_id', function ($join) use ($dossier_id) {
            $join->on('forms_data_signature_request_id.form_id', '=', 'forms_config.form_id')
                ->where('forms_data_signature_request_id.dossier_id', $dossier_id)
                ->where('forms_data_signature_request_id.meta_key', '=', 'signature_request_id');
        })
        ->leftJoin('forms_data as forms_data_signature_status', function ($join) use ($dossier_id) {
            $join->on('forms_data_signature_status.form_id', '=', 'forms_config.form_id')
                ->where('forms_data_signature_status.dossier_id', $dossier_id)
                ->where('forms_data_signature_status.meta_key', '=', 'signature_status');
        })
        ->join('forms', 'forms.id', '=', 'forms_config.form_id')
        ->join('etapes', 'etapes.id', '=', 'forms.etape_id')
        ->orderBy('etapes.order_column')
        ->select([
            'forms_config.id',
            'forms_config.name',
            'forms_config.required',
            'forms_config.type',
            'forms_config.options',
            'forms_config.title',
            'forms_data_meta.meta_value as meta_value',
            'forms_data_signature_request_id.meta_value as signature_request_id',
            'forms_data_signature_status.meta_value as signature_status',
            'forms.id as form_id',
            'etapes.order_column',
        ])
        ->get();

    // Process each document to determine its status
    foreach ($results as $result) {
        $options = json_decode($result->options, true);
        $doc = get_object_vars($result);
        $doc['options'] = $options;
        $doc['last_etape_order'] = $last_etape_order;
        // Check if the document should be processed

        

    

        if ($last_etape_order >= $doc['order_column']) {
            if ($doc['required'] == 1 || ($doc['required'] == 0 && !empty($doc['meta_value']))) {
                if (!empty($doc['meta_value'])) {

                    if (isset($doc['options']['signable']) && $doc['options']['signable'] === 'true') {
                        // Check the signature status
                        if (!empty($doc['signature_request_id'])) {
                            if (!empty($doc['signature_status'])) {
                                if ($doc['signature_status'] == 'finish') {
                                    // Document is signed
                                    $signedDocs[] = $doc['title'];
                                } elseif ($doc['signature_status'] == 'ongoing') {
                                    // Document is waiting for signature
                                    $waitingForSignatureDocs[] = $doc['title'];
                                } else {
                                    // Document is missing or not generated
                                    $missingDocs[] = $doc['title'];
                                }
                            } else {
                                // Signature status is not set, consider as missing
                                $noSignatureRequested[] = $doc['title'];
                            }
                        } else {
                            // Signature request ID is not set, consider as missing
                            $noSignatureRequested[] = $doc['title'];
                        }
                    } else {
                        // Document is not signable but is considered signed
                        $signedDocs[] = $doc['title'];
                    }
                } else {
                    // Document is missing or not generated
                    $missingDocs[] = $doc['title'];
                }
            }
        }
    }

    // Prepare the result with counts and document lists
    $resultData = [
        'missingDocs' => [
            'count' => count($missingDocs),
            'docs' => $missingDocs,
        ],
        'waitingForSignatureDocs' => [
            'count' => count($waitingForSignatureDocs),
            'docs' => $waitingForSignatureDocs,
        ],
        'signedDocs' => [
            'count' => count($signedDocs),
            'docs' => $signedDocs,
        ],
        'noSignatureRequested' => [
            'count' => count($noSignatureRequested),
            'docs' => $noSignatureRequested,
        ],
    ];
    $update = DB::table('dossiers_data')->updateOrInsert(
        [
            'dossier_id' => '' . $dossier_id . '',
            'meta_key' => 'docs'
        ],
        [
            'meta_value' => '' . json_encode($resultData) . '',
            'created_at' => now(),
            'updated_at' => now()
        ]
    );
    return $resultData;
}

if (!function_exists('change_status')) {
    function change_status($key, $value, $dossier_id)
    {
  
        // Dossier::where('id', $dossier_id)->update([ 'status_id' => $value]);

    if($key=='status_id' && $value==15) {
        Dossier::where('id', $dossier_id)->update(['etape_number' => 1, 'status_id' => $value]);
        Dossier::where('id', $dossier_id)->update(['annulation' => 1, 'status_id' => $value]);
    } 
    if($key=='status_id' && $value==37) {
        Dossier::where('id', $dossier_id)->update(['etape_number' => 20, 'status_id' => $value]);

        Dossier::where('id', $dossier_id)->update(['annulation' => 1, 'status_id' => $value]);
    } 
    }
}


