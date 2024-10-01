<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Dossier;
use App\Models\Etape;
use App\Models\DossiersActivity;
use App\Models\User;
use App\Models\Form;
use App\Models\Rdv;
use App\Models\RdvStatus;
use App\FormModel\FormConfigHandler;
use App\FormModel\EtapeValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Client;

class DossierLivewire extends Component
{
    public $time;
    public $docs;

    public $etape_display;
    public $etapes;
    public $forms_configs;
    public $global_data = [];
    public $tab;
    public $last_etape;
    public $last_etape_order;
    public $score_info;
    public $formData = [];
    public $validators = [];
    public $steps = [];
    protected $listeners = ['fileUploaded' => 'handleFileUploaded', 'test' => 'test'];

    public function mount($id)
    {
        $this->time = now()->format('H:i:s');


        // $dossiers=Dossier::where('id','>',0)->get();
        // foreach($dossiers as $dossier) {

        //     if ($dossier && $dossier->etape) {
        //         $orderColumn = $dossier->etape->order_column;
        //     } else {
        //         // Handle the case where $dossier or $dossier->etape is null
        //         $orderColumn = null;
        //     }

        //     $docs=getDocumentStatuses($dossier->id,$orderColumn);

        // }

        // Fetch dossier with related data
        
        $this->dossier = Dossier::where('id', $id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first();
            $this->dossier->mar = $this->dossier->mar_client;
        $current = DB::table('etapes')->where('id', $this->dossier->etape_number)->first();
        $this->dossier->order_column = $current->order_column;

        if (!$this->dossier) {
            return redirect()->route('dossiers.index');

            abort(404, 'Dossier not found');
        }

        // Fetch distinct etapes
        $distinctEtapes = DB::table('forms')
            ->select('etape_number', DB::raw('MIN(id) as min_id'))
            ->groupBy('etape_number');


        $etapes = DB::table('forms')
            ->join('etapes', 'forms.etape_number', '=', 'etapes.id')
            ->joinSub($distinctEtapes, 'distinctEtapes', function ($join) {
                $join->on('forms.id', '=', 'distinctEtapes.min_id');
            })
            // ->select('forms.*', 'etapes.etape_name', 'etapes.etape_desc', 'etapes.etape_icon', 'etapes.order_column')
            ->orderBy('etapes.order_column')
            ->get();
        $this->reinitializeFormsConfigs();

        $this->etapes = $this->convertArrayToStdClass($etapes->toArray());

        foreach ($this->etapes as $etape) {
            $this->validators[$etape->etape_id] = new EtapeValidator($etape->etape_id);
        }
        $last_etape = 1;
        foreach ($this->etapes as $etape) {
           
            if (is_user_allowed($etape->etape_name) == true && (($etape->order_column) ) <= $this->dossier->etape->order_column) {
                $last_etape = ($etape->id);
                $this->last_etape_order=$etape->order_column;
            }
        }
       
        $this->last_etape = $last_etape;

        // $this->setTab($this->dossier['etape_number']);
        if($last_etape) {
            $this->setTab($last_etape);
            $this->emit('setTab');
        } 


        // $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);

        $auditeurs = User::where('type_id', 4);

        // if (auth()->user()->client_id > 0) {
        //     $auditeurs = $auditeurs->where('client_id', auth()->user()->client_id);

        // }

        $auditeurs = $auditeurs->get();

        $this->auditeurs = $auditeurs;

        $this->departments = DB::table('departement')->get()->map(function ($department) {
            return (array) $department; // Convert stdClass to array
        })->toArray();

        $this->rdv_status = RdvStatus::all();

        $this->mars = Client::where('type_client', 1)->get();
        $this->financiers = Client::where('type_client', 2)->get();
        $this->installateurs = Client::where('type_client', 3)->get();
        
        $this->steps=[];
        $datas = DB::table('dossiers_data')->where('dossier_id', $id)->where('meta_key','like','%step_%')->get();
        foreach ($datas as $data) {
    
            $steps[$data->meta_key] = $data->meta_value;
        }
        
        if(!empty($steps)) {
            $this->steps=$steps;
        }

        $lastRdv = Rdv::with('user')
        ->where('dossier_id', $id)
        ->where('type_rdv', 1)
        ->where('status','!=', 2)
        ->orderBy('created_at', 'desc')
        ->first();
        $this->technicien=$lastRdv ?? '';


        if($this->setTab($last_etape)) {
            // dd($last_etape);
        }
        $this->emit('setTab');
        $dossierId = $this->dossier->id; // Assuming $this->dossier_id is available in the controller


       $this->get_docs();

    }
    public function test()
    {
        dd('ok');
    }
    public function get_docs()
    {
        $results = DB::table('forms_config')
        ->leftJoin('forms_data', function($join) {
            $join->on('forms_config.name', '=', 'forms_data.meta_key')
                 ->where('forms_data.dossier_id', $this->dossier->id);
        })
        ->join('forms', 'forms.id', '=', 'forms_config.form_id') // Join with the forms table
        ->join('etapes', 'etapes.id', '=', 'forms.etape_id') // Join with the etapes table
        ->whereIn('forms_config.type', ['generate', 'fillable', 'upload', 'generateConfig'])
        ->whereIn('forms_config.id', function($query) {
            $query->select(DB::raw('MIN(id)'))
                  ->from('forms_config')
                  ->groupBy('name');
        }) // Subquery to select the first occurrence of each name
        ->orderBy('etapes.order_column') // Order the results by etapes.order_column
        ->get()
        ->toArray();
        $this->docs=[];

        foreach ($results as $result) {
            $options = json_decode($result->options, true); // Decode as associative array
            $doc = get_object_vars($result); // Convert the object to an array
            $doc['options'] = $options; // Add the options array
            $doc['last_etape_order']=$this->last_etape_order ?? 1;

            // Check if 'signable' exists in options and is true
            if (isset($options['signable']) && $options['signable'] === "true") {
                // Perform your additional DB request here
                // Example:
                $additionalData = DB::table('forms_data')
                                    ->where('dossier_id', $this->dossier->id)
                                    ->where('form_id', $result->form_id)
                                    ->get();
        
                // You can then add this additional data to the doc if needed
                $doc['additional_data'] = $additionalData;
            }
        
            $this->docs[] = $doc; // Add the modified array to docs
        }


    }
    public function refresh()
    {
        $this->time = now()->format('H:i:s');

        // Fetch dossier with related data
        $this->dossier = Dossier::where('id', $this->dossier->id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first();
           
        $current = DB::table('etapes')->where('id', $this->dossier->etape_number)->first();
        $this->dossier->order_column = $current->order_column;

        if (!$this->dossier) {
            return redirect()->route('dossiers.index');

            abort(404, 'Dossier not found');
        }
        $this->global_data = [];
        // Fetch distinct etapes
        $distinctEtapes = DB::table('forms')
            ->select('etape_number', DB::raw('MIN(id) as min_id'))
            ->groupBy('etape_number');


        $etapes = DB::table('forms')
            ->join('etapes', 'forms.etape_number', '=', 'etapes.id')
            ->joinSub($distinctEtapes, 'distinctEtapes', function ($join) {
                $join->on('forms.id', '=', 'distinctEtapes.min_id');
            })
            // ->select('forms.*', 'etapes.etape_name', 'etapes.etape_desc', 'etapes.etape_icon', 'etapes.order_column')
            ->orderBy('etapes.order_column')
            ->get();
        // $this->reinitializeFormsConfigs();

        $this->etapes = $this->convertArrayToStdClass($etapes->toArray());
        foreach ($this->etapes as $etape) {
            $this->validators[$etape->etape_id] = new EtapeValidator($etape->etape_id);
        }


        $this->steps=[];
       
        $datas = DB::table('dossiers_data')->where('dossier_id', $this->dossier->id)->where('meta_key','like','%step_%')->get();
        
        foreach ($datas as $data) {
    
            $steps[$data->meta_key] = $data->meta_value;
        }
        if(!empty($steps)) {
            $this->steps=$steps;
        }
        $this->dossier->mar = $this->dossier->mar_client;
      
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        // Fetch and convert to array
        $etape_display = Etape::where('id', $tab)->first();
        $this->etape_display = $etape_display ? $this->convertObjectToArray($etape_display) : [];

        $this->etapes = $this->convertArrayToStdClass($this->etapes);

        $this->reinitializeFormsConfigs();
        $firstKey = array_key_first($this->forms_configs);
        $this->display_form($firstKey);

        $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);
        $this->emit('setTab', ['forms_configs' => $this->forms_configs]);

        foreach($this->forms_configs as $key=>$config) {
        $messages=DB::table('messages')->where('dossier_id',$this->dossier->id)
        ->where('form_id',$key)
        ->get();

        foreach($messages as $message) {
            DB::table('messages_suivi')->where('user_id',auth()->user()->id)
            ->where( 'message_id',$message->id)
            ->delete();
        }
        }
     

    }


    public function handleFileUploaded($request)
    {

        $this->forms_configs[$request[0]]->formData[$request[1]]->value = $request[2];
        $this->forms_configs[$request[0]]->formData[$request[1]]->save_value();

        $this->formData[$request[0]][$request[1]] = $request[2];
        $this->global_data[$request[1]] = $request[2];
        $form_id=$request[0];
 
      
        $form=Form::find($form_id);
      
        
        $field_name = $request[1]; // Extracts 'revenu_fiscal'

        $dossier_id = $this->dossier->id;
        $user_id = auth()->user()->id;

        
        // Try to find an existing record with the same dossier_id, form_id, and user_id
        $activity = DossiersActivity::where('dossier_id', $dossier_id)
                                    ->where('form_id', $form_id)
                                    ->where('user_id', $user_id)
                                    ->first();

        if ($activity) {
            // Update the existing record
            $activity->activity = "Document chargé ".$form->form_title."";
            $activity->updated_at = now(); // Update the timestamp
            $activity->score = 100; // Update the timestamp
            $activity->save();
        } else {
            // Create a new record
            DossiersActivity::create([
                'dossier_id' => $dossier_id,
                'user_id' => $user_id,
                'form_id' => $form_id,
                'score' => 100,
                'activity' => "Document chargé ".$form->form_title."",
            ]);
        }

        $this->reinitializeFormsConfigs(true);

        $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);

    }

