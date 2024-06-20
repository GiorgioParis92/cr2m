<?php

namespace App\FormModel\FormData;

class Phone extends _Regex
{

    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id, '/^(?:(?:\+|00)33|0)[1-9](?:[\s\.\-]?\d{2}){4}$/');
    }

    public function get_error_message() {

        return 'Le format ne correspond pas';
    }

}
