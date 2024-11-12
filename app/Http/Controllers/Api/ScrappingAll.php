<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use App\Models\Dossier;
use App\Models\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class ScrappingAll extends Controller
{



    public function index()
    {
        set_time_limit(30000);  // Set maximum execution time to 300 seconds
    
        $updatedDossierIds = []; // Initialize the array
    
        $dossiers = Dossier::with('beneficiaire', 'fiche', 'etape', 'status', 'mar_client')
            ->where('updated','!=',1)    
            ->get();       
            
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
                   
                    $this->checkAndUpdateStatus($responseData, $dossier);  // Call to save the status
                    Dossier::where('id', $dossier->id)->update([
                        'updated' => 1,
                    ]);
    
                    // Add the dossier ID to the array
                    $updatedDossierIds[] = $dossier->id;
                } else {
                    $statusCode = $response->status();
                    $errorBody = $response->body();
                    $responseData = "Error ({$statusCode}): {$errorBody}";
                }
                sleep(5);
            }
        }
    
        // Return the response
        if (!empty($updatedDossierIds)) {
            return response()->json(['dossier_ids' => $updatedDossierIds]);
        } else {
            return response()->json(['message' => 'No dossiers were updated.']);
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


                        $value=$element['text'];

                        if (strpos($value, 'accordée') !== false) {
                            preg_match('/\d+/', str_replace(' ', '', $value), $matches);
                            $value = isset($matches[0]) ? $matches[0] : null;
                            $tag;
                            if($value>0) {
                                \DB::table('dossiers_data')->updateOrInsert(
                                    [
                                        'dossier_id' => $dossier->id,
                                        'meta_key' => 'subvention'
                                    ],
                                    [
                                        'meta_value' => $value ?? '',
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
}
}
