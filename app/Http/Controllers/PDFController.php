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
use App\Models\Dossier;
use Illuminate\Support\Facades\DB;
use App\Models\Etape;



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
        ]);
        if (isset($validated['generation'])) {
            eval($validated['generation']);
        }
      
            $title='';
        // Determine the HTML content to use
        if (isset($validated['template'])) {
            $htmlContent = $this->getTemplateHtml($validated['template'], $validated['dossier_id'],$config=null,$title);
        } else {
            $htmlContent = '';
        }

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
            $dossierId = $validated['dossier_id'];
            $folderPath = "public/dossiers/{$dossierId}";
            $directPath = "dossiers/{$dossierId}";

            // Create the folder if it does not exist
            if (!Storage::exists($folderPath)) {
                Storage::makeDirectory($folderPath);
            }

            // Save the PDF file to the folder
            $fileName = ($validated['name'] ?? 'document') . ".pdf";
            $filePath = "{$folderPath}/{$fileName}";
            $directPath ="{$directPath}/{$fileName}";
            Storage::put($filePath, $pdfOutput);

            $dossier = Dossier::where('folder', $dossierId)->first();

            $update = DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $request->form_id . '',
                    'meta_key' => '' . $request->template . ''
                ],
                [
                    'meta_value' => '' . $directPath . '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            // Return success response
            return response()->json([
                'message' => 'PDF generated and saved successfully',
                'file_path' => Storage::url($filePath) // Adjusted this line
            ], 200);
        } else {
            // Return the PDF as a response
            return response($pdfOutput)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="document.pdf"');
        }
    }

    private function getTemplateHtml($template, $dossier_id,$config=null,$title)
    {
        // Check if the template view exists
        $templatePath = 'templates.' . $template;

        $dossier = Dossier::where('folder', $dossier_id)->first();

        $all_data = load_all_dossier_data($dossier);

        if (View::exists($templatePath)) {
            return view($templatePath, ['dossier' => $dossier, 'all_data' => $all_data,'config' => $config,'title' => $title])->render();
        } else {
            throw new \Exception('Invalid template specified');
        }
    }
    public function fillPdf(Request $request)
    {



        $validated = $request->validate([
            'dossier_id' => 'nullable',
        ]);

        $dossierId = $validated['dossier_id'];
        $folderPath = "public/dossiers/{$dossierId}";
        $directPath = "dossiers/{$dossierId}";

        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath);
        }

        if (isset($validated['dossier_id'])) {
            $config = \DB::table('forms_config')
                ->where('form_id', $request->form_id)
                ->where('name', $request->name)
                ->where('type', 'fillable')
                ->first();

            $jsonString = str_replace(["\n", '', "\r"], '', $config->options);
            $optionsArray = json_decode($jsonString, true);
            if (!is_array($optionsArray)) {
                $optionsArray = [];
            }
            $dossier = Dossier::where('id', $validated['dossier_id'])->first();

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

                        if(isset($fill_data_config["date_now"])) {
                          
                            $current_value = date($fill_data_config["date_now"],strtotime("now"));
                        }

                        $value .= ' ' . $current_value;
                    }
                    $x_pos = $fill_data_config["position"][0];
                    $y_pos = $fill_data_config["position"][1];

                    if(isset($fill_data_config["letter-spacing"])) {
                        $pdf->SetFontSpacing($fill_data_config["letter-spacing"]);
                    } else {
                        $pdf->SetFontSpacing(0);
                    }

                    

                    $pdf->SetXY($x_pos, $y_pos);
                    $pdf->Write(0, $value);
                }
            }
        }

        $folderPath = "public/dossiers/{$validated['dossier_id']}";

        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath);
        }

        $fileName = $request->name . ".pdf";
        $filePath = "{$folderPath}/{$fileName}";

        $directPath ="{$directPath}/{$fileName}";


        $pdfContent = $pdf->output('', 'S'); // 'S' returns the PDF as a string
        Storage::put($filePath, $pdfContent);
        $dossier = Dossier::where('folder', $dossierId)->first();


        $update = DB::table('forms_data')->updateOrInsert(
            [
                'dossier_id' => '' . $dossier->id . '',
                'form_id' => '' . $request->form_id . '',
                'meta_key' => '' . $request->name . ''
            ],
            [
                'meta_value' => '' . $directPath . '',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json([
            'message' => 'PDF generated and saved successfully',
            'file_path' => Storage::url($filePath) // Adjusted this line
        ], 200);
    }


    public function generateConfig(Request $request)
    {

        $dossierId = $request->dossier_id;
        $folderPath = "public/dossiers/{$dossierId}";
        $directPath = "dossiers/{$dossierId}";

        // Create the folder if it does not exist
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath);
        }
 

        // Fetch the dossier based on the provided dossier_id
        $dossier = Dossier::where('folder', $request->dossier_id)->first();
        // Load all the dossier data (assuming load_all_dossier_data is a custom helper function)
        $all_data = load_all_dossier_data($dossier);
    
        $config=DB::table('forms_config')->where('form_id',$request->config_id)->orderBy('ordering')->get();

        $title=$request->title;
 
        // Get the HTML content for the template
        $htmlContent = $this->getTemplateHtml('config', $request->dossier_id,$config,$title);
    


 
        // Generate the PDF using Html2Pdf
        $html2pdf = new Html2Pdf();
    
        // Convert the HTML content to PDF
        $html2pdf->writeHTML($htmlContent);
        $pdfOutput = $html2pdf->output('', 'S'); // Output as string
        // Save the PDF file to the folder
        $fileName = ($request->template ?? 'document') . ".pdf";
        $filePath = "{$folderPath}/{$fileName}";
        $directPath ="{$directPath}/{$fileName}";
        Storage::put($filePath, $pdfOutput);


        $update = DB::table('forms_data')->updateOrInsert(
            [
                'dossier_id' => '' . $dossier->id . '',
                'form_id' => '' . $request->form_id . '',
                'meta_key' => '' . $request->template . ''
            ],
            [
                'meta_value' => '' . $directPath . '',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json([
            'message' => 'PDF generated and saved successfully',
            'file_path' => Storage::url($filePath) // Adjusted this line
        ], 200);
    }
}
