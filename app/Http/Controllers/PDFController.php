<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spipu\Html2Pdf\Html2Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use setasign\Fpdi\Tcpdf\Fpdi; // Correctly import the FPDI class for TCPDF
use setasign\Fpdi\PdfReader;  // Import PdfReader if needed
use App\Models\Beneficiaire;
use App\Models\Client;
use App\Models\Fiche;
use App\Models\FormsData;
use App\Models\FormConfig;
use App\Models\Rdv;
use App\Models\Dossier;
use Illuminate\Support\Facades\DB;
use App\Models\Etape;
use PDF; // Import the PDF facade at the top
use Dompdf\Dompdf;
use Dompdf\Options;



class PDFController extends Controller
{
    public function generatePDF(Request $request)
    {




        // Validate the incoming request data
        $validated = $request->validate([
            'template' => 'nullable|string',
            'name' => 'nullable|string',
            'dossier_id' => 'nullable',
            'generation' => 'nullable',
            'identify' => 'nullable',
        ]);

        if (is_numeric($validated['dossier_id'])) {
            $dossier = Dossier::with('mar')->where('id', $validated['dossier_id'])->first();
        } else {
            $dossier = Dossier::with('mar')->where('folder', $validated['dossier_id'])->first();

        }

        if (isset($request->form_id)) {

      $formConfig = FormConfig::where('form_id', $request->form_id)
    ->where('name', $request->name)
    ->first();


            $config = json_decode($form_config->options);
            $validated['generation'] = $config->on_generation ?? [];
            $request->template = $config->template ?? [];
            $validated['template'] = $config->template ?? [];

        }
        if (isset($validated['generation']) && !empty($validated['generation'])) {
            eval ($validated['generation']);
        }
        $title = '';
        // Determine the HTML content to use
        if (isset($validated['template'])) {
            $htmlContent = $this->getTemplateHtml($validated['template'], $dossier->id, $config = null, $title, $content = null, $send_data = true);
        } else {
            $htmlContent = '';
        }
        // dd($htmlContent);
        // Generate the PDF using Html2Pdf
        $html2pdf = new Html2Pdf();

        $html2pdf->writeHTML($htmlContent);

        $pdfOutput = $html2pdf->output('', 'S'); // Output as string

        if ($request->display) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="document.pdf"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            echo $pdfOutput;
        }



