<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\View;

class Button extends AbstractFormData
{
    public function render(bool $is_error)
    {
      

        $data='';

      
        $data .= '<div>';
        $data .= '<button type="button" class="btn btn-primary" onclick="sendApiRequest(this)"'
            . ' data-login="giorgio"'
            . ' data-password="Dafa2021"'
            . ' data-type_demande="reno_ampleur"'
            . ' data-raison_sociale="test"'
            . ' data-siren="123456789"'
            . ' data-proprietaire_name="BOGICEVIC"'
            . ' data-proprietaire_adresse="18 RUE DU PONT NOYELLES"'
            . ' data-proprietaire_cp="94160"'
            . ' data-proprietaire_ville="SAINT-MANDE"'
            . ' data-contact_intervention="BOGICEVIC"'
            . ' data-tel_contact_intervention="0651980838"'
            . ' data-entreprise_travaux="."' // Note: Ensure this doesn't break the HTML
            . ' data-beneficiaire_name="BOGICEVIC">'
            . $this->config->title
            . '</button>';
        $data .= '</div>';
        
        

        return $data;

    }

}