    public function hydrate()
    {
        $this->etapes = $this->convertArrayToStdClass($this->etapes);

        $this->reinitializeFormsConfigs(false);
        // $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);
   
        $this->get_docs();

    }


    public function update_value($propertyName, $value)
    {
        if (strpos($propertyName, 'formData.') === 0) {
    
            $parts = explode('.', $propertyName);
            $form_id = $parts[1]; // Extracts '1' from 'formData.1.revenu_fiscal'
            $form=Form::find($form_id);
          
            
            $field_name = $parts[2]; // Extracts 'revenu_fiscal'
    
            $dossier_id = $this->dossier->id;
            $user_id = auth()->user()->id;
    
            
            // Try to find an existing record with the same dossier_id, form_id, and user_id
            $activity = DossiersActivity::where('dossier_id', $dossier_id)
                                        ->where('form_id', $form_id)
                                        ->where('user_id', $user_id)
                                        ->first();
    
            if ($activity) {
                // Update the existing record
                $activity->activity = "".$form->form_title." rempli à ".$this->score_info['form_score'][$form_id]." %";
                $activity->score = $this->score_info['form_score'][$form_id];
                $activity->updated_at = now(); // Update the timestamp
                $activity->save();
            } else {
                // Create a new record
                DossiersActivity::create([
                    'dossier_id' => $dossier_id,
                    'user_id' => $user_id,
                    'form_id' => $form_id,
                    'score'=>$this->score_info['form_score'][$form_id],
                    'activity' => "".$form->form_title." rempli à ".$this->score_info['form_score'][$form_id]." %",
                ]);
            }
    
            $this->updatedFormData($propertyName, $value);
        }
    }
    
