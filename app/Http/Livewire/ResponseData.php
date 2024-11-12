<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\Dossier;
use App\Models\Client;

class ResponseData extends Component
{
    public $dossierId;
    public $dossier;
    public $responseData = null;

    public function mount($dossierId)
    {
        $this->dossierId = $dossierId;
        $this->dossier = Dossier::with('beneficiaire', 'fiche', 'etape', 'status','mar_client')->find($this->dossierId);
    }

    public function render()
    {
        return view('livewire.response-data');
    }

    public function loadResponseData()
    {
        $mar=Client::where('id',$this->dossier->mar)->first();
       
        if($mar) {
            $login=$mar->anah_login;
            $password=$mar->anah_password;
        }
        if ($this->dossier && $this->dossier->reference_unique) {
            $url = url('/api/scrapping');
            $token = 'qlcb1m8AlZU8dteqvYWFxrehJ2iGlGvUbinQhUNOa3yqjizldp0ARNiCDmsl';

            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->post($url, [
                    'reference_unique' => $this->dossier->reference_unique,
                    'login' => $login,
                'password' => $password,
                ]);
             
            if ($response->successful()) {
                $this->responseData = $response->json();
                $this->checkAndUpdateStatus();  // Call to save the status

            } else {
                $statusCode = $response->status();
                $errorBody = $response->body();
                $this->responseData = "Error ({$statusCode}): {$errorBody}";
            }
        }
    }


    
    public function checkAndUpdateStatus()
    {
        if($this->responseData) {
        foreach ($this->responseData as $tag => $data) {
            if (!empty($data['elements'])) {
                foreach ($data['elements'] as $element) {
                    \DB::table('dossiers_data')->updateOrInsert(
                        [
                            'dossier_id' => $this->dossier->id,
                            'meta_key' => $tag
                        ],
                        [
                            'meta_value' => $value ?? '',
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
                                    'dossier_id' => $this->dossier->id,
                                    'meta_key' => 'subvention'
                                ],
                                [
                                    'meta_value' => $value ?? '',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]
                            );
                            \DB::table('dossiers_data')->updateOrInsert(
                                [
                                    'dossier_id' => $this->dossier->id,
                                    'meta_key' => 'Statut du dossier'
                                ],
                                [
                                    'meta_value' => 'Subvention accordée' ?? '',
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
