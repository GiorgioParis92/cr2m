<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Dossier;
class DashboardDevis extends Component
{

    public $liste;
    public function mount()
    {
        $dossiers = Dossier::with('beneficiaire', 'dossiersData', 'formsData', 'etape', 'status')
        ->where('etape_number', 12)
        ->where(function ($query) {
            $query->whereHas('formsData', function ($query) {
                $query->where('meta_key', 'audit')
                      ->where('meta_value', '!=', '');
            })
            ->orWhereDoesntHave('formsData', function ($query) {
                $query->where('meta_key', 'audit');
            });
        })

        ->where(function ($query) {
            $query->whereHas('formsData', function ($query) {
                $query->where('meta_key', 'devis')
                      ->where('meta_value', '=', '');
            })
            ->orWhereHas('formsData', function ($query) {
                $query->where('meta_key', 'devis');
            });
        });



    // Check if the user is an installer and apply the filter based on the 'installateur' column
    if (auth()->user()->client_id > 0 && auth()->user()->client->type_client == 3) {
        $dossiers = $dossiers->where('installateur', auth()->user()->client_id);
    }

    // Retrieve the filtered dossiers
    $dossiers = $dossiers->get();
        // dd($dossiers);
        $this->liste = $dossiers;
    }

    public function render()
    {
        return view('livewire.dashboard-devis');
    }
}
