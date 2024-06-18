<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;


class Db_table extends AbstractFormData
{
    public function render(bool $is_error)
    {

        
        $jsonString = str_replace(["\n", "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);

        $sql_command = $optionsArray['sql'];

        if (isset($optionsArray['arguments'])) {
            foreach ($optionsArray['arguments'] as $key => $data) {
                $sql_command = str_replace($key, eval($data), $sql_command);
            }
        }

   
        $request=DB::select($sql_command);

        $data = '<div class="form-group  col-lg-12">';

        $data.='<table class="table table-bordered responsive-table table-responsive dataTable no-footer">';
        $data.='<thead>';
        $data.='<th>';
        $data.='Date';

        $data.='<th>';
        $data.='Heure';

        $data.='</th>';
        $data.='<th>';
        $data.='Auditeur';

        $data.='</th>';
        $data.='<th>';
        $data.='Type de rdv';

        $data.='</th>';
        $data.='<th>';
        $data.='';

        $data.='</th>';
 
        $data.='</thead>';
        foreach( $request as $key => $value) {
            $data.='<tr>';

            $data.='<td>';
            $data.=date('d/m/Y',strtotime($value->date_rdv));

            $data.='</td>';
            $data.='<td>';
            $data.=date('H:i',strtotime($value->date_rdv));

            $data.='</td>';

            $data.='<td>';
            $data.=$value->name;

            $data.='</td>';


            $data.='<td>';
            $data.=$value->title;

            $data.='</td>';

            $data.='<td>';
            $data.='<div data-rdv_id="'.$value->rdv_id.'" class="btn btn-primary show_rdv"><i class="fa fa-eye"></i></div>';

            $data.='</td>';

            $data.='</tr>';
        }
        $data.='<tr>';

        $data.='<td>';

        $data.='</td>';
        $data.='<td>';

        $data.='</td>';

        $data.='<td>';

        $data.='</td>';


        $data.='<td>';

        $data.='</td>';

        $data.='<td>';
        $data.='<div data-rdv_id="" class="btn btn-secondary show_rdv">Ajouter un Rdv </div>';

        $data.='</td>';

        $data.='</tr>';
        $data.='</table>';
        

        $data .= '</div>';
        $data .= '<script>';

        $data .= '</script>';

        return $data;
    }

    public function save_value()
    {

        return true;
    }

}