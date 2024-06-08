<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Dossier;
use App\Models\Etape;
use App\FormModel\FormConfigHandler;
use Illuminate\Support\Facades\DB;

class DossierLivewire extends Component
{
    public $etape_display;
    public $etapes;
    public $forms_configs;
    public $tab;

    public function mount($id)
    {
        // Fetch dossier with related data
        $this->dossier = Dossier::where('id', $id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first();

        if (!$this->dossier) {
            abort(404, 'Dossier not found');
        }

        // Fetch distinct etapes
        $distinctEtapes = DB::table('forms')
            ->select('etape_id', DB::raw('MIN(id) as min_id'))
            ->groupBy('etape_id');

        $etapes = DB::table('forms')
            ->join('etapes', 'forms.etape_id', '=', 'etapes.id')
            ->joinSub($distinctEtapes, 'distinctEtapes', function ($join) {
                $join->on('forms.id', '=', 'distinctEtapes.min_id');
            })
            ->select('forms.*', 'etapes.etape_name', 'etapes.etape_desc')
            ->orderBy('forms.etape_number')
            ->get();

        $this->etapes = $this->convertArrayToStdClass($etapes->toArray());
            $this->setTab($this->dossier['etape_number']);
        $this->reinitializeFormsConfigs();
    }

    public function setTab($tab)
    {
        $this->tab = $tab;

        $etape_display = Etape::where('id', $tab)->first();

        $this->etape_display = $etape_display ?? $this->convertArrayToStdClass($etape_display->toArray());
        $etapes = [];
        foreach ($this->etapes as $etape) {
            $etapes[] = $this->convertArrayToStdClass((array) $etape);

        }
        $this->etapes = $etapes;

        $this->reinitializeFormsConfigs();
        $firstKey = array_key_first($this->forms_configs);
        $this->display_form($firstKey);
    }

    public function display_form($form_id)
    {
        
        $this->form_id=$form_id;


        $etape_display = Etape::where('id', $this->tab)->first();

        $this->etape_display = $etape_display ?? $this->convertArrayToStdClass($etape_display->toArray());
        $etapes = [];
        foreach ($this->etapes as $etape) {
            $etapes[] = $this->convertArrayToStdClass((array) $etape);

        }
        $this->etapes = $etapes;

        $this->reinitializeFormsConfigs();
       
    }
    public function reinitializeFormsConfigs()
    {
        // Check if dossier is set and not null
        if (isset($this->dossier) && $this->dossier->fiche_id) {
            $forms = DB::table('forms')->where('fiche_id', $this->dossier->fiche_id);
            
            if(isset($this->etape_display)) {
                $forms=$forms->where('etape_number', $this->etape_display->id);

            }

            $forms=$forms->get();

            $this->forms_configs = [];

            foreach ($forms as $form) {
                $this->forms_configs[$form->id] = new FormConfigHandler($this->dossier, $this->convertArrayToStdClass((array) $form));
            }
        } else {
            // Log or handle the case where dossier or fiche_id is not set
            $this->forms_configs = [];
        }
    }

    public function render()
    {
        return view('livewire.dossier-livewire', [
            'dossier' => $this->dossier, // Pass as a parameter to the view
        ]);
    }

    private function convertArrayToStdClass($array)
    {
        return json_decode(json_encode($array));
    }
}
