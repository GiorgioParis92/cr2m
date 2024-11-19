<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\View;

class Button extends AbstractFormData
{
    public function render(bool $is_error)
    {

        if (!is_array($this->config->options)) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->config->options;
        }
        if (!is_array($optionsArray)) {
            $optionsArray = [];
        }
        $dossier = Dossier::find($this->dossier_id);
        $all_data = load_all_dossier_data($dossier);

        foreach ($all_data as $data_key => $data_value) {
            foreach ($data_value as $k => $v) {
                foreach ($v as $a => $b) {
                    if (!empty($b)) {
                        $array[$a] = $b;
                    }

                }
            }
        }



        $data = '';
        if(!$this->value) {
        $data .= '<div>';
        $data .= '<div  class="btn btn-primary" onclick="sendApiRequest(this)" ';

        foreach ($optionsArray['fields'] as $k => $v) {
            $val = '';
            if (!empty($v)) {

                if (is_array($v)) {
                   
                    foreach($v as $cle=>$valeur) {
                        $val .= $array[$valeur].' ';
                    }


                } else {
                    if (!empty($array[$v])) {
                        $val = $array[$v];
                    } else {
                        $val = $v;
                    }
                }



            } else {
                $val = $array[$k] ?? '';
            }


            $data .= 'data-';
            $data .= $k;
            $data .= '="';
            $data .= $val;
            $data .= '"';
        }

        if(isset($optionsArray['files'])) {
            $data_files='';
            foreach($optionsArray['files'] as $files) {
                if(isset($array[$files])) {
                    $data_files.=($array[$files] ?? '').';';
                }
               
            }
            $data .= 'data-';
            $data .= 'files';
            $data .= '="';
            $data .= $data_files;
            $data .= '"';
        }


        $data .= 'data-';
        $data .= 'form_id';
        $data .= '="';
        $data .= $this->form_id;
        $data .= '"';

        $data .= '>';
        $data .= $this->config->title;
        $data .= '</div>';
        $data .= '</div>';
        } else {
            $data .= '<div>';
            $data .= '<a class="btn btn-success " target="_blank" href="https://crm.elitequalityinspection.fr/demandes/'.$this->value.'">Voir la demande d\'inspection sur ELITE';
      
            
            $data .= '</a>';
            $data .= '</div>';
        }


        return $data;

    }

}