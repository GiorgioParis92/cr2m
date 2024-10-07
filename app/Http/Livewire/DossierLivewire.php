<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\{
    Dossier, Etape, DossiersActivity, User, Form, Rdv, RdvStatus, Client
};
use App\FormModel\{
    FormConfigHandler, EtapeValidator
};
use Illuminate\Support\Facades\DB;

class DossierLivewire extends Component
{
    public $time;
    public $docs = [];
    public $etape_display = [];
    public $etapes = [];
    public $forms_configs = [];
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
    public $dossier;

    protected $listeners = ['fileUploaded' => 'handleFileUploaded'];

    public function mount($id)
    {
        $this->time = now()->format('H:i:s');

        $this->dossier = Dossier::with('beneficiaire', 'fiche', 'etape', 'status')->find($id);

        if (!$this->dossier) {
            abort(404, 'Dossier not found');
        }

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
        $this->mars = Client::where('type_client', 1)->get()->toArray();
        $this->financiers = Client::where('type_client', 2)->get()->toArray();
        $this->installateurs = Client::where('type_client', 3)->get()->toArray();

        $this->steps = DB::table('dossiers_data')
            ->where('dossier_id', $id)
            ->where('meta_key', 'like', '%step_%')
            ->pluck('meta_value', 'meta_key')
            ->toArray();

        $technicien = Rdv::with('user')
            ->where('dossier_id', $id)
            ->where('type_rdv', 1)
            ->where('status', '!=', 2)
            ->latest()
            ->first();
        $this->technicien = $technicien ? $technicien->toArray() : [];

        $this->get_docs();

        $this->reinitializeFormsConfigs(false);
    }

    private function determineLastEtape()
    {
        $this->last_etape = 1;
        foreach ($this->etapes as $etape) {
            if ($this->isUserAllowed($etape['etape_name']) && $etape['order_column'] <= $this->dossier->etape->order_column) {
                $this->last_etape = $etape['id'];
                $this->last_etape_order = $etape['order_column'];
            }
        }
    }

