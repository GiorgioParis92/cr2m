<?php

namespace App\FormModel\FormData;

class Siret extends Regex
{

    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id, '\b\d{3}\b[ ]\b\d{3}\b[ ]\b\d{3}\b[ ]\b\d{5}\b|\b\d{14}\b|\b\d{5}\b[ ]\b\d{9}\b');
    }

}
