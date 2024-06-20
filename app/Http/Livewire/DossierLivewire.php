<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Dossier;
use App\Models\Etape;
use App\Models\User;
use App\FormModel\FormConfigHandler;
use App\FormModel\EtapeValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DossierLivewire extends Component
{

    public $etape_display;
    public $etapes;
    public $forms_configs;
    public $global_data = [];
    public $tab;
    public $formData = [];
    public $validators = [];
    protected $listeners = ['fileUploaded' => 'handleFileUploaded'];

    public function mount($id)
    {
        // Fetch dossier with related data
        $this->dossier = Dossier::where('id', $id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first();

            $current=DB::table('etapes')->where('id', $this->dossier->etape_number)->first();
        $this->dossier->order_column = $current->order_column;

        if (!$this->dossier) {
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
            ->select('forms.*', 'etapes.etape_name', 'etapes.etape_desc', 'etapes.order_column')
            ->orderBy('etapes.order_column')
            ->get();

        $this->reinitializeFormsConfigs();

        $this->etapes = $this->convertArrayToStdClass($etapes->toArray());
        foreach ($this->etapes as $etape) {
            $this->validators[$etape->etape_id] = new EtapeValidator($etape->etape_id);
        }


        $this->setTab($this->dossier['etape_number']);
        $this->emit('setTab');
        // $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);

        $auditeurs = User::where('type_id', 4);

        if (auth()->user()->client_id > 0) {
            $auditeurs = $auditeurs->where('client_id', auth()->user()->client_id);

        }

        $auditeurs = $auditeurs->get();

        $this->auditeurs = $auditeurs;

        $this->departments = DB::table('departement')->get()->map(function ($department) {
            return (array) $department; // Convert stdClass to array
        })->toArray();

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

    }


    public function handleFileUploaded()
    {
        // $this->reinitializeFormsConfigs();

        // $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);

    }

    public function hydrate()
    {
        $this->etapes = $this->convertArrayToStdClass($this->etapes);
        $this->reinitializeFormsConfigs();
        // $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);

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
        // Example property name: formData.3.nom
        $pattern = '/^formData\.(\d+)\.(\w+)$/';
        if (preg_match($pattern, $propertyName, $matches)) {
            $formId = $matches[1]; // Extract formId
            $key = $matches[2]; // Extract key
            $this->forms_configs[$formId]->formData[$key]->updating=true;
            // Ensure the formId and key exist
            if (isset($this->forms_configs[$formId]) && isset($this->forms_configs[$formId]->formData[$key])) {
                $this->forms_configs[$formId]->formData[$key]->value = $value;
            }
        }
 
        foreach ($this->forms_configs as $formId => $config) {
       
            $config->save();
            foreach ($config->formData as $tag => $data_form) {
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
        $this->forms_configs = array_filter($this->forms_configs, function ($config) use ($etape_display) {
            return isset($config->form->etape_id) && $config->form->etape_id == $etape_display['id'];
        });
        $this->reinitializeFormsConfigs();

        // $this->emit('initializeDropzones', ['forms_configs' => $this->forms_configs]);
    }

    public function reinitializeFormsConfigs()
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
                        $this->formData[$form->id][$key] = $field->value;
                   
                        $this->global_data[$key] = $field->value;
                    } else {
                        $this->formData[$form->id][$key] = $this->global_data[$key];
                     
                        $field->value = $this->global_data[$key];
                    }
                    if($field->value) {
                        $field->save_value();
                    }
                 

                }
            }
            $dossier=Dossier::where('id', $this->dossier->id)->first();

            foreach($dossier->getAttributes() as $key=>$value) {
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
                ->select('forms.*', 'etapes.etape_name', 'etapes.etape_desc', 'etapes.order_column')
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
    }

    public function updateFormData($formId, $key, $value)
    {
        $this->formData[$formId][$key] = $value;
        if (isset($this->forms_configs[$formId])) {
            $this->forms_configs[$formId]->formData[$key]->value = $value;
        }
    }

    public function render()
    {
        return view('livewire.dossier-livewire', [
            'dossier' => $this->dossier,
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
}
