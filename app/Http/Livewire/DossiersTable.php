<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Dossier;
use App\Models\Client;
use App\Models\ClientLinks;
use App\Models\Beneficiaire;
use App\Models\Fiche;

use App\Models\Etape;
use App\Models\Status;
use App\Models\RdvStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class DossiersTable extends Component
{
    public $time;
    public $search = '';
    public $dossiers = [];

    // Add public properties for filters
    public $clientName = '';
    public $precarite = '';
    public $etape = '';
    public $mandataire = '';
    public $installateur = '';
    public $accompagnateur = '';
    public $statut = '';
    public $dpt = '';

    public function mount()
    {
        $this->time = now()->format('H:i:s');
        $this->loadDossiers();

        // Load other data for select options
        $this->etapes = Etape::orderBy('order_column')->get();
        $this->mars = Client::where('type_client', 1)->get();
        $this->financiers = Client::where('type_client', 2)->get();
        $this->installateurs = Client::where('type_client', 3)->get();
        $this->fiches = Fiche::all();

        $this->departments = DB::table('departement')->get()->map(function ($department) {
            return (array) $department;
        })->toArray();
        $this->status = Status::select(DB::raw('MIN(id) as id'), 'status_desc')
            ->groupBy('status_desc')
            ->get();
    }

    public function updated($propertyName)
    {
        // Whenever any filter property is updated, reload the dossiers
        $this->loadDossiers();

        $this->emit('dossierDataUpdated', $this->dossiers);

    }

    public function loadDossiers()
    {
        $user = auth()->user();
        $client = Client::where('id', $user->client_id)->first();

        $dossiersQuery = Dossier::with(['beneficiaire', 'fiche', 'etape', 'status', 'mar']);

        // Apply client-specific filtering
        $userClientId = auth()->user()->client_id;
        if ($userClientId > 0) {
            switch ($client->type_client) {
                case 1:
                    $dossiersQuery->where('client_id', $userClientId);
                    break;
                case 2:
                    $dossiersQuery->where('mandataire_financier', $userClientId);
                    break;
                case 3:
                    $dossiersQuery->where('installateur', $userClientId);
                    break;
                case 4:
                    $hasChild = ClientLinks::where('client_parent', $client->id)->pluck('client_id')->toArray();
                    $dossiersQuery->where(function ($query) use ($userClientId, $hasChild) {
                        $query->where('installateur', $userClientId)
                            ->orWhereIn('installateur', $hasChild);
                    });
                    break;
            }
        }

        // Apply search and filters
        if ($this->search) {
            $dossiersQuery->whereHas('beneficiaire', function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    ->orWhere('adresse', 'like', '%' . $this->search . '%')
                    ->orWhere('telephone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->clientName) {

            $dossiersQuery->whereHas('beneficiaire', function ($query) {
                $query->where('nom', 'like', '%' . $this->clientName . '%');
            });
        }

        if ($this->precarite) {
            $dossiersQuery->whereHas('beneficiaire', function ($query) {
                $query->where('menage_mpr', $this->precarite);
            });
        }
        if ($this->statut) {
            $dossiersQuery->whereHas('status', function ($query) {
                $query->where('status_desc', $this->statut);
            });
        }
        if ($this->etape) {

            $dossiersQuery->whereHas('etape', function ($query) {
                $query->where('etape_number', $this->etape);
            });
        }
        if ($this->accompagnateur) {

            $dossiersQuery->where('mar', $this->accompagnateur);
            ;
        }
        if ($this->installateur) {

            $dossiersQuery->where('installateur', $this->installateur);
            ;
        }
        if ($this->mandataire) {

            $dossiersQuery->where('mandataire_financier', $this->mandataire);
            ;
        }
        if ($this->dpt) {

            $dossiersQuery->whereHas('beneficiaire', function ($query) {
                $query->where('cp', 'like', '' . $this->dpt . '%');
            });
        }



        if (auth()->user()->client_id > 0 && ($client->type_client == 1)) {
            $dossiersQuery->where('client_id', auth()->user()->client_id);
        }
        if (auth()->user()->client_id > 0 && ($client->type_client == 2)) {
            $dossiersQuery->where('mandataire_financier', auth()->user()->client_id);
        }
        if (auth()->user()->client_id > 0 && ($client->type_client == 3)) {
            $dossiersQuery->where('installateur', auth()->user()->client_id);
        }

        if (auth()->user()->client_id > 0 && ($client->type_client == 4)) {
            $has_child = ClientLinks::where('client_parent', $client->id)->pluck('client_id')->toArray();

            $dossiersQuery->where(function ($query) use ($has_child) {
                $query->where('installateur', auth()->user()->client_id)
                    ->orWhereIn('installateur', $has_child);
            });
        }
        // Fetch the filtered results
        $dossiers = $dossiersQuery->get()->map(function ($dossier) {
            return [
                'id' => $dossier->id,
                'date_creation' => $dossier->created_at,
                'dossier_url' => route('dossiers.show', $dossier->folder),
                'beneficiaire' => [
                    'nom' => $dossier->beneficiaire->nom ?? '',
                    'prenom' => $dossier->beneficiaire->prenom ?? '',
                    'numero_voie' => $dossier->beneficiaire->numero_voie ?? '',
                    'adresse' => $dossier->beneficiaire->adresse ?? '',
                    'cp' => $dossier->beneficiaire->cp ?? '',
                    'ville' => $dossier->beneficiaire->ville ?? '',
                    'telephone' => $dossier->beneficiaire->telephone ?? '',
                    'email' => $dossier->beneficiaire->email ?? '',
                ],
                'etape' => $dossier->etape->etape_icon,
                'etape_style' => $dossier->etape->etape_style,
                'etape_desc' => $dossier->etape->etape_desc,
                'couleur_menage' => couleur_menage($dossier->beneficiaire->menage_mpr),
                'texte_menage' => texte_menage($dossier->beneficiaire->menage_mpr),
                'accompagnateur' => $dossier->mar_client->client_title ?? '',
                'accompagnateur_img' => $dossier->mar_client->main_logo ?? '',
                'mandataire' => $dossier->mandataire_financier_client->client_title ?? '',
                'mandataire_img' => $dossier->mandataire_financier_client->main_logo ?? '',
                'installateur' => $dossier->installateur_client->client_title ?? '',
                'installateur_img' => $dossier->installateur_client->main_logo ?? '',
                'statut' => $dossier->status->status_desc ?? '',
                'statut_style' => $dossier->status->status_desc ?? '',
            ];
        });

        // Update the component's dossier data
        $this->dossiers = $dossiers->toArray();

    }


    public function render()
    {
        return view('livewire.dossiers-table');
    }
}
