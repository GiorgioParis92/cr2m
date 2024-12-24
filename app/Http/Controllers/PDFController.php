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
use App\Models\Form;
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

            $form_config = FormConfig::where('form_id', $request->form_id)
                ->where('name', $request->name)
                ->first();

            if(isset($form_config->options)) {
                $config = json_decode($form_config->options);
            } else {
                $config=[];
            }
           
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
                    'form_id' => $request->form_id,
                    'meta_key' => $request->template
                ],
                [
                    'meta_value' => $directPath
                ]
            );

            if ($update) {
                // if ($dossier && $dossier->etape) {
                //     $orderColumn = $dossier->etape->order_column;
                // } else {
                //     // Handle the case where $dossier or $dossier->etape is null
                //     $orderColumn = null;
                // }
                // $docs = getDocumentStatuses($dossier->id, $orderColumn);


                // Dossier::where('id', $dossier->id)->update([

                //     'updated_at' => now(),
                // ]);

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
                                foreach ($v as $kk => $valeur) {

                                    $all_data[$kk] = $valeur;
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
        // Validate the request
        $validated = $request->validate([
            'dossier_id' => 'nullable',
        ]);

        // Retrieve the dossier
        $dossier = $this->getDossier($validated['dossier_id']);
        if (!$dossier) {
            return response()->json(['message' => 'Dossier not found'], 404);
        }

        // Set up folder paths
        $folderPath = "public/dossiers/{$dossier->folder}";
        $directPath = "dossiers/{$dossier->folder}";
        $this->ensureDirectoryExists($folderPath);

        // Process form configuration if dossier_id is provided
        if (isset($validated['dossier_id'])) {
            $config = $this->getFormConfig($request->form_id, $request->name);
            if (!$config) {
                return response()->json(['message' => 'FormConfig not found'], 404);
            }

            $optionsArray = $this->parseConfigOptions($config->options);
            $allData = $this->loadAllDossierData($dossier);
        }

        // Initialize PDF
        $pdf = new Fpdi();
        $templatePath = public_path($optionsArray['template'] . '.pdf');
        $pageCount = $pdf->setSourceFile($templatePath);

        // Process each page of the PDF
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $templateId = $pdf->importPage($pageNo);
            $pdf->useTemplate($templateId);

            $pdf->SetXY(0, 0);
            $pdf->SetAutoPageBreak(false);
            $pdf->SetFont('Helvetica');

            if (isset($optionsArray['config'][$pageNo])) {
                foreach ($optionsArray['config'][$pageNo] as $fillDataConfig) {
                    $this->processFillDataConfig($pdf, $fillDataConfig, $allData, $dossier);
                }
            }
        }

        // Save the PDF
        $fileName = "{$request->name}.pdf";
        $filePath = "{$folderPath}/{$fileName}";
        $this->ensureDirectoryExists($folderPath);

        $pdfContent = $pdf->output('', 'S');
        Storage::put($filePath, $pdfContent);

        // Update forms data and dossier timestamp
        $this->updateFormsData($dossier->id, $request->form_id, $request->name, "{$directPath}/{$fileName}");
        $this->updateDossierTimestamp($dossier->id);

        // Get document statuses
        $orderColumn = $dossier->etape->order_column ?? null;
        $docs = getDocumentStatuses($dossier->id, $orderColumn);

        return response()->json([
            'message' => 'PDF generated and saved successfully',
            'file_path' => Storage::url($filePath),
            'path' => "{$directPath}/{$fileName}",
        ], 200);
    }

    /**
     * Retrieve the dossier based on dossier_id.
     */
    private function getDossier($dossierId)
    {
        if (is_numeric($dossierId)) {
            return Dossier::with('mar', 'etape')->find($dossierId);
        }

        return Dossier::with('mar', 'etape')->where('folder', $dossierId)->first();
    }

    /**
     * Ensure the directory exists.
     */
    private function ensureDirectoryExists(string $folderPath): void
    {
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath);
        }
    }

    /**
     * Retrieve the form configuration.
     */
    private function getFormConfig($formId, $name)
    {
        return FormConfig::where([
            ['form_id', '=', $formId],
            ['name', '=', $name],
            ['type', '=', 'fillable'],
        ])->first();
    }

    /**
     * Parse the configuration options.
     */
    private function parseConfigOptions($options)
    {
        $jsonString = str_replace(["\n", '', "\r"], '', $options);
        $optionsArray = json_decode($jsonString, true);

        return is_array($optionsArray) ? $optionsArray : [];
    }

    /**
     * Load all dossier data.
     */
    private function loadAllDossierData($dossier)
    {
        return load_all_dossier_data($dossier);
    }

    /**
     * Process the fill data configuration for the PDF.
     */
    private function processFillDataConfig($pdf, $fillDataConfig, $allData, $dossier)
    {
        // Set font properties
        $font = $fillDataConfig['font'] ?? 'Helvetica';
        $fontSize = $fillDataConfig['font-size'] ?? 12;
        $pdf->SetFont($font);
        $pdf->SetFontSize($fontSize);
        $pdf->SetFontSpacing($fillDataConfig['letter-spacing'] ?? 0);

        $value = '';
        $formId = $fillDataConfig['form_id'];

        foreach ($fillDataConfig['tags'] as $tag) {
            $currentValue = $this->getCurrentValue($fillDataConfig, $allData, $formId, $tag);
            $value .= ' ' . $currentValue;
        }

        $xPos = $fillDataConfig['position'][0];
        $yPos = $fillDataConfig['position'][1];

        // Handle images
        if (isset($fillDataConfig['img'])) {
            $this->processImage($pdf, $fillDataConfig, $dossier, $allData, $formId, $tag);
        }
        // Handle tables
        elseif (isset($fillDataConfig['table'])) {
            $this->processTable($pdf, $fillDataConfig, $allData, $formId, $tag);
        }
        // Write text
        else {
            $pdf->SetXY($xPos, $yPos);
            $pdf->Write(0, $value);
        }
    }

    /**
     * Retrieve the current value for a given tag.
     */
    private function getCurrentValue($fillDataConfig, $allData, $formId, $tag)
    {
        $currentValue = '';

        if ($tag !== '' && isset($allData[$fillDataConfig['data_origin']][$formId][$tag])) {
            $currentValue = $allData[$fillDataConfig['data_origin']][$formId][$tag];
            $currentValue = $this->applyEvaluations($fillDataConfig, $currentValue);
            $currentValue = $this->applyOperations($fillDataConfig, $currentValue);
            $currentValue = $this->applyReplacements($fillDataConfig, $currentValue);
        }

        return $currentValue;
    }

    /**
     * Apply evaluations to the current value.
     */
    private function applyEvaluations($fillDataConfig, $currentValue)
    {
        if (!isset($fillDataConfig['eval'])) {
            return $currentValue;
        }

        try {
            $evaluations = is_array($fillDataConfig['eval']) ? $fillDataConfig['eval'] : [$fillDataConfig['eval']];
            $newValue = '';

            foreach ($evaluations as $eval) {
                $queryString = str_replace('$$value$$', $currentValue, $eval);
                $evalResult = eval ('return ' . $queryString . ';');
                $newValue .= !empty($evalResult) ? $evalResult : '';
                $newValue .= ' ';
            }

            return $newValue;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Apply mathematical operations to the current value.
     */
    private function applyOperations($fillDataConfig, $currentValue)
    {
        if (!isset($fillDataConfig['operation'])) {
            return $currentValue;
        }

        $operation = $fillDataConfig['operation'];

        return $this->handleOperation($currentValue, $operation['type'], $operation['value']);

    }

    private function handleOperation($value, $operationType, $value2)
    {
        $value = ((float) $value);
        if ($operationType === '*') {
            $value *= $value2;

        }
        $value = number_format($value, 2, ',', ' ');

        return $value;
    }

    /**
     * Apply replacements to the current value.
     */
    private function applyReplacements($fillDataConfig, $currentValue)
    {
        if (isset($fillDataConfig['value_replace'])) {
            if ($currentValue == $fillDataConfig['value_replace']) {
                $currentValue = $fillDataConfig['replace_by'];
            } elseif (isset($fillDataConfig['replace_if_not'])) {
                $currentValue = $fillDataConfig['replace_if_not'];
            }
        }

        if (isset($fillDataConfig['value_equal'])) {
            if ($currentValue == $fillDataConfig['value_equal']) {
                $currentValue = $fillDataConfig['replace_by'];
            } elseif (isset($fillDataConfig['replace_if_not'])) {
                $currentValue = $fillDataConfig['replace_if_not'];
            }
        }

        if (isset($fillDataConfig['format_date'])) {
            $currentValue = date($fillDataConfig['format_date'], strtotime(str_replace('/', '-', $currentValue)));
        }

        if (isset($fillDataConfig['date_now'])) {
            $currentValue = date($fillDataConfig['date_now']);

        }

        return $currentValue;
    }

    /**
     * Process image insertion.
     */
    private function processImage($pdf, $fillDataConfig, $dossier, $allData, $formId, $tag)
    {
        if (isset($fillDataConfig['signature'])) {
            // $this->insertClientSignature($pdf, $fillDataConfig, $dossier);
            $this->insertBeneficiarySignature($pdf, $fillDataConfig, $allData, $formId, $tag);
        } elseif (isset($fillDataConfig['signature_client'])) {
            $this->insertClientSignature($pdf, $fillDataConfig, $dossier);
        }
    }

    /**
     * Insert client signature into the PDF.
     */
    private function insertClientSignature($pdf, $fillDataConfig, $dossier)
    {
        $client = Client::find($dossier->mar);
        $signaturePath = storage_path('app/public/' . ($client->signature ?? ''));

        if (file_exists($signaturePath)) {
            $x = $fillDataConfig['x'] ?? 10;
            $y = $fillDataConfig['y'] ?? 10;
            $width = $fillDataConfig['width'] ?? 50;

            $pdf->Image($signaturePath, $x, $y, $width);
        }
    }

    /**
     * Insert beneficiary signature into the PDF.
     */
    private function insertBeneficiarySignature($pdf, $fillDataConfig, $allData, $formId, $tag)
    {
        $signatureData = json_decode($allData[$fillDataConfig['data_origin']][$formId][$tag])[0] ?? '';
        $signaturePath = storage_path('app/public/' . $signatureData);

        if (file_exists($signaturePath)) {
            $x = $fillDataConfig['x'] ?? 10;
            $y = $fillDataConfig['y'] ?? 10;
            $width = $fillDataConfig['width'] ?? 50;

            $pdf->Image($signaturePath, $x, $y, $width);
        }
    }

    /**
     * Process table data insertion.
     */
    private function processTable($pdf, $fillDataConfig, $allData, $formId, $tag)
    {
        if(isset($allData[$fillDataConfig['data_origin']][$formId][$tag])) {
            $tableData = json_decode($allData[$fillDataConfig['data_origin']][$formId][$tag]);
            $x = $fillDataConfig['position'][0];
            $y = $fillDataConfig['position'][1];
            $increment = $fillDataConfig['table']['increment'];
            $range = $fillDataConfig['table']['range'];
            $maxwidth = $fillDataConfig['table']['max-width'] ?? 120;
            $newY = $y;
            $i = 1;
           
            foreach ($tableData as $row) {

                if(is_array($row)) {
                foreach ($row as $k => $v) {
                    if ($k === $fillDataConfig['table']['sub_tag'] && $i >= $range[0] && $i <= $range[1]) {
        
                        $value = $v->value;
                        if (isset($fillDataConfig['table']['operation'])) {
                            $value = $this->handleOperation($value, $fillDataConfig['table']['operation']['type'], $fillDataConfig['table']['operation']['value']);
                        }
                 
                        // Write text with MultiCell for left alignment
                        $pdf->SetXY($x, $newY);
                        $pdf->MultiCell($maxwidth, $increment, $value, 0, 'L'); // Left-aligned text
        
                        // Adjust vertical position based on number of lines
                        $newY += $increment ;
                    }
                }
                } else {
             

                    if ($i >= $range[0] && $i <= $range[1]) {
$value = $allData['form_data'][$formId][$tag.'.value.'.$row.'.'.$fillDataConfig['table']['sub_tag']] ?? '';
if (isset($fillDataConfig['table']['operation'])) {
    $value = $this->handleOperation($value, $fillDataConfig['table']['operation']['type'], $fillDataConfig['table']['operation']['value']);
}

// Write text with MultiCell for left alignment
$pdf->SetXY($x, $newY);
$pdf->MultiCell($maxwidth, $increment, $value, 0, 'L'); // Left-aligned text

// Adjust vertical position based on number of lines
$newY += $increment ;
                }
            }
                $i++;
            }
        }

    }
    
    
    

    /**
     * Update or create FormsData record.
     */
    private function updateFormsData($dossierId, $formId, $metaKey, $metaValue)
    {
        FormsData::updateOrCreate(
            [
                'dossier_id' => $dossierId,
                'form_id' => $formId,
                'meta_key' => $metaKey,
            ],
            [
                'meta_value' => $metaValue,
            ]
        );
    }

    /**
     * Update the dossier's timestamp.
     */
    private function updateDossierTimestamp($dossierId)
    {
        Dossier::where('id', $dossierId)->update(['updated_at' => now()]);
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
                $content .= '<div class="btn bg-primary bg-Très modestes">' . $dossier_data['beneficiaire']['menage_mpr'] . '</div><div class="">Technicien RDV MAR 1 :' . ($lastRdv ? ($lastRdv->user->name ?? '') : '') . '</div></div></td></tr></table>';
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
                'form_id' => $request->form_id,
                'meta_key' => $request->template
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
