<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;


if (!function_exists('format_date')) {
    function format_date($value)
    {
        $value=str_replace('/','-',$value);
        dump($value);
        $value=str_replace('.','-',$value);
        dump($value);
        $value=str_replace(' ','-',$value);
        dump($value);
       
        $value=strtotime($value);
      
        $value=date("d/m/Y",$value);
        return $value;
    }
}

if (!function_exists('strtotime_date')) {
    function strtotime_date($value)
    {
        $value=str_replace('/','-',$value);
        $value=str_replace('.','-',$value);
        $value=str_replace(' ','-',$value);
        $value=strtotime($value);
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
    function load_all_dossier_data($dossier) {
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

        $beneficiaire = DB::table('beneficiaires')->where('id', $dossier->beneficiaire_id)->first();

        if ($beneficiaire) {
            foreach ($beneficiaire as $key => $value) {
                $results["beneficiaire_data"][$key] = $value;
            }
        }
        
        return $results;
    }
}



function strtoupper_extended($string) {
    // Mapping of accented characters to their uppercase equivalents
    $accents = [
        'à' => 'À', 'á' => 'Á', 'â' => 'Â', 'ã' => 'Ã', 'ä' => 'Ä', 'å' => 'Å',
        'æ' => 'Æ', 'ç' => 'Ç', 'è' => 'È', 'é' => 'É', 'ê' => 'Ê', 'ë' => 'Ë',
        'ì' => 'Ì', 'í' => 'Í', 'î' => 'Î', 'ï' => 'Ï', 'ð' => 'Ð', 'ñ' => 'Ñ',
        'ò' => 'Ò', 'ó' => 'Ó', 'ô' => 'Ô', 'õ' => 'Õ', 'ö' => 'Ö', 'ø' => 'Ø',
        'ù' => 'Ù', 'ú' => 'Ú', 'û' => 'Û', 'ü' => 'Ü', 'ý' => 'Ý', 'þ' => 'Þ',
        'ÿ' => 'Ÿ'
    ];

    // First, use the standard strtoupper to convert ASCII characters
    $string = strtoupper($string);

    // Then, replace accented characters with their uppercase equivalents
    $string = strtr($string, $accents);
    
    return $string;
}
