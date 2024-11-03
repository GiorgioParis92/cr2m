<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Dossier;
class DashboardAudit extends Component
{

    public $liste;
    public function mount()
    {
        // $this->loadDossiers();
        $this->loadDossiers();

    }
    public function refresh()
    {
       $this->loadDossiers();
    }


    public function loadDossiers()
    {
        $user = auth()->user();
    
        $dossiersQuery = Dossier::select('id','etape_number', 'beneficiaire_id', 'etape_id', 'status_id', 'folder', 'installateur')
            ->with([
                'beneficiaire:id,nom,prenom,numero_voie,adresse,cp,ville',
                'etape:id,etape_icon,etape_desc',
                'status:id,status_style,status_desc',
            ])
            ->where('etape_number', 8)
            ->where('status_id','!=',15)
            // ->where('annulation','!=',1)
            
            ->where(function ($query) {
                $query->whereDoesntHave('formsData', function ($query) {
                    $query->where('meta_key', 'audit');
                })
                ->orWhereHas('formsData', function ($query) {
                    $query->where('meta_key', 'audit')
                          ->where('meta_value', '');
                });
            });
    
        if ($user->client_id > 0 && $user->client->type_client == 3) {
            $dossiersQuery->where('installateur', $user->client_id);
        }
        
        // Use cursor or lazy collection
        $this->liste = $dossiersQuery->get();
        dd($this->liste);
    }
    
    
    public function render()
    {
        return view('livewire.dashboard-audit');
    }
}
