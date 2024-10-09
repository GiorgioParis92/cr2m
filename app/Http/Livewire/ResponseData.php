<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\Dossier;
use App\Models\Client;

class ResponseData extends Component
{
    public $dossierId;
    public $responseData = null;

    public function mount($dossierId)
    {
        $this->dossierId = $dossierId;
    }

    public function render()
    {
        return view('livewire.response-data');
    }

    public function loadResponseData()
    {
        $dossier = Dossier::find($this->dossierId);
        $mar=Client::where('id',$this->dossier->mar)->first();

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
                $this->responseData = $response->json();
            } else {
                $statusCode = $response->status();
                $errorBody = $response->body();
                $this->responseData = "Error ({$statusCode}): {$errorBody}";
            }
        }
    }
}
