<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\{
    Dossier,
    Etape,
    DossiersActivity,
    User,
    Form,
    Forms,
    FormConfig,
    Rdv,
    RdvStatus,
    Client,
    Card
};
use App\FormModel\{
    FormConfigHandler,
    EtapeValidator
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\CardCreationService;

class DossierLivewireNew extends Component
{
    public $time;
    public $docs = [];
    public $etape_display = [];
    public $etapes = [];
    public $forms_configs = [];
    protected $forms_configs_saved = [];
    public $global_data = [];
    public $tab;
    public $last_etape;
    public $last_etape_order;
    public $score_info;
    public $formData = [];
    public $validators = [];
    public $steps = [];
    public $auditeurs = [];
    public $departments = [];
    public $rdv_status = [];
    public $mars = [];
    public $financiers = [];
    public $installateurs = [];
    public $technicien = [];
    public $technicien2 = [];
    public $responseData = '';
    public $dossier;
    public $set_form;
    public $config;

    protected $listeners = ['fileUploaded' => 'handleFileUploaded'];




    public function mount($id)
    {
        $this->time = now()->format('H:i:s');

        $this->dossier = Dossier::with('beneficiaire', 'fiche', 'etape', 'status', 'mar_client')->find($id);

        if (!$this->dossier) {
            abort(404, 'Dossier not found');
        }

        $this->load_etapes();

        $currentEtape = Etape::find($this->dossier->etape_number);
        $this->dossier->order_column = $currentEtape ? $currentEtape->order_column : null;

        // Fetch distinct etapes using a subquery
        $distinctEtapes = Form::select('etape_number', DB::raw('MIN(id) as min_id'))
            ->groupBy('etape_number');

        $this->etapes = Form::joinSub($distinctEtapes, 'distinctEtapes', function ($join) {
                $join->on('forms.id', '=', 'distinctEtapes.min_id');
            })
            ->join('etapes', 'forms.etape_number', '=', 'etapes.id')
            ->orderBy('etapes.order_column')
            ->get()
            ->toArray();

        // Initialize validators for each etape
        foreach ($this->etapes as $etape) {
            $this->validators[$etape['etape_id']] = new EtapeValidator($etape['etape_id']);
        }

        $this->determineLastEtape();

        if ($this->last_etape) {
            $this->setTab($this->last_etape);
            $this->emit('setTab');
        }


        $this->auditeurs = User::where('type_id', 4)->get()->toArray();
        $this->departments = DB::table('departement')->get()->toArray();
        $this->rdv_status = RdvStatus::all()->toArray();

        $this->financiers = Client::where('type_client', 2)->get()->toArray();
        $this->installateurs = Client::where('type_client', 3)->get()->toArray();
        $stepData = DB::table('dossiers_data')
            ->join('users', 'dossiers_data.user_id', '=', 'users.id')  // Join with the users table
            ->where('dossier_id', $id)
            ->where('meta_key', 'like', '%step_%')
            ->get(['dossiers_data.meta_key', 'dossiers_data.meta_value', 'dossiers_data.user_id', 'users.name as user_name']);  // Select user name

        $this->steps = [];
        foreach ($stepData as $item) {
            $this->steps[$item->meta_key] = [
                'meta_value' => $item->meta_value,
                'user_id' => $item->user_id,
                'user_name' => $item->user_name,  // Add user name to the result
            ];
        }

        // $this->get_docs();
    }

    public function hydrate()
    {
 
       

            
        // $this->get_docs();
     
    }
    public function load_etapes()
    {
        $distinctEtapes = Form::select('etape_number', DB::raw('MIN(id) as min_id'))
            ->groupBy('etape_number');

        $this->etapes = Form::joinSub($distinctEtapes, 'distinctEtapes', function ($join) {
            $join->on('forms.id', '=', 'distinctEtapes.min_id');
        })
            ->join('etapes', 'forms.etape_number', '=', 'etapes.id')
            ->orderBy('etapes.order_column')
            ->get()
            ->toArray();

    }


    public function toggleDossier($dossierId)
    {
        // Find the dossier by ID
        $dossier = Dossier::find($dossierId);

        if ($dossier) {

            // Toggle the dossier's 'annulation' field based on its current state
            $dossier->annulation = ($dossier->annulation == 0) ? 1 : 0;
            $dossier->save();

            // Optionally, you can add flash messages or notifications to indicate success
            if ($dossier->annulation == 0) {
                session()->flash('message', 'Dossier rétabli.');
            } else {
                session()->flash('message', 'Dossier annulé.');
            }

            $this->emit('showFlashMessage');  // Emit event to show flash message

            // Optionally, you can refresh the page or update any necessary data
            $this->dossier = $dossier;
        }
    }

    public function update_installateur($installateurId)
    {
        // Update the dossier's 'installateur' field with the selected value
        $this->dossier->installateur = $installateurId;
        $this->dossier->save();

        // Optionally, add a flash message or other response
        session()->flash('message', 'Installateur mis à jour avec succès.');
        $this->emit('showFlashMessage');  // Emit event to show flash message

    }
    public function get_docs()
    {
        $results = DB::table('forms_config')
            ->leftJoin('forms_data', function($join) {
                $join->on('forms_config.name', '=', 'forms_data.meta_key')
                     ->where('forms_data.dossier_id', $this->dossier->id);
            })
            ->join('forms', 'forms.id', '=', 'forms_config.form_id')
            ->join('etapes', 'etapes.id', '=', 'forms.etape_id')
            ->whereIn('forms_config.type', ['generate', 'fillable', 'upload', 'generateConfig'])
            ->whereIn('forms_config.id', function($query) {
                $query->select(DB::raw('MIN(id)'))
                      ->from('forms_config')
                      ->groupBy('name');
            })
            ->orderBy('etapes.order_column')
            ->get();
    
        $this->docs = [];
    
        foreach ($results as $result) {
            $options = json_decode($result->options, true);
            $doc = (array) $result;
            $doc['options'] = $options;
            $doc['last_etape_order'] = $this->last_etape_order ?? 1;
    
            if (isset($options['signable']) && $options['signable'] === "true") {
                $doc['additional_data'] = DB::table('forms_data')
                    ->where('dossier_id', $this->dossier->id)
                    ->where('form_id', $result->form_id)
                    ->get()
                    ->toArray();
            }
    
            $this->docs[] = $doc;
        }
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $etape_display = Etape::find($tab);
        $this->etape_display = $etape_display ? $etape_display->toArray() : [];
        $this->forms='';
        $this->config=[];

        $this->load_forms($tab);

        // $firstKey = $this->reinitializeFormsConfigs();
        // $this->display_form($firstKey);
        // $firstKey = array_key_first($this->forms_configs);
        // $this->display_form($firstKey);

        $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);
        $this->emit('setTab', ['forms_configs' => $this->forms_configs]);

        foreach ($this->forms_configs as $key => $config) {
            DB::table('messages_suivi')
                ->where('user_id', auth()->id())
                ->whereIn('message_id', function ($query) use ($key) {
                    $query->select('id')
                        ->from('messages')
                        ->where('dossier_id', $this->dossier->id)
                        ->where('form_id', $key);
                })
                ->delete();
        }
    }

    private function determineLastEtape()
    {
        $this->last_etape = 1;
        foreach ($this->etapes as $etape) {
            if (
                (is_user_allowed($etape['etape_name']) && !is_user_forbidden($etape['etape_name']))
                
                && $etape['order_column'] <= $this->dossier->etape->order_column) {
                $this->last_etape = $etape['id'];
                $this->last_etape_order = $etape['order_column'];
            }
        }
    }


    public function load_forms($etape_id) {
        $this->forms=Forms::where('etape_id',$etape_id)->get();
     
    }


    public function set_form($id) {
        $this->set_form=$id;
        $this->config=FormConfig::where('form_id',$id)->orderBy('ordering')->get()->toArray();
  
    }

    public function handleFileUploaded($request)
    {



        $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);

    }

    public function delete_doc($form_id, $tag)
    {
        
        $update = DB::table('forms_data')->where(
            [
                'dossier_id' => '' . $this->dossier->id . '',
                'form_id' => '' . $form_id . '',
            ],
       
        )->delete();
       
    }
    public function render()
    {
        return view('livewire.dossier-livewire-new', [
            'dossier' => $this->dossier,

        ]);
    }


}
