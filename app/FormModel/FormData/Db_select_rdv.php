<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\View;

class Db_select_rdv extends Db_select
{
    public function save_value()
    {
        $value = $this->generate_value();
        if (!$this->check_value()) {
            return false;
        }

        DB::table('forms_data')->updateOrInsert(
            [
                'dossier_id' => $this->dossier_id,
                'form_id' => $this->form_id,
                'meta_key' => $this->name
            ],
            [
                'meta_value' => $value ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return true;
    }

}