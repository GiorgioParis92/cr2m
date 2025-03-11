<?php

namespace App\FormModel\FormData;

class TVA extends _Regex
{

    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id, '(\b(FR|Fr|fR|fr)\d{11}\b)|(\b(FR|Fr|fR|fr)\d{2}\b[ ]\b\d{3}\b[ ]\b\d{3}\b[ ]\b\d{3}\b)');
    }

}
