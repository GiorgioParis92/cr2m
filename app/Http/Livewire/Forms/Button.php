<?php

namespace App\Http\Livewire\Forms;

use App\http\Livewire\Forms\AbstractFormData;
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
use Illuminate\Support\Facades\DB;

class Button extends AbstractData
{


    protected $date_pattern = "/^(\d{2})\/(\d{2})\/(\d{4})$/";
   
    public function mount($conf, $form_id, $dossier_id) {
        parent::mount($conf, $form_id, $dossier_id);
        // $this->value=date("d/m/Y",strtotime(str_replace('/','-',$this->value)));

        $this->dossier = Dossier::find($dossier_id);

        $jsonString = str_replace(["\n", ' ', "\r"], '', $this->conf['options']);
        $this->optionsArray = json_decode($jsonString, true);
        if (!is_array($this->optionsArray)) {
            $this->optionsArray = [];
        }
        if (isset($this->optionsArray['link'])) {
            $this->form_id = $this->optionsArray['link']['form_id'];
           
            $form_config=DB::table('forms_config')->where('form_id',$this->form_id)
            ->where('id',$this->optionsArray['link']['id'])
            ->first();

            $this->form_id=$form_config->form_id;

            // $this->conf=$form_config;

            $config = \DB::table('forms_data')
            ->where('form_id', $this->form_id)
            ->where('dossier_id', $dossier_id)
            ->where('meta_key', $this->conf['name'])
            ->first();

      
        $this->value = $config->meta_value ?? '';

            // $jsonString = str_replace(["\n", ' ', "\r"], '', $form_config->options);
            // $this->optionsArray = json_decode($jsonString, true);
           
        }


        if($this->value) {
  
                $rapport=FormsData::where('dossier_id',$dossier_id)->where('meta_key','rapport_cofrac')->first();
               

                if(isset($rapport)) {
                    $this->rapport_inspection=$rapport->meta_value;
                } else {
                    $demandeId = $this->value; // Set your demande_id dynamically
                    $url = "https://crm.elitequalityinspection.fr/api/rapports/show_by_demande?demande_id=" . urlencode($demandeId);
                    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI3IiwianRpIjoiNWU2ODVjMzQ0ZDlkMGY2YzJhOTY2OGIyMTJkMDBmYjE5YTIyM2MzNmE1NmI3MWZhM2ZmMjVkNWRkYWE3MmMwMGQ3ZTQwOTk4NjgyYzE4NDkiLCJpYXQiOjE3Mzk0NTA5NjAuMzI4NDA2LCJuYmYiOjE3Mzk0NTA5NjAuMzI4NDA4LCJleHAiOjE3NzA5ODY5NjAuMzIyNzY2LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.fOCyZWCHXiumTs35mt08KoDG9deoRGuiU2nO5YwC9Rwhmo0wuqwi_Xsjg9mHJFsBh8vZy_80477sf0bYiJmoWC1ApHH9Rr0uSBZ8UI8yRpyC6ojLG5fdEKWDu9PMTE6rRrTKpW4wex8FHWTOlEYI0Q5LtBRNjY9_71Hi1FjzFRKheEdUUXE6o_KZVhzg5KVq08Ovxublochqml-UxQUXujzEVnm6QwOGNrwS1XqXQ-dnOraonY8u5_GnbiRZCd2tePvoF9fKQ-Bh-c7DUuRxpZemv6qMg-VLaz6Avwe3Yn38vsi4_hTi4PcyjtBYno3uaD2Q-hwvyDQ3YhPr98JW4mYRK8XiKbQCmSSMlqKJJ907ziF1R-4phZU9wymu0Nh7Yl4c1L_BvocODUugETuNe7TGKBhUJdZ2DQArjIBOgVHfqo2bNYGTQtDLkfyT3VR-YgGmqoAGguWwsjJo5e556nYPWL7jqTGX_lLP1eVdESRyHcgcEhvDiuwpKvYAKVABALd3278gJeYuir3eG6wD48yfv7NptfRve-OAaT4w8n9pESGQMoOUiA7tfn7pjs0hKpGbrMdEoC1x32DOenzMf-fcnb3XyGd3OMSzKJwHbvCa95DWI6y4spS0szdxI59QAh4Br03L_Bu8IjsG_8ELzpv5RFkkIZECMYQBFs2EiXc";
                    // $this->rapport=$response;
                    $ch = curl_init();
                    
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Accept: */*",
                        "Authorization: Bearer $token"
                    ]);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    // Handle the response
                    if ($httpCode === 200) {
                        $data = json_decode($response, true);
                        $this->rapport=$data;
    
                        if(isset($data['rapport_id'])) {
                            $this->rapport=$data['rapport_id'];

                            if(isset($data['date_rapport'])) {
                                $this->rdv_cofrac=date("d/m/Y",strtotime(str_replace('/','-',$data['date_rapport'])));
                                FormsData::updateOrCreate(
                                    [
                                        'dossier_id' => $this->dossier_id,
                                        'form_id' => '73',
                                        'meta_key' => 'rdv_cofrac'
                                    ],
                                    [
                                        'meta_value' => $this->rdv_cofrac
                                    ]
                                );
                            }
                            

                            $this->rapport_inspection='https://crm.elitequalityinspection.fr/storage/rapports/rapport_'.$data['rapport_id'].'/'.$data['nom_rapport'].'.pdf';
                       
                            FormsData::updateOrCreate(
                                [
                                    'dossier_id' => $this->dossier_id,
                                    'form_id' => '86',
                                    'meta_key' => 'rapport_cofrac'
                                ],
                                [
                                    'meta_value' => $this->rapport_inspection
                                ]
                            );
                       
                        }
                        // $this->rapport=$data;
                      
                        // return response()->json([
                      
                        //     'data' => $data,
                        // ]);
                    } else {
                        // $this->rapport=$response;
                        // echo "Error: HTTP Status Code $httpCode\n";
                        // return response()->json([
                      
                        //     'error' => $response,
                        // ]);
                    }
                }

              



    
           
        }

    }

    public function getErrorMessage() {
        return 'Mauvais format de date.';
    }
    public function refresh() {
        parent::mount($this->conf, $this->form_id, $this->dossier_id);

    }
    protected function validateValue($value): bool
    {

        return !empty($value) && preg_match($this->date_pattern, $value);
    }

    public function render()
    {
        return view('livewire.forms.button');
    }
}
