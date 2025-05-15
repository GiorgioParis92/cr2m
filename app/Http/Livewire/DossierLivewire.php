<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\{
    Dossier, Etape, DossiersActivity, User, Form, Rdv, RdvStatus, Client, Card
};
use App\FormModel\{
    FormConfigHandler, EtapeValidator
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\CardCreationService;

class DossierLivewire extends Component
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
    public $responseData='';
    public $dossier;

    protected $listeners = ['fileUploaded' => 'handleFileUploaded'];

    
    public function mount($id)
    {
        $this->time = now()->format('H:i:s');

        $this->dossier = Dossier::with('beneficiaire', 'fiche', 'etape', 'status','mar_client')->find($id);

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


            $programme=DB::table('forms_data')->where(dossier_id,$this->dossier->id)->where('meta_key','programme_dossier')->first();

            if($programme) {
                $this->dossier['programme_dossier']=$programme->meta_value;
            }


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
        ->where('dossier_id', $id)
        ->where('meta_key', 'like', '%step_%')
        ->get(['meta_key', 'meta_value', 'user_id']);
    
    $this->steps = [];
    foreach ($stepData as $item) {
        $this->steps[$item->meta_key] = [
            'meta_value' => $item->meta_value,
            'user_id' => $item->user_id,
        ];
    }

        $technicien = Rdv::with('user')
            ->where('dossier_id', $id)
            ->where('type_rdv', 1)
            ->where('status', '!=', 2)
            ->latest()
            ->first();

            $this->technicien = $technicien ? $technicien->toArray() : [];
            $technicien2 = Rdv::with('user')
            ->where('dossier_id', $id)
            ->where('type_rdv', 2)
            ->where('status', '!=', 2)
            ->latest()
            ->first();

            $this->technicien2 = $technicien2 ? $technicien2->toArray() : [];
        $this->get_docs();
        $this->reinitializeFormsConfigs(false);

        $this->responseData = null;


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
        // foreach ($this->forms_configs as $formId => $config) {

        //     $config->save();

        //     foreach ($config->formData as $tag => $data_form) {
        //         if (!isset($this->global_data[$tag])) {
        //             $this->global_data[$tag] = '';
        //         }
        //         if ($this->global_data[$tag] != $data_form->value) {
        //             $this->global_data[$tag] = $data_form->value;
        //         }
        //     }
        // }
        foreach ($this->forms_configs as $formId => $config) {
            // Save the main configuration for the form
            $config->save();
        
            foreach ($config->formData as $tag => $data_form) {
                // Check if the data form is valid before updating
                if (!isset($this->global_data[$tag])) {
                    $this->global_data[$tag] = [];
                }
        
                // Update global data with the current form's data
                $this->global_data[$tag][$formId] = $data_form->value ?? null;
        
                // Save individual form data
                try {
                    $data_form->save_value();
                } catch (\Exception $e) {
                    // Log or handle exceptions gracefully
                    \Log::error("Failed to save value for form $formId, tag $tag: " . $e->getMessage());
                }
            }
        }
        
        change_status($key, $value,$this->dossier->id);

        $card=app(CardCreationService::class)->checkAndCreateCard(
            $key,
            $value,
            $this->dossier,
            auth()->user()->id
        );
   

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

        // $this->dossier = Dossier::with('beneficiaire', 'fiche', 'etape', 'status','mar_client')->find($this->dossier->id);

        // if (!$this->dossier) {
        //     abort(404, 'Dossier not found');
        // }

        $currentEtape = Etape::find($this->dossier->etape_number);
        $this->dossier->order_column = $currentEtape ? $currentEtape->order_column : null;

        $this->global_data = [];
        // $this->reinitializeFormsConfigs();

        $stepData = DB::table('dossiers_data')
        ->where('dossier_id', $this->dossier->id)
        ->where('meta_key', 'like', '%step_%')
        ->get(['meta_key', 'meta_value', 'user_id']);
    
    $this->steps = [];
    foreach ($stepData as $item) {
        $this->steps[$item->meta_key] = [
            'meta_value' => $item->meta_value,
            'user_id' => $item->user_id,
        ];
    }
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $etape_display = Etape::find($tab);
        $this->etape_display = $etape_display ? $etape_display->toArray() : [];


        // $this->reinitializeFormsConfigs();

        $firstKey = $this->reinitializeFormsConfigs();
        $this->display_form($firstKey);
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

    public function handleFileUploaded($request)
    {

        $this->reinitializeFormsConfigs(false);

        // dump($request);
        // dd($this->forms_configs);
        // $this->forms_configs[$request[0]]->formData[$request[1]]->value = $request[2];
        // $this->forms_configs[$request[0]]->formData[$request[1]]->save_value();

        // $this->formData[$request[0]][$request[1]] = $request[2];
        // $this->global_data[$request[1]] = $request[2];
        // $form_id=$request[0];
 
      
        // $form=Form::find($form_id);
      
        
        // $field_name = $request[1]; // Extracts 'revenu_fiscal'

        // $dossier_id = $this->dossier->id;
        // $user_id = auth()->user()->id;

        
        // // Try to find an existing record with the same dossier_id, form_id, and user_id
        // $activity = DossiersActivity::where('dossier_id', $dossier_id)
        //                             ->where('form_id', $form_id)
        //                             ->where('user_id', $user_id)
        //                             ->first();

        // if ($activity) {
        //     // Update the existing record
        //     $activity->activity = "Document chargé ".$form->form_title."";
        //     $activity->updated_at = now(); // Update the timestamp
        //     $activity->score = 100; // Update the timestamp
        //     $activity->save();
        // } else {
        //     // Create a new record
        //     DossiersActivity::create([
        //         'dossier_id' => $dossier_id,
        //         'user_id' => $user_id,
        //         'form_id' => $form_id,
        //         'score' => 100,
        //         'activity' => "Document chargé ".$form->form_title."",
        //     ]);
        // }


        $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);

    }

    public function hydrate()
    {
 
            $this->reinitializeFormsConfigs(false);

            
        $this->get_docs();
     
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
            $firstKey=null;
            foreach ($forms as $form) {
                $handler = new FormConfigHandler($this->dossier, $form);
                $this->forms_configs[$form->id] = $handler;

                if($firstKey==null && $form->type!='document') {
                    $firstKey=$form->id;
                }

            }

        } 
        $this->get_score_per_etape();

        return $firstKey;
       
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
            $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);

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
    public function delete_signature($form_id, $tag)
    {
        // Use $this->dossier->id instead of $this->dossier_id
        $result = DB::table('forms_data')
                    ->where('dossier_id', $this->dossier->id) // Corrected dossier_id reference
                    ->where('form_id', $form_id)
                    ->where('meta_key', '!=', $tag) // Assuming you want to delete based on meta_key equal to the tag
                    ->delete();
        
        // Optionally, you can return or handle the result here if needed
        if ($result) {
            session()->flash('message', 'Signature deleted successfully.');
        } else {
            session()->flash('message', 'Failed to delete signature.');
        }
    }
    

    public function mark_signed($form_id, $tag)
    {
   
        
        $update = DB::table('forms_data')->updateOrInsert(
            [
                'dossier_id' => '' . $this->dossier->id . '',
                'form_id' => '' . $form_id . '',
                'meta_key' => 'signature_request_id'
            ],
            [
                'meta_value' => 'finish',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        $update = DB::table('forms_data')->updateOrInsert(
            [
                'dossier_id' => '' . $this->dossier->id . '',
                'form_id' => '' . $form_id . '',

                'meta_key' => 'signature_status'
            ],
            [
                'meta_value' => 'finish',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        $update = DB::table('forms_data')->updateOrInsert(
            [
                'dossier_id' => '' . $this->dossier->id . '',
                'form_id' => '' . $form_id . '',

                'meta_key' => 'document_id'
            ],
            [
                'meta_value' => 'finish',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
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

    private function arrayToObject($array)
{
    return json_decode(json_encode($array), false);
}

public function fetchResponseData()
{
    // Fetch the MAR credentials
    $mar = Client::where('id', $this->dossier->mar)->first();
    
    // Set login and password if found
    if ($mar) {
        $login = $mar->anah_login;
        $password = $mar->anah_password;
    }

    // If there's a reference_unique, attempt to call the scrapping API
    if ($this->dossier->reference_unique) {
        $url = url('/api/scrapping');
        $token = 'qlcb1m8AlZU8dteqvYWFxrehJ2iGlGvUbinQhUNOa3yqjizldp0ARNiCDmsl';

        try {
            // Make API request
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->post($url, [
                    'reference_unique' => $this->dossier->reference_unique,
                    'login' => $login,
                    'password' => $password,
                ]);

            // Check if the request was successful
            if ($response->successful()) {
                $this->responseData = $response->json();
            } else {
                // Log the actual error for debugging
                \Log::error("Scrapping API Error", [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);

                // Display a user-friendly message
                $this->responseData = "There was an issue processing your request. Please try again later.";
            }

        } catch (\Exception $e) {
            // Catch any exceptions, log them and return a friendly error message
            \Log::error("Scrapping API Exception", ['error' => $e->getMessage()]);

            $this->responseData = "An unexpected error occurred. Please try again later.";
        }
    }
}



private function checkAndCreateCard($key, $value)
{
    foreach ($this->cardCreationRules as $rule) {
        // Check if the rule's key matches the update
        if ($rule['key'] === $key) {
            // Handle check_not_null cases
            if (isset($rule['check_not_null']) && $rule['check_not_null']) {
                // Skip if the value is null or an empty string
                if ($value === null || $value === '') {
                    continue;
                }
            } 
            // Handle specific value cases
            elseif (isset($rule['value']) && $rule['value'] !== $value) {
                continue; // Skip if the value does not match
            }

            $title = $rule['title'];
            $assignedUsers = $this->getAssignedUsers($rule);

            // Create the card if a title and assigned users are defined
            if ($title && !empty($assignedUsers)) {
                $card = Card::create([
                    'title' => $title,
                    'dossier_id' => $this->dossier->id,
                    'user_id' => auth()->user()->id,
                    'status' => 1,
                ]);

                $card->users()->attach($assignedUsers);
                $this->emit('cardAdded');
            }

            // Stop the loop once a rule is matched
            break;
        }
    }
}


private function getAssignedUsers($rule)
{
    if (isset($rule['assigned_users'])) {
        // Return assigned users directly if specified in the rule
        return $rule['assigned_users'];
    } elseif (isset($rule['user_type'])) {
        // Fetch users based on user type if specified
        return User::where('type_id', $rule['user_type'])->pluck('id')->toArray();
    } elseif (!empty($rule['custom_user_logic'])) {
        // Define any additional custom logic for assigned users here
        return $this->customUserLogic();
    }

    return [];
}

private function customUserLogic()
{
    // Define any custom logic for selecting users here
    // Example: Select users dynamically based on certain conditions
    return User::where('type_id', 4)->pluck('id')->toArray();
}

protected $cardCreationRules = [
    [
        'key' => 'specific_field_1',
        'value' => 'trigger_value_1',
        'title' => 'Title for Card 1',
        'assigned_users' => [1, 2, 3],
    ],
    [
        'key' => 'cp',
        'check_not_null' => true,  // Only check that value is not null or empty
        'title' => 'Code postal mis à jour',
        'user_type' => 4,
    ],
    [
        'key' => 'specific_field_3',
        'value' => 'trigger_value_3',
        'title' => 'Title for Card 3',
        'custom_user_logic' => true,
    ],
    // Add more rules as needed
];


}
