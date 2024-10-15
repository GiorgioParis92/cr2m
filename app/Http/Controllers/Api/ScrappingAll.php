<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use App\Models\Dossier;
use App\Models\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class ScrappingAll extends Controller
{



    public function index()
    {
        set_time_limit(30000);  // Set maximum execution time to 300 seconds

        $dossiers=Dossier::with('beneficiaire', 'fiche', 'etape', 'status','mar_client')->get();
        foreach($dossiers as $dossier) {
            $mar=Client::where('id',$dossier->mar)->first();
       
            if($mar) {
                $login=$mar->anah_login;
                $password=$mar->anah_password;
            }
        
            if ($dossier && $dossier->reference_unique) {
                $url = url('/api/scrapping');
                $token = 'qlcb1m8AlZU8dteqvYWFxrehJ2iGlGvUbinQhUNOa3yqjizldp0ARNiCDmsl';
    
                $response = Http::withToken($token)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->post($url, [
                        'reference_unique' => $dossier->reference_unique,
                        'login' => $login,
                    'password' => $password,
                    ]);
                if ($response->successful()) {
                    $responseData = $response->json();

                    $this->checkAndUpdateStatus($responseData,$dossier);  // Call to save the status

                } else {
                    $statusCode = $response->status();
                    $errorBody = $response->body();
                    $responseData = "Error ({$statusCode}): {$errorBody}";
                }
                sleep(5);

            }
        }
       
    }


    
    public function checkAndUpdateStatus($responseData,$dossier)
    {
        if($responseData) {
        foreach ($responseData as $tag => $data) {
            if (!empty($data['elements'])) {
                foreach ($data['elements'] as $element) {
                    // Assuming you want to save the status as 'text'
          
                        \DB::table('dossiers_data')->updateOrInsert(
                            [
                                'dossier_id' => $dossier->id,
                                'meta_key' => $tag
                            ],
                            [
                                'meta_value' => $element['text'] ?? '',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                    }
                
            }
        }
    }
}
}
