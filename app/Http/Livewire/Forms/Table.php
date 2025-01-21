<?php

namespace App\Http\Livewire\Forms;

use Livewire\Component;
use App\Models\{
    Dossier,
    Etape,
    DossiersActivity,
    User,
    Form,
    Forms,
    FormConfig,
    Rdv,
    RdvStatus,
    Client,
    FormsData,
    Card
};

class Table extends AbstractData
{


    public function mount($conf, $form_id, $dossier_id)
    {
        parent::mount($conf, $form_id, $dossier_id);

        $data = [];
        $newvalue = [];
        $is_old = false;
        try {


            $data = ((!empty($this->value) && $this->value != null && $this->value != "null") ? json_decode($this->value, true) : []);



        } catch (\Throwable $th) {

        }

        $is_old = $this->isAssociativeJson($data);

        if ($is_old) {
            // Ensure $data is not empty and is an array
            if (!empty($data) && is_array($data)) { 
                $newvalue = []; // Initialize $newvalue as an array
        
                foreach ($data as $key => $values) {
                    // Ensure $values is an array before processing
                    if (is_array($values)) {
                        $newvalue[] = $key;
        
                        foreach ($values as $tag => $value) {
                            // Ensure $value contains the 'value' key and it's an array or scalar
                            if (isset($value['value'])) {
                                FormsData::updateOrCreate(
                                    [
                                        'dossier_id' => $this->dossier_id,
                                        'form_id' => $this->form_id,
                                        'meta_key' => $this->conf['name'] . '.value.' . $key . '.' . $tag
                                    ],
                                    [
                                        'meta_value' => (is_array($value['value']) ? json_encode($value['value']) : $value['value'])
                                    ]
                                );
                            } else {
                                // Log or handle cases where 'value' key is missing
                                error_log("Missing 'value' key in tag: $tag, key: $key");
                            }
                        }
                    } else {
                        // Log or handle unexpected $values structure
                        throw new \Exception("Unexpected structure for 'values'. Expected array, got: " . gettype($values));
                    }
                }
        
                $this->value = $newvalue;
        
                // Call the updatedValue method with the new value
                $this->updatedValue($this->value);
            } else {
                // Handle cases where $data is not a valid array
                throw new \Exception("Invalid data structure. Expected non-empty array, got: " . gettype($data));
            }
        } else {
            // When $data is not associative JSON
            $this->value = $data;
        }
        


    }
    public function updatedValue($newValue)
    {


        // Always save, regardless of validity
        FormsData::updateOrCreate(
            [
                'dossier_id' => $this->dossier_id,
                'form_id' => $this->form_id,
                'meta_key' => $this->conf['name']
            ],
            [
                'meta_value' => json_encode($newValue)
            ]
        );
        $this->emit($this->conf['name']);
    }
    public function getErrorMessage()
    {
        return '';
    }


    protected function validateValue($value): bool
    {

        return true;
    }


    private function isAssociativeJson($json)
    {
        // Check if the input is an array
        if (!is_array($json)) {
            return false;
        }

        if (array_keys($json) !== range(0, count($json) - 1)) {
            return true;
        }

        // Otherwise, we recurse: if *any* sub-array is associative, return true
        foreach ($json as $value) {
            if ($this->isAssociativeJson($value)) {
                return true;
            }
        }

        // If none of the conditions match, it's not the associative JSON structure
        return false;
    }


    public function add_row() {

        $uniqueId = uniqid(); // Generate a unique id for the row
        $this->value[] = $uniqueId;



        foreach(json_decode($this->conf['options']) as $option) {
            FormsData::updateOrCreate(
                [
                    'dossier_id' => $this->dossier_id,
                    'form_id' => $this->form_id,
                    'meta_key' => $this->conf['name'] . '.value.' . $uniqueId . '.' . $option->name
                ],
                [
                    'meta_value' => ''
                ]
            );
        }
        $this->updatedValue($this->value);

    }

    public function remove_row($rowId)
    {
        // 1. Remove the rowId from $this->value array
        $this->value = array_filter($this->value, function($value) use ($rowId) {
            return $value !== $rowId;
        });

        // 2. Remove all associated data in the database
        //    Those entries have meta_key like: "tableName.value.{rowId}.{optionName}"
        $prefix = $this->conf['name'] . '.value.' . $rowId . '.';
        FormsData::where('dossier_id', $this->dossier_id)
            ->where('form_id', $this->form_id)
            ->where('meta_key', 'LIKE', $prefix . '%')
            ->delete();

        // 3. Persist the updated array
        $this->updatedValue($this->value);
    }
    public function render()
    {
        return view('livewire.forms.table');
    }
}
