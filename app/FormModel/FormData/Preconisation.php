<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Preconisation extends AbstractFormData
{
    public function render(bool $is_error)
    {
        $produits = DB::table("produits")->get();
        $data = '';

        foreach ($produits as $produit) {


            $wireModel = "formData.{$this->form_id}.{$this->name}_produit_{$produit->id}";
            $isChecked = '';

            $data .= '<div class="form-group col-sm-12 ';
            $data .= $this->config->class ?? '';
            $data .= '">';

            $data .= '<label class="switch">';
            $data .= '<input type="checkbox" 
                id="checkbox_' . $this->config->name . '_' . $produit->id . '"
                wire:model="' . $wireModel . '"
                value="' . $produit->id . '"
                name="' . $this->config->name . '[]"
                ' . ($isChecked ? 'checked' : '') . '>';
            $data .= '<span class="slider round"></span>';
            $data .= '</label>';
            $data .= '<label class="custom-control-label" for="checkbox_' . $this->config->name . '_' . $produit->id . '">';
            $data .= $produit->produit_title;
            $data .= '</label>';

            $data .= '</div>';
        }

        return $data;
    }


}