    public function updated($propertyName, $value)
    {

        if (strpos($propertyName, 'formData.') === 0) {

            $this->updatedFormData($propertyName, $value);
        }
    }

    public function updatedFormData($propertyName, $value)
    {
        // Parse the property name
        // Example property names: formData.3.nom, formData.27.ajout_piece#table-0-nom_de_la_piece

        // Pattern to match simple formData properties
        $simplePattern = '/^formData\.(\d+)\.(\w+)$/';
        // Pattern to match complex formData properties with table
        $complexPattern = '/^formData\.(\d+)\.(\w+)\.value\.(\d+)\.(\w+)$/';

        if (preg_match($complexPattern, $propertyName, $matches)) {
            $formId = $matches[1]; // Extract formId
            $key = $matches[2]; // Extract tag
            $tableIndex = (int) $matches[3]; // Extract table index
            $cellTag = $matches[4]; // Extract cell tag

            $this->forms_configs[$formId]->formData[$key]->updating = true;

            if (isset($this->forms_configs[$formId]) && isset($this->forms_configs[$formId]->formData[$key])) {
                $this->forms_configs[$formId]->formData[$key]->value = $this->forms_configs[$formId]->formData[$key]->init_value();
                $this->forms_configs[$formId]->formData[$key]->value[(int) $tableIndex][$cellTag]["value"] = $value;

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

    public function display_form($form_id)
    {
        $this->form_id = $form_id;

        // Fetch and convert to array
        $etape_display = Etape::where('id', $this->tab)->first();

        $this->etape_display = $etape_display ? $this->convertObjectToArray($etape_display) : [];
        $this->etapes = $this->convertArrayToStdClass($this->etapes);

     
        if(!empty($this->etape_display)) {
            $this->forms_configs = array_filter($this->forms_configs, function ($config) use ($etape_display) {
                return isset($config->form->etape_id) && $config->form->etape_id == $etape_display['id'];
            });
        }

        $this->reinitializeFormsConfigs();

        // $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);
    }

    public function reinitializeFormsConfigs($should_save = true)
    {
        if (isset($this->dossier) && $this->dossier->fiche_id) {
            $forms = DB::table('forms')->where('fiche_id', $this->dossier->fiche_id);

            if (!empty($this->etape_display)) {
                $forms = $forms->where('etape_number', $this->etape_display['id']);
            }

            $forms = $forms->get();

            $this->forms_configs = [];
            $this->formData = [];



            foreach ($forms as $form) {
                $handler = new FormConfigHandler($this->dossier, $this->convertArrayToStdClass((array) $form));
                $this->forms_configs[$form->id] = $handler;

                foreach ($handler->formData as $key => $field) {

                    if (!isset($this->global_data[$key])) {
                        $this->formData[$form->id][$key] = $field->generate_value();

                        $this->global_data[$key] = $field->generate_value();
                    } else {

                        $this->formData[$form->id][$key] = $this->global_data[$key];

                        $field->value = $this->global_data[$key];
                    }
                    if ($field->value && $should_save) {
                        $field->save_value();
                    }


                }
            }
            $dossier = Dossier::where('id', $this->dossier->id)->first();
            $this->dossier->mar = $dossier->mar_client;
            foreach ($dossier->getAttributes() as $key => $value) {
                $this->global_data[$key] = $value;

            }


        } else {
            $this->forms_configs = [];
            $this->formData = [];
        }
        if (isset($this->etape_display["id"])) {

            $distinctEtapes = DB::table('forms')
                ->select('etape_number', DB::raw('MIN(id) as min_id'))
                ->groupBy('etape_number');

            $etapes = DB::table('forms')
                ->join('etapes', 'forms.etape_number', '=', 'etapes.id')
                ->joinSub($distinctEtapes, 'distinctEtapes', function ($join) {
                    $join->on('forms.id', '=', 'distinctEtapes.min_id');
                })
                // ->select('forms.*', 'etapes.etape_name', 'etapes.etape_desc', 'etapes.etape_icon', 'etapes.order_column')
                ->orderBy('etapes.order_column')
                ->get();

            $this->etapes = $this->convertArrayToStdClass($etapes->toArray());

            $this->validators = [];
            foreach ($this->etapes as $etape) {
                $this->validators[$etape->etape_id] = new EtapeValidator($etape->etape_id);
            }
            // if($this->validators[$this->etape_display["id"]]) {
            //     $new_status = $this->validators[$this->etape_display["id"]]->get_last_validate_status($this->forms_configs);
            //     if (isset($new_status) && $this->dossier->etape_number==$this->etape_display['id']) {
            //         Dossier::where('id',$this->dossier->id)->update(['status_id'=> $new_status]);
            //     }
            // }


        }

        $this->get_score_per_etape();

        $this->steps=[];
       
        $datas = DB::table('dossiers_data')->where('dossier_id', $this->dossier->id)->where('meta_key','like','%step_%')->get();
        
        foreach ($datas as $data) {
    
            $steps[$data->meta_key] = $data->meta_value;
        }
        
        if(!empty($steps)) {
            $this->steps=$steps;
        }
        
    }



    public function render()
    {

        return view('livewire.dossier-livewire', [
            'dossier' => $this->dossier,
            'docs' => $this->docs,  // Pass docs to the view

        ]);
    }

    private function convertArrayToStdClass($array)
    {
        return json_decode(json_encode($array));
    }

    private function convertObjectToArray($object)
    {
        return json_decode(json_encode($object), true);
    }

    public function showPdfModal($pdfUrl)
    {
        $this->emit('pdfModalShow', $pdfUrl);
    }


    public function get_score_per_etape()
    {
        $scores = [];
        $global_score = 0;
        $total_forms = 0;

        foreach ($this->formData as $formId => $fields) {
            $score = $this->forms_configs[$formId]->get_form_progression_percent() * 100;
            $formatted_score = number_format($score, 2) . ''; // Formater le score à deux décimales et ajouter '%'
            $scores[$formId] = $formatted_score;
            $total_forms++;
            $global_score += $score;
        }

        if ($total_forms == 0) {
            $global_score = '0.00%';
        } else {
            $global_score = number_format($global_score / $total_forms, 2) . ''; // Formater le score global à deux décimales et ajouter '%'
        }

        $this->score_info = ["form_score" => $scores, "etape_score" => $global_score];
    }



    public function submit()
    {
        $this->etapes = $this->convertArrayToStdClass($this->etapes);

        foreach ($this->formData as $formId => $fields) {
            foreach ($fields as $key => $value) {
                if (isset($this->forms_configs[$formId])) {
                    $this->forms_configs[$formId]->formData[$key]->value = $value;
                }
            }
        }

        $result_save = [];
        foreach ($this->forms_configs as $formId => $config) {
            $result_save[$formId] = $config->save();
        }

        session()->flash('message', 'Data saved successfully.');
    }



    public function add_row($table_tag, $form_id)
    {

        if (isset($this->forms_configs[$form_id])) {
            $form_configs = $this->forms_configs[$form_id];
            $form_configs->formData[$table_tag]->add_element();
        }

        return '';
    }


    public function remove_row($table_tag, $form_id, $index)
    {
        if (isset($this->forms_configs[$form_id])) {
            $form_configs = $this->forms_configs[$form_id];
            $form_configs->formData[$table_tag]->remove_element($index);
            $this->forms_configs[$form_id]=$form_configs;
            // $this->global_data[$table_tag] = $form_configs->formData[$table_tag]->remove_element($index);
        }

        return '';
    }
}
