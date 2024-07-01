<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\View;

class Result extends AbstractFormData
{
    public function render(bool $is_error)
    {



        $jsonString = str_replace(["\n", '', "\r"], '', $this->config->options);
        $optionsArray = json_decode($jsonString, true);

        if (isset($optionsArray['arguments'])) {
            foreach ($optionsArray['arguments'] as $key => $data) {
                $sql_command = str_replace($key, eval ($data), $sql_command);
            }
        }

        $total=0;
        if (isset($optionsArray['operands'])) {
            foreach ($optionsArray['operands'] as $operand) {

                if ($operand['operand'] == 'x') {
                    $total = 1; // Initialize to 1 for multiplication
                    foreach ($operand['tags'] as $tag) {
                        $tagValue = number_format((float) $this->getOtherValue($tag), 2, '.', '');


                        if ($tagValue === '' || $tagValue === null) {
                            if (is_numeric($tag)) {
                                $tagValue = $tag;
                            } else {
                                $tagValue = 1;
                            }
                        }

                        $total *= $tagValue;
                    }
                }


         
                if ($operand['operand'] == '/') {
         
                    foreach ($operand['tags'] as $tag) {

                        $tagValue = $this->getOtherValue($tag) ? number_format((float) $this->getOtherValue($tag), 2, '.', '') : '';

                        
                        if ($tagValue === '' || $tagValue === null ) {

                            if (is_numeric($tag)) {
                                $tagValue = $tag;
                                
                            } else {
                                $tagValue = 1;
                            }
                        }
                        if ($tagValue != 0) {
                            $total /= $tagValue;
                        }

                    }
                }

                if ($operand['operand'] == '-') {
                 

                    foreach ($operand['tags'] as $tag) {
                        $tagValue = number_format((float) $this->getOtherValue($tag), 2, '.', '');

                        if ($tagValue === '' || $tagValue === null) {
                            if (is_numeric($tag)) {
                                $tagValue = $tag;
                            } else {
                                $tagValue = 1;
                            }
                        }
                 
                        
                        $total -= $tagValue;
                    }
                }

                if ($operand['operand'] === '+') {
                   
                    
                    
                    foreach ($operand['tags'] as $tag) {

                        $tagValue = number_format((float) $this->getOtherValue($tag), 2, '.', '');


                        $total += $tagValue;
                    }
                }
                if ($operand['operand'] === '<') {
                   
                    
                    
                    foreach ($operand['tags'] as $tag) {

                        $tagValue = number_format((float) $this->getOtherValue($tag), 2, '.', '');

                        if($total>=$tagValue) {
                            $total = $tagValue;
                        }
                        
                    }
                }

                if ($operand['operand'] === '>') {
                   
                    
                    
                    foreach ($operand['tags'] as $tag) {

                        $tagValue = number_format((float) $this->getOtherValue($tag), 2, '.', '');

                        if($total<=$tagValue) {
                            $total = $tagValue;
                        }
                        
                    }
                }

            }
        }
    
        $this->value = number_format((float) $total, 2, '.', '');
        $this->save_value();
        $wireModel = "formData.{$this->form_id}.{$this->name}";



        $readonly = '';
        if (isset($optionsArray['readonly'])) {
            $readonly = (($optionsArray['readonly'] == true) ? 'disabled' : '');
        }




        $data = '<div class="form-group col-sm-12 ' . ($this->config->class ?? "") . '">';


        $data .= '<label style="display:inline-block">' . $this->config->title . '</label>';


        $data .= $this->generate_loading();


        $data .= '<input ' . $readonly . ' wire:model.lazy="' . $wireModel . '" class="form-control " type="text" name="' . $this->name . '" ';

        if ($this->config->required) {
            $data .= ' required ';
        }
        $data .= ' value="' . ($this->value) . '">';

        if (!$this->check_value()) {
            $data .= '<div  class="invalid-feedback">' . $this->get_error_message() . '</div>';

        } else {
            if ($this->prediction) {
                $data .= '<span class="prediction">Predicted by OCEER</span>';
            }
        }
        $data .= '</div>';


        return $data;

    }


    public function check_value()
    {
        if ($this->config->required && $this->value == '') {
            return false;
        }
        return true;
    }

  

    public function get_error_message()
    {
        if ($this->config->required && $this->value == '') {
            return 'La valeur ne peut pas être vide';
        }

        return 'Erreur';
    }

}