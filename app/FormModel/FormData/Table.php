<?php
namespace App\FormModel\FormData;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Dossier;

class Table extends AbstractFormData
{

    // public $data_array=[];
    public function __construct($config, $name, $form_id, $dossier_id)
    {
        parent::__construct($config, $name, $form_id, $dossier_id);
       
        $this->value=$this->init_value();
     

    }
    public function render(bool $is_error)
    {
        $data='<div class="btn btn-primary" wire:click="add_row(\'ajout_piece\','.$this->form_id.')">Add row</div>';
        $data.='<div class="btn btn-primary" wire:click="remove_row(\'ajout_piece\','.$this->form_id.',0)">remove row</div>';
        return $data;
    }
   
    public function add_element()
    {
     
        $element=['test'];
        $this->value[]= $element;
        $this->save_value();
    }
    public function remove_element($index)
    {
    
        unset($this->value[$index]);
        $this->save_value();
    }
    public function init_value()
    {
        if(is_array($this->value)) {
            return $this->value;
        }
        $value=json_decode($this->value,true);
        if(!isset($value) || $value=='') {
            $value=[];
        }

        return $value;
  
    }

    public function generate_value()
    {
        if(is_array($this->value)) {
            return json_encode($this->value);
        }

        return '[]';
     
    }
}