        // Check if dossier_id is provided
        if (isset($validated['dossier_id'])) {
            $dossierId = $dossier->id;
            $folderPath = "public/dossiers/{$dossier->folder}";
            $directPath = "dossiers/{$dossier->folder}";


            // Create the folder if it does not exist
            if (!Storage::exists($folderPath)) {
                Storage::makeDirectory($folderPath);
            }

            // Save the PDF file to the folder
            $fileName = ($validated['name'] ?? 'document') . ".pdf";
            $filePath = "{$folderPath}/{$fileName}";
            $directPath = "{$directPath}/{$fileName}";
            $result = Storage::put($filePath, $pdfOutput);
            if (!$result) {
                \Log::error("Failed to save file at path: " . $filePath);
                return response()->json(['error' => 'Failed to save file'], 500);
            }





            $update = FormsData::updateOrCreate(
                [
                    'dossier_id' => $dossier->id,
                    'form_id'    => $request->form_id,
                    'meta_key'   => $request->template
                ],
                [
                    'meta_value' => $directPath
                ]
            );

            if ($update) {
                if ($dossier && $dossier->etape) {
                    $orderColumn = $dossier->etape->order_column;
                } else {
                    // Handle the case where $dossier or $dossier->etape is null
                    $orderColumn = null;
                }
                $docs = getDocumentStatuses($dossier->id, $orderColumn);


                Dossier::where('id', $dossier->id)->update([
                
                    'updated_at' => now(),
                ]);

            }

            $identify = '';

            // if (isset($validated['identify'])) {
            //     $fullFilePath = Storage::path($filePath); // This will return the absolute path
            //     // Call identify_doc with the full path
            //     $identify = $this->identify_doc($fullFilePath);

            // }

            // Return success response
            return response()->json([
                'message' => 'PDF generated and saved successfully',
                'file_path' => Storage::url($filePath), // Adjusted this line
                'path' => $directPath, // Adjusted this line
                'identify' => json_decode($identify) ?? '' // Adjusted this line
            ], 200);
        } else {
            // Return the PDF as a response
            return response($pdfOutput)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="document.pdf"');
        }
    }

    private function getTemplateHtml($template, $dossier_id, $config = null, $title = '', $content = null, $send_data = null)
    {
        // Check if the template view exists
        $templatePath = 'templates.' . $template;

        // $dossier = Dossier::with('dossiersData','mar_client')->where('folder', $dossier_id)->first();

        if (is_numeric($dossier_id)) {
            $dossier = Dossier::with('dossiersData', 'mar_client')->where('id', $dossier_id)->first();
        } else {
            $dossier = Dossier::with('dossiersData', 'mar_client')->where('folder', $dossier_id)->first();

        }


        if ($send_data) {
            $all_data = load_all_dossier_data($dossier);
            if (is_array($all_data)) {
            foreach ($all_data as $key => $data) {
             
                if (is_array($data)) {
                   
                    foreach ($data as $k => $v) {
                    
                        if (is_array($v)) {
                            foreach ($v as $kk=>$valeur) {
                         
                              $all_data[$kk]= $valeur;
                                if (is_array($valeur)) {
                                    foreach ($valeur as $c => $vv) {
                                        array_push($all_data, [$c => $vv]);
                                    }
                                }
                            }
                        }
                    }
                }
            }



            }
        } else {
            $all_data = [];
        }


        if (View::exists($templatePath)) {

            return view($templatePath, ['all_data' => $all_data, 'dossier' => $dossier, 'config' => $config, 'title' => $title, 'content' => $content])->render();
        } else {
            throw new \Exception('Invalid template specified');
        }
    }
    public function fillPdf(Request $request)
    {



        $validated = $request->validate([
            'dossier_id' => 'nullable',
        ]);

        if (is_numeric($validated['dossier_id'])) {
            $dossier = Dossier::with('mar', 'etape')->where('id', $validated['dossier_id'])->first();
        } else {
            $dossier = Dossier::with('mar', 'etape')->where('folder', $validated['dossier_id'])->first();

        }

        $dossierId = $dossier->id;
        $folderPath = "public/dossiers/{$dossier->folder}";
        $directPath = "dossiers/{$dossier->folder}";

        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath);
        }

        if (isset($validated['dossier_id'])) {
           $config = FormConfig::where('form_id', $request->form_id)
    ->where('name', $request->name)
    ->where('type', 'fillable')
    ->first();


            $jsonString = str_replace(["\n", '', "\r"], '', $config->options);
            $optionsArray = json_decode($jsonString, true);
            if (!is_array($optionsArray)) {
                $optionsArray = [];
            }
            // $dossier = Dossier::where('id', $validated['dossier_id'])->first();

            if (is_numeric($validated['dossier_id'])) {
                $dossier = Dossier::where('id', $validated['dossier_id'])->first();
            } else {
                $dossier = Dossier::where('folder', $validated['dossier_id'])->first();
            }

            $all_data = load_all_dossier_data($dossier);

        }
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile(public_path($optionsArray["template"] . '.pdf'));

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $templateId = $pdf->importPage($pageNo);
            $pdf->useTemplate($templateId);

            $pdf->SetXY(0, 0);
            $pdf->SetAutoPageBreak(false);
            $pdf->SetFont('Helvetica');

            if (isset($optionsArray["config"][$pageNo])) {
                foreach ($optionsArray["config"][$pageNo] as $fill_data_config) {

                    $font = isset($fill_data_config["font"]) ? $fill_data_config["font"] : 'Helvetica';
                    $pdf->SetFont($font);


                    $font_size = isset($fill_data_config["font-size"]) ? $fill_data_config["font-size"] : 12;
                    $pdf->SetFontSize($font_size);

                    $value = "";
                    $form_id = $fill_data_config["form_id"];
                    foreach ($fill_data_config["tags"] as $tag) {
                        $current_value = '';

                        if ($tag != "" && isset($all_data[$fill_data_config["data_origin"]][$form_id][$tag])) {
                            $current_value = $all_data[$fill_data_config["data_origin"]][$form_id][$tag];
                            if (isset($fill_data_config["eval"])) {

                                if (isset($fill_data_config["eval"])) {
                                    try {
                                        // Replace `$$value$$` in the eval string with the actual value
                                        if (is_array($fill_data_config["eval"])) {

                                        } else {
                                            $fill_data_config["eval"][] = $fill_data_config["eval"];
                                        }
                                        $new_value = '';
                                        foreach ($fill_data_config["eval"] as $eval) {
                                            $queryString = str_replace('$$value$$', $current_value, $eval);

                                            // Use eval to execute the query and assign the result
                                            $evalResult = eval ('return ' . $queryString . ';');
                                            // Check if the result is not empty and assign it to $current_value
                                            $new_value .= !empty($evalResult) ? $evalResult : null;
                                            $new_value .= ' ';
                                        }
                                        $current_value = $new_value;


                                    } catch (\Throwable $th) {
                                        // Catch any errors during eval execution
                                        $current_value = $th->getMessage();
                                    }
                                }
                            }
                            if (isset($fill_data_config["value_replace"])) {
                                if ($current_value == $fill_data_config["value_replace"]) {
                                    $current_value = $fill_data_config["replace_by"];
                                } else {
                                    if (isset($fill_data_config["replace_if_not"])) {
                                        $current_value = $fill_data_config["replace_if_not"];
                                    }
                                }
                            }

                            if (isset($fill_data_config["value_equal"])) {
                                if ($current_value == $fill_data_config["value_equal"]) {
                                    $current_value = $fill_data_config["replace_by"];
                                } else {
                                    if (isset($fill_data_config["replace_if_not"])) {
                                        $current_value = $fill_data_config["replace_if_not"];
                                    }
                                }
                            }
                        }
                        if (isset($fill_data_config["format_date"])) {

                            $current_value = date($fill_data_config["format_date"], strtotime(str_replace('/', '-', $current_value)));
                        }
                        if (isset($fill_data_config["date_now"])) {

                            $current_value = date($fill_data_config["date_now"], strtotime("now"));
                        }

                        $value .= ' ' . $current_value;
                    }
                    $x_pos = $fill_data_config["position"][0];
                    $y_pos = $fill_data_config["position"][1];

                    if (isset($fill_data_config["letter-spacing"])) {
                        $pdf->SetFontSpacing($fill_data_config["letter-spacing"]);
                    } else {
                        $pdf->SetFontSpacing(0);
                    }
                    if (isset($fill_data_config["img"])) {
                        if (isset($fill_data_config['signature'])) {
                          $dossier = Dossier::where('folder', $dossier->folder)->first();


                       $client = Client::where('id', $dossier->mar)->first();


                            $signature_client = $client->signature ?? '';

                            $signaturePath = storage_path('app/public/' . $signature_client);
                  
                                // dd($signaturePath);
                            
                            
                            if (file_exists($signaturePath)) {
                                // Set default position and size if not provided

                                $x = isset($fill_data_config["x"]) ? $fill_data_config["x"] : 10;
                                $y = isset($fill_data_config["y"]) ? $fill_data_config["y"] : 10;
                                $width = isset($fill_data_config["width"]) ? $fill_data_config["width"] : 50;
                                $height = isset($fill_data_config["height"]) ? $fill_data_config["height"] : 30;

                                // Insert the image into the PDF
                                $pdf->Image($signaturePath, $x, $y, $width);
                            }
                        }

                        if (isset($fill_data_config['signature_beneficiaire'])) {


                            $current_value = json_decode($all_data[$fill_data_config["data_origin"]][$form_id][$tag])[0] ?? '';
                            $value = '';

                            $signature_client = $current_value ?? '';
                            $signaturePath = storage_path('app/public/' . $signature_client);
                            if (file_exists($signaturePath)) {

                                // Set default position and size if not provided
                                $x = isset($fill_data_config["x"]) ? $fill_data_config["x"] : 10;
                                $y = isset($fill_data_config["y"]) ? $fill_data_config["y"] : 10;
                                $width = isset($fill_data_config["width"]) ? $fill_data_config["width"] : 50;
                                $height = isset($fill_data_config["height"]) ? $fill_data_config["height"] : 30;
                                // Insert the image into the PDF
                                $pdf->Image($signaturePath, $x, $y, $width);
                            }
                        }

                    }


                    $pdf->SetXY($x_pos, $y_pos);
                    $pdf->Write(0, $value);
                }
            }
        }

        $folderPath = "public/dossiers/{$dossier->folder}";
        $folderPath2 = "dossiers/{$dossier->folder}";

        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath);
        }

        $fileName = $request->name . ".pdf";
        $filePath = "{$folderPath}/{$fileName}";
        $path = "{$folderPath2}/{$fileName}";

        $directPath = "{$directPath}/{$fileName}";


        $pdfContent = $pdf->output('', 'S'); // 'S' returns the PDF as a string
        Storage::put($filePath, $pdfContent);
        $dossier = Dossier::where('folder', $dossier->folder)->first();

      

        $update = FormsData::updateOrCreate(
            [
                'dossier_id' => $dossier->id,
                'form_id'    => $request->form_id,
                'meta_key'   => $request->name
            ],
            [
                'meta_value' => $directPath
            ]
        );

        Dossier::where('id', $dossier->id)->update([
                
            'updated_at' => now(),
        ]);

        if ($dossier && $dossier->etape) {
            $orderColumn = $dossier->etape->order_column;
        } else {
            // Handle the case where $dossier or $dossier->etape is null
            $orderColumn = null;
        }
        $docs = getDocumentStatuses($dossier->id, $orderColumn);
        return response()->json([
            'message' => 'PDF generated and saved successfully',
            'file_path' => Storage::url($filePath), // Adjusted this line
            'path' => $path // Adjusted this line
        ], 200);
    }


    public function generateConfig(Request $request)
    {

        $dossierId = $request->dossier_id;

        if (isset($request->id)) {
            $dossierId = $request->id;
        }

        if (is_numeric($dossierId)) {
            $dossier = Dossier::with('mar', 'etape')->where('id', $dossierId)->first();
        } else {
            $dossier = Dossier::with('mar', 'etape')->where('folder', $dossierId)->first();

        }

        $folderPath = "public/dossiers/{$dossier->folder}";
        $directPath = "dossiers/{$dossier->folder}";

        // Create the folder if it does not exist
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath);
        }


        // Fetch the dossier based on the provided dossier_id
        $dossier = Dossier::where('folder', $dossier->folder)->first();
        $startTime = microtime(true);
        // Load all the dossier data (assuming load_all_dossier_data is a custom helper function)


        $dossier_data = Dossier::where('id', $dossier->id)
            ->with('beneficiaire', 'fiche', 'etape', 'status')
            ->first();


        $timeAfterDossier = microtime(true) - $startTime;
        $htmlContent = '';
        $content = '';
        $configs = explode(',', $request->config_id);
        $count = 0;
        $template_name = $request->template;


        if (isset($request->form_id)) {
       $config = FormConfig::where('form_id', $request->form_id)
    ->where('name', $request->template ?? $request->name)
    ->first();


            $jsonString = str_replace(["\n", '', "\r"], '', $config->options);
            $optionsArray = json_decode($jsonString, true);
            if (!is_array($optionsArray)) {
                $optionsArray = [];
            }

            $configs = explode(',', $optionsArray['config_id']);
            $template_name = $optionsArray['template'];

        }

        // dump($timeAfterDossier);
        foreach ($configs as $config_id) {
$form = Form::where('id', $config_id)->first();

$config = FormConfig::where('form_id', $config_id)
    ->orderBy('ordering')
    ->get();
            $timeAfterConfig = microtime(true) - $startTime;
            $count++;

            // dump($timeAfterConfig);

            $title = $request->title;

            $title_content = '<div><table style="margin:auto;width:90%;margin-top:20px;border-collapse: collapse;">';
            $title_content_count = 0;

            // $all_data = load_all_dossier_data($dossier);
            $lastRdv = Rdv::with('user')
                ->where('dossier_id', $dossier->id)
                ->where('type_rdv', 1)
                ->where('status', '!=', 2)
                ->orderBy('created_at', 'desc')
                ->first();

            $content .= '<table style="margin:auto;width:90%;border-collapse: collapse;margin-top:20px">
            <tr><td class="s1 form_title" style="font-size:18px">' . ($form->form_title ?? '') . '</td></tr>
        </table>';


            if ($count == 1) {


                $content .= '<div><table style="margin:auto;width:90%;border-collapse: collapse;margin-top:20px"><tr>';
                $content .= '<td class="s2 form_title" style="width:100%;border:1px solid #ccc;border-collapse: collapse;padding-left:12px;text-align:center">';
                $content .= '<div>Coordonnées bénéficiaire</div></td></tr><tr>';
                $content .= '<td style="width:100%;border:1px solid #ccc;border-collapse: collapse;padding-left:12px;padding-bottom:15px;text-align:center"><div>';
                $content .= '<h5 class="mb-0" style="text-align:center"><b>' . $dossier_data['beneficiaire']['nom'] . ' ' . $dossier_data['beneficiaire']['prenom'] . '</b><br>' . $dossier_data['beneficiaire']['numero_voie'] . ' ' . ($dossier_data['beneficiaire']['adresse'] ?? '') . ' ' . $dossier_data['beneficiaire']['cp'] . ' ' . $dossier_data['beneficiaire']['ville'] . '<br> </h5>';
                $content .= '<h6 class="mb-0"><b>Tél : ' . $dossier_data['beneficiaire']['telephone'] . '</b> -Email : ' . $dossier_data['beneficiaire']['email'] . '<br></h6>';
                $content .= '<h6 class="mb-0"><b>N° CLAVIS : ' . ($dossier_data['reference_unique'] ?? '') . '</b></h6>';
                $content .= '<div class="btn bg-primary bg-Très modestes">' . $dossier_data['beneficiaire']['menage_mpr'] . '</div><div class="">Technicien RDV MAR 1 :' . ($lastRdv ? $lastRdv->user->name : '') . '</div></div></td></tr></table>';
                $content .= '</div>';
            }
            foreach ($config as $element) {
                if (empty($element) || empty($element->type)) {
                    $content .= '<p>Error: Configuration element is missing.</p>';
                    continue;
                }

                $class = 'App\\FormModel\\FormData\\' . ucfirst($element->type);
                if (!class_exists($class)) {
                    $content .= "Error: Class $class does not exist.";
                    continue;
                }

                try {
                    if ($element->type == 'title') {
                        if ($title_content_count > 1) {
                            $content .= $title_content . '</table></div>';
                        }
                        $title_content = '<div><table style="margin:auto;width:90%;border-collapse: collapse;margin-top:20px">';
                        $title_content_count = 0;
                    }



                    $instance = new $class($element, $element->name, $element->form_id, $dossier->id ?? null);
                    $instance->set_dossier($dossier);
                    $instance_result = $instance->render_pdf();

                    // if ($element->type == 'table' && $element->name=='ajout_mur') {

                    //     echo($instance_result);

                    // }
                    // if ($element->type == 'table' && $element->name=='ajout_piece') {

                    //     echo($instance_result);

                    // }



                    if ($instance_result) {
                        $title_content_count++;
                        if ($element->type == 'title') {
                            $title_content .= '<tr><td class="s2 form_title" style="width:100%;border:1px solid #ccc;border-collapse: collapse;padding-left:12px">' . $instance_result . '</td></tr>';

                        } else {
                            if ($element->type != 'table') {
                                $title_content .= '<tr><td style="width:100%;border:1px solid #ccc;border-collapse: collapse;padding-left:12px;padding-bottom:15px">' . $instance_result . '</td></tr>';
                            } else {
                                $title_content .= '</table><div>' . $instance_result . '</div></table>';
                                ;
                            }
                        }
                    }

                } catch (\Throwable $th) {
                    $title_content .= $element->name . ' Error: ' . $th->getMessage();
                }
            }

            if ($title_content_count != 0) {
                $content .= $title_content;
            }

            // Get the HTML content for the template
        }

        $htmlContent = $this->getTemplateHtml('config', $dossier->id, $config, $title, $content);

        // Generate the PDF using Dompdf
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); // Set a default font to avoid embedding multiple fonts
        $options->set('isFontSubsettingEnabled', true); // Enable font subsetting to embed only used glyphs
        $options->set('dpi', 72); // Reduce DPI (default is 96)
        $options->set('isRemoteEnabled', true); // Enable loading remote images

        // Create new Dompdf instance with the defined options
        $dompdf = new Dompdf($options);
        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait'); // Set paper size and orientation

        // Load HTML content into Dompdf
        $dompdf->loadHtml($htmlContent);

        // Render the HTML as PDF
        $dompdf->render();

        // Set compression level (optional)
        // $dompdf->getCanvas()->get_cpdf()->setCompression(true);

        // Output the generated PDF as a string
        $pdfOutput = $dompdf->output();

        $timeaftergenerate = microtime(true) - $startTime;


        // dump($timeaftergenerate);

        // Save the PDF file to the folder
        $fileName = ($template_name ?? 'document') . ".pdf";
        $filePath = "{$folderPath}/{$fileName}";
        $directPath = "{$directPath}/{$fileName}";
        Storage::put($filePath, $pdfOutput);

        $timeafterstore = microtime(true) - $startTime;


     

        $update = FormsData::updateOrCreate(
            [
                'dossier_id' => $dossier->id,
                'form_id'    => $request->form_id,
                'meta_key'   => $request->template
            ],
            [
                'meta_value' => $directPath
            ]
        );


        Dossier::where('id', $dossier->id)->update([
                
            'updated_at' => now(),
        ]);
        if ($dossier && $dossier->etape) {
            $orderColumn = $dossier->etape->order_column;
        } else {
            // Handle the case where $dossier or $dossier->etape is null
            $orderColumn = null;
        }
        $docs = getDocumentStatuses($dossier->id, $orderColumn);
        return response()->json([
            'message' => 'PDF generated and saved successfully',
            'file_path' => Storage::url($filePath), // Adjusted this line
            'path' => $directPath // Adjusted this line
        ], 200);
    }


    public function identify_doc($filePath)
    {

        // Get the real path of the file
        $file = Storage::path($filePath);

        // Check if the file exists and get its real path
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Use the file path directly
        $data = array(
            'service' => 'document_detection',
            'token' => '6b22c62c-924a-4aac-9eab-9faafe55e394',
            'model' => 'atlas',
            'file' => new \CURLFile($filePath), // Use \CURLFile to send file via cURL
        );

        // Send the request
        $response = makeRequest('https://oceer.fr/api/document_detection', $data);

        return $response;
    }


}
