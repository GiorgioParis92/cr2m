<?php
namespace App\FormModel\FormData;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class TableNew extends AbstractFormData
{

    public $optionsArray = [];
    public $condition_valid = [];

    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id);

        // $this->value = $this->init_value();
        if (!is_array($this->config->options)) {
            $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
            $optionsArray = json_decode($jsonString, true);
        } else {
            $optionsArray = $this->config->options;

        }
        $optionsArray = convertToArray($optionsArray);
        $this->optionsArray = $optionsArray;

        $this->condition_valid = false;

        if (isset($optionsArray['conditions'])) {

            foreach ($optionsArray as $key => $condition_config) {
                if ($key == 'conditions') {
                    if ($this->check_condition($condition_config)) {

                        $this->condition_valid = true;

                        if (isset($condition_config['operation']) && $condition_config['operation'] == 'AND') {
                            break;
                        }

                    } else {
                        $this->condition_valid = false;
                    }
                }


            }
        } else {
            $this->condition_valid = true;
        }





    }
    public function render(bool $is_error)
    {
        $data = '';
        if ($this->condition_valid == false) {
            return '';
        }
        $this->value = $this->decode_if_json($this->value);
    
        if (!empty($this->value)) {
            foreach ($this->value as $index => $element_data) {
                $data .= '<div class="row">';
                $data .= '<div class="col-lg-10 col-sm-12">';
    
                foreach ($this->optionsArray as $element_config) {
                    $baseNamespace = 'App\FormModel\FormData\\';
                    $className = $baseNamespace . ucfirst($element_config['type']);
                    $div_name = $this->name . '.value.' . $index . '.' . $element_config['name'];
    
                    if (class_exists($className)) {
                        $reflectionClass = new \ReflectionClass($className);
                        $element_instance = $reflectionClass->newInstance((object) $element_config, $div_name, $this->form_id, $this->dossier_id, false);
                    } else {
                        // Fallback to AbstractFormData if the class does not exist
                        $element_instance = new AbstractFormData((object) $element_config, $div_name, $this->form_id, $this->dossier_id, false);
                    }
    
                    $element_instance->set_dossier($this->dossier);
    
                    // Set the value from the stored data
                    $element_value = $element_data[$element_config['name']]['value'] ?? '';
                    $element_instance->value = $element_value;
    
                    $data .= $element_instance->render(false);
                }
    
                $data .= '</div>';

                $data .= '</div>';
                $data .= '<div class="col-lg-2">';
                $data .= "<label></label>";
                $data .= '<div class="col-lg-12 col-sm-12 btn btn-danger" wire:click="remove_row(\'' . $this->name . '\',' . $this->form_id . ',' . (int) $index . ')">Supprimer</div>';
                $data .= '</div>';
            }
        }
    
        $data .= '<div class="btn btn-success" wire:click="add_row(\'' . $this->name . '\',' . $this->form_id . ')">' . $this->config->title . '</div>';
    
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
        $element_values = [];
        foreach ($element as $key => $field) {
            $element_values[$key] = ['value' => $field->value ?? ''];
        }
    
        // Decode $this->value and ensure it's an array
        $this->value = $this->decode_if_json($this->value);
    
        if (!is_array($this->value)) {
            $this->value = [];
        }
    
        $this->value[] = $element_values;
    
        // Explicitly reassign $this->value to trigger Livewire update
        $this->value = array_values($this->value);
    
        $this->save_value();
    }
    
    
    public function remove_element($index)
    {
        $this->value = $this->decode_if_json($this->value);
    
        unset($this->value[$index]);
    
        // Reindex the array and explicitly update the property
        $this->value = array_values($this->value);
    
        // Remove the line that sets $this->value to an empty string
        if (empty($this->value)) {
            $this->value = '';
        }
    
        $this->save_value();
    
        return $this->generate_value();
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
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return $value;
    }
    
    public function render_pdf()
    {

        $should_render = false;
        $data = '<div><table style="margin:auto;width:90%;margin-top:20px;border-collapse: collapse;">';

        // Decode the JSON value if needed
        $this->value = $this->decode_if_json($this->value);

        foreach ($this->value as $index => $element_data) {
            $title_content = '';
            $title_content_count = 0;

            foreach ($this->optionsArray as $element_config) {
                $class = 'App\\FormModel\\FormData\\' . ucfirst($element_config['type']);
                $configInstance = $element_config;
                $name = $element_config['name'];
                $form_id = $this->form_id;
                $dossier_id = $this->dossier_id ?? null;

                // Instantiate the form element class
                if (class_exists($class)) {
                    $instance = new $class($configInstance, $name, $form_id, $dossier_id);
                } else {
                    $instance = new AbstractFormData($configInstance, $name, $form_id, $dossier_id);
                }
                // Handle array data to avoid Array to string conversion error
                if (isset($element_data[$element_config['name']])) {
                    // Recursively decode JSON and handle arrays
                    $instance->value = $element_data[$element_config['name']]['value'];
                } else {
                    $instance->value = ''; // Or handle default value here
                }


                $element_render = $instance->render_pdf();

                if ($element_render) {
                    $data .= '<tr><td style="width:100%;border:1px solid #ccc;border-collapse: collapse;padding-left:12px;padding-bottom:15px">';
                    $data .= $element_render;
                    $data .= '</td></tr>';
                }


            }
        }
        $data .= '</table></div>';
        // print_r($data);
        // return false;
        return $data;
    }





}