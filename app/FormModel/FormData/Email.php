<?php

namespace App\FormModel\FormData;

class Email extends Regex
{

    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id, '/[\w.+-]+@[\w-]+\.[\w.-]+/');
    }

}