    private function isUserAllowed($etapeName)
    {
        // Implement your user permission logic here
        return true;
    }
    public function update_value($propertyName, $value)
    {
          // Parse the property name
        // Example property names: formData.3.nom, formData.27.ajout_piece#table-0-nom_de_la_piece

        // Pattern to match simple formData properties
        $simplePattern = '/^formData\.(\d+)\.(\w+)$/';
        // Pattern to match complex formData properties with table
        $complexPattern = '/^formData\.(\d+)\.(\w+)\.value\.(\w+)\.(\w+)$/';

        if (preg_match($complexPattern, $propertyName, $matches)) {
            $formId = $matches[1]; // Extract formId
            $key = $matches[2]; // Extract tag
            $tableIndex = $matches[3]; // Extract table index
            $cellTag = $matches[4]; // Extract cell tag

            $this->forms_configs[$formId]->formData[$key]->updating = true;

            if (isset($this->forms_configs[$formId]) && isset($this->forms_configs[$formId]->formData[$key])) {
                $this->forms_configs[$formId]->formData[$key]->value = $this->forms_configs[$formId]->formData[$key]->init_value();
                $this->forms_configs[$formId]->formData[$key]->value[$tableIndex][$cellTag]["value"] = $value;

                $this->forms_configs[$formId]->formData[$key]->save_value();


            }


        } elseif (preg_match($simplePattern, $propertyName, $matches)) {
            $formId = $matches[1]; // Extract formId
            $key = $matches[2]; // Extract key
            $this->forms_configs[$formId]->formData[$key]->updating = true;
            // Ensure the formId and key exist
            if (isset($this->forms_configs[$formId]) && isset($this->forms_configs[$formId]->formData[$key])) {
                $this->forms_configs[$formId]->formData[$key]->value = $value;
                $this->forms_configs[$formId]->formData[$key]->save_value();
            }
        }
        // dd($this->global_data);
        foreach ($this->forms_configs as $formId => $config) {

            $config->save();

            foreach ($config->formData as $tag => $data_form) {
                if (!isset($this->global_data[$tag])) {
                    $this->global_data[$tag] = '';
                }
                if ($this->global_data[$tag] != $data_form->value) {
                    $this->global_data[$tag] = $data_form->value;
                }
            }
        }
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
    

    public function refresh()
    {
        $this->time = now()->format('H:i:s');

        $this->dossier = Dossier::with('beneficiaire', 'fiche', 'etape', 'status')->find($this->dossier->id);

        if (!$this->dossier) {
            abort(404, 'Dossier not found');
        }

        $currentEtape = Etape::find($this->dossier->etape_number);
        $this->dossier->order_column = $currentEtape ? $currentEtape->order_column : null;

        $this->global_data = [];
        // $this->reinitializeFormsConfigs();

        $this->steps = DB::table('dossiers_data')
            ->where('dossier_id', $this->dossier->id)
            ->where('meta_key', 'like', '%step_%')
            ->pluck('meta_value', 'meta_key')
            ->toArray();
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $etape_display = Etape::find($tab);
        $this->etape_display = $etape_display ? $etape_display->toArray() : [];

        $this->reinitializeFormsConfigs();
        $firstKey = array_key_first($this->forms_configs);
        $this->display_form($firstKey);

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

    public function handleFileUploaded($request)
    {
        [$formId, $key, $value] = $request;

        if (isset($this->forms_configs[$formId]->formData[$key])) {
            $this->forms_configs[$formId]->formData[$key]->value = $value;
            $this->forms_configs[$formId]->formData[$key]->save_value();

            $this->formData[$formId][$key] = $value;
            $this->global_data[$key] = $value;

            $form = Form::find($formId);

            DossiersActivity::updateOrCreate(
                [
                    'dossier_id' => $this->dossier->id,
                    'form_id' => $formId,
                    'user_id' => auth()->id()
                ],
                [
                    'activity' => "Document chargé " . $form->form_title,
                    'score' => 100,
                    'updated_at' => now()
                ]
            );

            // $this->reinitializeFormsConfigs(true);
            $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);
        }
    }

    public function hydrate()
    {
      
        $this->reinitializeFormsConfigs(false);
        $this->get_docs();
     
    }

    public function updated($propertyName)
    {
        if (strpos($propertyName, 'formData.') === 0) {
            $this->handleFormDataUpdate($propertyName);
        }
    }

    private function handleFormDataUpdate($propertyName)
    {
        if (preg_match('/^formData\.(\d+)\.(\w+)/', $propertyName, $matches)) {
            $formId = $matches[1];
            $key = $matches[2];

            if (isset($this->forms_configs[$formId]->formData[$key])) {
                $field = $this->forms_configs[$formId]->formData[$key];
                $field->value = $this->formData[$formId][$key];
                $field->save_value();

                $form = Form::find($formId);
                $score = $this->score_info['form_score'][$formId] ?? 0;

                DossiersActivity::updateOrCreate(
                    [
                        'dossier_id' => $this->dossier->id,
                        'form_id' => $formId,
                        'user_id' => auth()->id()
                    ],
                    [
                        'activity' => "{$form->form_title} rempli à {$score} %",
                        'score' => $score,
                        'updated_at' => now()
                    ]
                );
            }
        }

        foreach ($this->forms_configs as $config) {
            $config->save();
            foreach ($config->formData as $tag => $data_form) {
                $this->global_data[$tag] = $data_form->value;
            }
        }
    }

    public function display_form($form_id)
    {
        $this->form_id = $form_id;
        $etape_display = Etape::find($this->tab);
        $this->etape_display = $etape_display ? $etape_display->toArray() : [];

        // $this->reinitializeFormsConfigs();
    }

    public function reinitializeFormsConfigs($should_save = true)
    {
        if ($this->dossier->fiche_id) {
            $formsQuery = Form::where('fiche_id', $this->dossier->fiche_id);

            if (!empty($this->etape_display)) {
                $formsQuery->where('etape_number', $this->etape_display['id']);
            }

            $forms = $formsQuery->get();

            $this->forms_configs = [];
            $this->formData = [];

            foreach ($forms as $form) {
                $handler = new FormConfigHandler($this->dossier, $form);
                $this->forms_configs[$form->id] = $handler;

                foreach ($handler->formData as $key => $field) {
                    // $value = $field->generate_value();
                    // $this->formData[$form->id][$key] = $value;
                    // $this->global_data[$key] = $value;

                }
            }

            // $this->global_data = array_merge($this->global_data, $this->dossier->toArray());
        } else {
            $this->forms_configs = [];
            $this->formData = [];
        }

        $this->get_score_per_etape();

        // $this->steps = DB::table('dossiers_data')
        //     ->where('dossier_id', $this->dossier->id)
        //     ->where('meta_key', 'like', '%step_%')
        //     ->pluck('meta_value', 'meta_key')
        //     ->toArray();
    }

    public function render()
    {
        return view('livewire.dossier-livewire', [
            'dossier' => $this->dossier,
            'docs' => $this->docs,
        ]);
    }

    public function get_score_per_etape()
    {
        $scores = [];
        $global_score = 0;
        $total_forms = count($this->formData);

        foreach ($this->formData as $formId => $fields) {
            $score = $this->forms_configs[$formId]->get_form_progression_percent() * 100;
            $scores[$formId] = number_format($score, 2);
            $global_score += $score;
        }

        $this->score_info = [
            "form_score" => $scores,
            "etape_score" => $total_forms ? number_format($global_score / $total_forms, 2) : '0.00'
        ];
    }

    public function submit()
    {
        foreach ($this->formData as $formId => $fields) {
            if (isset($this->forms_configs[$formId])) {
                foreach ($fields as $key => $value) {
                    $this->forms_configs[$formId]->formData[$key]->value = $value;
                }
            }
        }

        foreach ($this->forms_configs as $config) {
            $config->save();
        }

        session()->flash('message', 'Data saved successfully.');
    }

    public function add_row($table_tag, $form_id)
    {

        if (isset($this->forms_configs[$form_id])) {
            $field = $this->forms_configs[$form_id]->formData[$table_tag];
    
            // Add a new element to the form data
            $field->add_element();
    
            // Update the form data within the Livewire component
            $this->formData[$form_id][$table_tag] = $field->value;
            // $this->global_data[$table_tag] = $field->value;
    
            // Reassign to trigger reactivity
            $this->formData = $this->formData;
        }
    }

    public function remove_row($table_tag, $form_id, $uniqueId)
    {
        if (isset($this->forms_configs[$form_id])) {
            $field = $this->forms_configs[$form_id]->formData[$table_tag];
            $field->remove_element($uniqueId);
    
            $this->formData[$form_id][$table_tag] = $field->value;
    
            // Reassign to trigger reactivity
            $this->formData = $this->formData;
        }
    }
}
