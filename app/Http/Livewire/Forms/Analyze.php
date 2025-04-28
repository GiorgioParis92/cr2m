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


        $values_to_fill=[];
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
             
                $multipartData = [];

                // Build the multipart array where each key => value 
                // in $data goes to "name" => key, "contents" => value.
                foreach ($data as $key => $value) {
                    $multipartData[] = [
                        'name'     => $key,
                        'contents' => $value,
                    ];
                }
                if(auth()->user()->id==1) {
                    print_r($data);
                }

                if(!empty($data) && count($options['values_check'])==count($data)) {

               
                $client = new Client();
        
                $response = $client->post(
                    $options['api_url'],
                    [
                        'headers' => [
                            'api-key' => 'SkjQxOoh3BT6bgU', // Use env() or config() instead of hardcoding in real apps
                        ],
                        'multipart' => $multipartData
                    ]
                );
          

                if($response->getStatusCode()==200) {

                    $responseBody = $response->getBody()->getContents();
    
                    // Decode JSON to associative array
                    $responseData = json_decode($responseBody, true);
                
                    // Optional: check if decoding was successful
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $result=(json_encode($responseData)); // Or use it as needed

                        FormsData::updateOrCreate(
                            [
                                'dossier_id' => $this->dossier_id,
                                'form_id' => $this->form_id,
                                'meta_key' => $this->conf['name']
                            ],
                            [
                                'meta_value' => ''.$result.''
                            ]
                        );
                        $this->updatedJson(''.$result.'');
                        $this->emit($this->conf['name']);
                    } else {
                        dd('Invalid JSON response', $responseBody);
                    }
                
                 
                }

                // Return or process the response as needed
                return response()->json([
                    'status' => $response->getStatusCode(),
                    'body'   => json_decode($response->getBody(), true),
                ]);
            }

            }
           
        } else {
      

            if(auth()->user()->id==1) {
                dump($this->value);
            }

            $json=(json_decode($this->value, true));   
            if($json['output_0']['success']) {
                $this->invalidGroups = (new JsonValidator())->getInvalidGroups($this->value);
                $this->ValidGroups = (new JsonValidator())->getValidGroups($this->value);
            } else {
                $this->updatedValue('');
                $this->invalidGroups = [];
                $this->ValidGroups = [];
            }

            $this->emit($this->conf['name']);
          
            $options=json_decode($conf['options'],true);

            if(isset($options['fill_values'])) {
              
    
           
                    foreach($options['fill_values'] as $key => $value) {
                        $values_to_fill[$key] = $value;
                    }
    
    
                
               
            }
        
            foreach($this->ValidGroups as $group) {
                foreach($group['groups'] as $values) {
                     
                    foreach($values['tags'] as $tag) {
                
                        if( in_array($tag,$values_to_fill)) {
                            $key = array_search($tag, $values_to_fill);
                            if ($key !== false && is_numeric( $key )) {
                                
                                FormsData::updateOrCreate(
                                    [
                                        'dossier_id' => $this->dossier_id,
                                        'form_id' => $key,
                                        'meta_key' => $tag
                                    ],
                                    [
                                        'meta_value' => ''.$values['value'].''
                                    ]
                                );
                             
                                $this->emit($key);
                            }
                        }

                    }

                }
            }


            // dd('ok');
        }
   
    }

    public function getErrorMessage()
    {
        return 'La valeur ne peut être vide';
    }

    protected function validateValue($value): bool
    {
        return true;
    }

    public function render()
    {
        return view('livewire.forms.analyze', [
            'invalidGroups' => $this->invalidGroups
        ]);
    }
}
