<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Dossier;
use App\Models\Rdv;
use App\Models\User;
use App\Models\Client;

class DossierHeader extends Component
{
    public $dossier;
    public $technicien;
    public $installateurs;

    /**
     * Create a new component instance.
     *
     * @param Dossier $dossier
     * @param Rdv|null $technicien
     * @param \Illuminate\Database\Eloquent\Collection|Client[] $installateurs
     * @return void
     */
    public function __construct(Dossier $dossier, $technicien = null, $installateurs = null)
    {
       
        $this->dossier = $dossier;
        $this->technicien = $technicien;
        $this->installateurs = $installateurs;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.dossier-header');
    }
}
