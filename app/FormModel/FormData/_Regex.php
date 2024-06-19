<?php

namespace App\FormModel\FormData;

class _Regex extends Text
{
    protected $regex;

    public function __construct($config, $name, $form_id, $dossier_id, $regex)
    {
        parent::__construct($config, $name, $form_id, $dossier_id);
        $this->regex = $regex;
    }

    public function get_error_message()
    {
        if (!parent::check_value()) {
            return parent::get_error_message();
        }

        if ($this->value !== '' && !preg_match($this->regex, $this->value)) {
            return 'La valeur ne correspond pas au format attendu';
        }

        return 'Erreur';
    }

    public function check_value()
    {
        if (!parent::check_value()) {
            return false;
        }

        if ($this->value !== '' && !preg_match($this->regex, $this->value)) {
            return false;
        }

        return true;
    }
}
