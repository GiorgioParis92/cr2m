<?php

namespace App\Http\Livewire\Forms;
use App\Services\JsonValidator;
use App\Models\{
 
    FormsData

};
use GuzzleHttp\Client;

use Livewire\Component;

class Analyze extends AbstractData
{
    public $invalidGroups = [];

    public function mount($conf, $form_id, $dossier_id)
    {
        parent::mount($conf, $form_id, $dossier_id);


    

        if(!($this->value)) {

            $options=json_decode($conf['options'],true);
            $result_values=[];
            if(isset($options['values_check'])) {
              

                foreach($options['values_check'] as $values) {
               
                    $val = FormsData::where('meta_key', $values)
                    ->where('dossier_id', $dossier_id)
                    ->value('meta_value');
                   
                    if(isset($val)) {
                        $result_values[$values]=json_decode($val);
                    }

                }

            }
         
           if(empty($result_values)) {
           
            $value='{"errors":"Inserer un document"}';
         
            
            

            } else {
                $data=[];
                foreach($result_values as $k=>$v) {
               
                    if(isset($v->ocr->data->oceer_document)) {
                        $data[$k]=json_encode($v->ocr->data->oceer_document,true);
                    }

                }
                dd($data);
                $multipartData = [];

                // Build the multipart array where each key => value 
                // in $data goes to "name" => key, "contents" => value.
                foreach ($data as $key => $value) {
                    $multipartData[] = [
                        'name'     => $key,
                        'contents' => $value,
                    ];
                }


                if(!empty($data)) {

               
                $client = new Client();
        
                $response = $client->post(
                    'https://app.oceer.fr/api/pipeline/start/9cb3b523-539f-4ff2-b6ac-d1bc2fa81af0',
                    [
                        'headers' => [
                            'api-key' => 'SkjQxOoh3BT6bgU', // Use env() or config() instead of hardcoding in real apps
                        ],
                        'multipart' => $multipartData
                    ]
                );
               

                if($response->getStatusCode()==200) {

                    // $this->value=$response->getBody();
                    FormsData::updateOrCreate(
                        [
                            'dossier_id' => $this->dossier_id,
                            'form_id' => $this->form_id,
                            'meta_key' => $this->conf['name']
                        ],
                        [
                            'meta_value' =>  $response->getBody()
                        ]
                    );
                    $this->emit($this->conf['name']);
                }

                // Return or process the response as needed
                return response()->json([
                    'status' => $response->getStatusCode(),
                    'body'   => json_decode($response->getBody(), true),
                ]);
            }

            }
           
        } else {

            $json=(json_decode($this->value, true));   
            if($json['output_0']['success']) {
                $this->invalidGroups = (new JsonValidator())->getInvalidGroups($this->value);
            } else {
                $this->updatedValue('');
                $this->invalidGroups = [];
            }

          
         
        }
   
    }

    public function getErrorMessage()
    {
        return 'La valeur ne peut être vide';
    }

    protected function validateValue($value): bool
    {
        return !($this->conf['required'] == 1 && empty($value));
    }

    public function render()
    {
        return view('livewire.forms.analyze', [
            'invalidGroups' => $this->invalidGroups
        ]);
    }
}
