<?php

namespace App\FormModel\FormData;

class Email extends _Regex
{

    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id, '/[\w.+-]+@[\w-]+\.[\w.-]+/');
    }
    public function get_error_message() {

        return 'Veuillez entre une adresse email valide';
    }
}
