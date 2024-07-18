<?php
namespace App\FormModel\FormData;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Table extends AbstractFormData
{

    public $optionsArray = [];
    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id);

        // $this->value = $this->init_value();
        $jsonString = str_replace(["\n", "\r"], '', $this->config->options);
        $this->optionsArray = json_decode($jsonString, true);

    }
    public function render(bool $is_error)
    {
        $data = '';
        $this->value = $this->decode_if_json($this->value);


        foreach ($this->value as $index => $element_data) {
            foreach ($this->optionsArray as $element_config) {

                $baseNamespace = 'App\FormModel\FormData\\';
                $className = $baseNamespace . ucfirst($element_config['type']);
                $div_name = $this->name . '.value.' . $index . '.' . $element_config['name'];

                if (class_exists($className)) {
                    $reflectionClass = new \ReflectionClass($className);
                    $element_group[$element_config['name']] = $reflectionClass->newInstance((object) $element_config, $div_name, $this->form_id, $this->dossier->id, false);
                } else {
                    // Fallback to AbstractFormData if the class does not exist
                    $element_group[$element_config['name']] = new AbstractFormData((object) $element_config, $div_name, $this->form_id, $this->dossier->id, false);
                }
                $element_group[$element_config['name']]->set_dossier($this->dossier);
                if (is_array($element_data[$element_config['name']])) {
                    $element_data[$element_config['name']] = (object) $element_data[$element_config['name']];
                }

                $element_group[$element_config['name']]->value = $element_data[$element_config['name']]->value;
                $data .= $element_group[$element_config['name']]->render(false);

            }
            $data .= '<div class="btn btn-primary" wire:click="remove_row(\'ajout_piece\',' . $this->form_id . ','. $index.')">remove row</div>';

        }
        $this->save_value();


        $data .= '<div class="btn btn-primary" wire:click="add_row(\'ajout_piece\',' . $this->form_id . ')">Add row</div>';


        return $data;
    }

    public function init_element()
    {

        $element_group = [];

        foreach ($this->optionsArray as $element_config) {
            $baseNamespace = 'App\FormModel\FormData\\';
            $className = $baseNamespace . ucfirst($element_config['type']);

            if (class_exists($className)) {
                $reflectionClass = new \ReflectionClass($className);
                $element_group[$element_config['name']] = $reflectionClass->newInstance((object) $element_config, $element_config['name'], $this->form_id, $this->dossier->id, false);
            } else {
                // Fallback to AbstractFormData if the class does not exist
                $element_group[$element_config['name']] = new AbstractFormData((object) $element_config, $element_config['name'], $this->form_id, $this->dossier->id, false);
            }
            $element_group[$element_config['name']]->set_dossier($this->dossier);
        }


        return $element_group;
    }
    public function add_element()
    {

        $element = $this->init_element();

        $this->value = $this->decode_if_json($this->value);
        $this->value[] = $element;
        $this->save_value();
    }
    public function remove_element($index)
    {

        unset($this->value[$index]);
        $this->value = array_values($this->value); // Reindex the array

        $this->save_value();
    }
    public function init_value()
    {
        if (is_array($this->value)) {
            return $this->value;
        }
        $value = json_decode($this->value, true);

        if (!isset($value) || $value == '') {
            $value = [];
        }

        return $value;

    }

    public function generate_value()
    {
        if (is_array($this->value)) {
            return json_encode($this->value);
        }

        return $this->value;

    }

    function decode_if_json($value)
    {
        if ($value == '') {
            return [];
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            // Check if json_decode succeeded
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return $value;
    }
}