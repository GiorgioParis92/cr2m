<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\UserPermission;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;





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

        if ($userPermission && $userPermission->is_active == 1) {
            return true;
        }


        if ($userPermission) {
            if ($userPermission->is_active == 0) {
                return false;
            }
        } else {

            if(isset($user->client->type_client)) {
                $defaultPermission = DB::table('default_permission')
                ->where('type_client', $user->client->type_client)
                ->where('permission_name', $permission_name)
                
                    ->first();
    
            
                if ($defaultPermission && $defaultPermission->is_active == 0) {
                    return false;
                }
            }


            $defaultPermission = DB::table('default_permission')->where('type_id', $user->type_id)
                ->where('permission_name', $permission_name)
                
                ->first();


            if ($defaultPermission && $defaultPermission->is_active == 0) {
                return false;
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
        if ($key === 'file' && $value != '{}' && $value != null) {

            $multipartData[$key] = new CURLFile($value->getRealPath(), $value->getClientMimeType(), $value->getClientOriginalName());
        } else {
            $multipartData[$key] = $value;
        }
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $multipartData);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

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
                $results["form_data"][$data->form_id][$data->meta_key] = $data->meta_value;
            }
        }



        $datas = DB::table('dossiers_data')->where('dossier_id', $dossier->id)->get();

        foreach ($datas as $data) {
            if (!isset($results["dossiers_data"])) {
                $results["dossiers_data"] = [];
            }
            $results["dossiers_data"][$data->meta_key] = $data->meta_value;
        }


        $beneficiaire = DB::table('beneficiaires')->where('id', $dossier->beneficiaire_id)->first();

        if ($beneficiaire) {
            foreach ($beneficiaire as $key => $value) {
                $results["beneficiaire_data"][$key] = $value;
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

    function generate_num_devis($client_id,$dossier_id)
    {
        $check_num_devis = DB::table('dossiers_data')->where('dossier_id', $dossier_id)->where('meta_key', 'numero_devis')->first();
        if (!isset($check_num_devis)) {
            
        $check_devis = DB::table('numerotation_devis')
        ->where('year', date('Y', strtotime('now')))
        ->where('month', date('m', strtotime('now')))
        ->where('client_id',$client_id)->first();

        if (!isset($check_devis)) {
            DB::table('numerotation_devis')
            ->insert(['year' => date('Y', strtotime('now')), 'month' => date('m', strtotime('now')), 'client_id' => $client_id,'increment'=>1]);
        $increment=1;
        } else {

        $increment=$check_devis->increment+1;

        DB::table('numerotation_devis')->where('year', date('Y', strtotime('now')))
        ->where('month', date('m', strtotime('now')))->where('client_id',$client_id)->update(['increment'=>$increment]);
        }



       
            $increment = str_pad($increment, 5, '0', STR_PAD_LEFT);
        $num="DE-".date('Y', strtotime('now')).date('m', strtotime('now')).$increment;

         DB::table('dossiers_data')->insert(['dossier_id' => $dossier_id, 'meta_key' => 'numero_devis', 'meta_value' => $num]);
        }

}
}

function stringToColorCode($str) {
    // Generate a hash from the string
    $hash = md5($str);
    
    // Extract the first 6 characters from the hash
    $color = substr($hash, 0, 6);
    
    // Return the color code
    return '#' . $color;
}



function couleur_menage($couleur) {
    // Generate a hash from the string
$array=[
    'bleu'=>'Très modestes',
    'jaune'=>'Très modestes',
];
return $array[$couleur] ?? '';
}
