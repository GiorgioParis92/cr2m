<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Add this line
use App\Http\Controllers\Api\OcrAnalyze; // Import the OcrAnalyze controller
use App\Models\Dossier;
use App\FormModel\FormData\Photo;
use App\FormModel\FormData\Table;
use Livewire\Livewire; // Import the Livewire facade
use PDF; // Assuming you have barryvdh/laravel-dompdf installed
use Image; // Assuming you have intervention/image installed
use setasign\Fpdi\Fpdi;
use FPDF;

class FileUploadService
{
    /**
     * Store an uploaded image in a specific folder structure.
     *
     * @param Request $request
     * @param string $folder
     * @param int $clientId
     * @param string $inputName
     * @return string|false
     */
    public function storeImage(Request $request, string $folder = null, int $clientId = null, string $inputName = 'file', bool $random_name = false)
    {
        if ($request->folder == 'dossiers') {
            if (isset($request->analyze)) {
                // $ocrResponse = $this->callOcrAnalyzeDirectly($request);
            } else {
                // $ocrResponse = false;
            }

            // 

            // if (!$ocrResponse) {
            //     return false; // Handle OCR failure as needed
            // } else {
            //     $result = $ocrResponse['result']['data']['analyze_result'];
            //     $array_result = $result;
            //     foreach ($array_result as $key => $value) {
            //         $meta_value='';

            //         // Check if the value is an array and convert to JSON string if true
            //         if(isset($value['value'])) {

            //             if (is_array($value['value'])) {
            //                 $meta_value = json_encode($value['value']);
            //             } else {
            //                 $meta_value = $value['value'];
            //             }
            //         }


            //         \DB::table('dossiers_data')->updateOrInsert(
            //             [
            //                 'dossier_id' => $request->clientId,
            //                 'meta_key' => $key
            //             ],
            //             [
            //                 'meta_value' => $meta_value,
            //                 'created_at' => now(),
            //                 'updated_at' => now()
            //             ]
            //         );
            //     }

            // }
        }

        if (isset($request->folder)) {
            $folder = $request->folder;

        }
        if (isset($request->clientId)) {
            $clientId = $request->clientId;

            if (isset($request->folder) && $request->folder == 'dossiers') {
                if (is_numeric($request->clientId)) {
                    $dossier = Dossier::where('id', $request->clientId)->first();
                } else {
                    $dossier = Dossier::where('folder', $request->clientId)->first();
                }

                $clientId = $dossier->folder;
            }


        }



        if (isset($request->random_name)) {

            $random_name = true;
        }

        if (isset($request->form_id)) {
            $form_id = $request->form_id;
        }


        $file = $request->file('file');

        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'pdf', 'heic', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());



        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        $directory = "{$folder}/{$clientId}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Directory path where files will be stored
        $directoryPath = storage_path('app/public/' . $directory);

        // Find all files with the same base name in the directory
        foreach (glob($directoryPath . '/' . $originalFileName . '.*') as $existingFile) {

            unlink($existingFile);
        }

        // Prepare the new file name with the template if provided
        if ($request->has('template')) {
            $extension = $file->getClientOriginalExtension();
            if ($random_name) {
                $fileName = $request->input('template') . time() . '.' . $extension;

            } else {
                $fileName = $request->input('template') . '.' . $extension;

            }
        } else {
            $fileName = $file->getClientOriginalName();
        }

        // Save the new file
        $filePath = $file->storeAs($directory, $fileName, 'public');

        DB::enableQueryLog();
        $index = '';

        $template = $request->input('template');





        if ($random_name == true) {

            $index = '';
            $explode = (explode('.', $request->input('template')));
            if (is_array($explode) && count($explode) > 1) {
                $array = explode('.', $request->input('template'));
                $template = $array[0];
                $index = $array[2];
                $field = $array[3];

            } else {

                $template = $request->input('template');
            }

            $value = DB::table('forms_data')
                ->where('meta_key', $template)
                ->where("form_id", $form_id)
                ->where("dossier_id", $dossier->id)
                ->first();

            if ($value) {
                $json_value = json_decode($value->meta_value);
                if ($json_value) {
                    array_push($json_value, $filePath);
                } else {
                    $json_value = [];
                    array_push($json_value, $filePath);
                }
                $updatedJsonString = json_encode($json_value);
                if ($index != '') {
                    $json_array = (json_decode($value->meta_value, true));

                    $json_array[$index][$field]['value'][] = $filePath;

                    $updatedJsonString = json_encode($json_array);


                }



                $update = DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => '' . $dossier->id . '',
                        'form_id' => '' . $form_id . '',
                        'meta_key' => '' . $template . ''
                    ],
                    [
                        'meta_value' => '' . $updatedJsonString . '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );


            } else {
                $json_value = [];
                array_push($json_value, $filePath);
                $update = DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => '' . $dossier->id . '',
                        'form_id' => '' . $form_id . '',
                        'meta_key' => '' . $request->input('template') . ''
                    ],
                    [
                        'meta_value' => '' . json_encode($json_value) . '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        } else {


            $update = DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $form_id . '',
                    'meta_key' => '' . $template . ''
                ],
                [
                    'meta_value' => '' . $filePath . '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

        }



        if (isset($request->upload_image) && $file->isValid() && in_array(strtolower($extension), ['jpeg', 'jpg', 'png', 'gif', 'bmp'])) {

            $image = Image::make($file);

            $exif = @exif_read_data($file->getPathname());
            if ($exif && isset($exif['Orientation'])) {


                $update = DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => '' . $dossier->id . '',
                        'form_id' => '' . $form_id . '',
                        'meta_key' => 'ORIENTATION'
                    ],
                    [
                        'meta_value' => $exif['Orientation'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                switch ($exif['Orientation']) {
                 
                    case 6:
                        $image->rotate(90);
                        break;
                    case 8:
                        $image->rotate(90);
                        break;
                }
            }
        
            // Get the width and height of the image
            $width = $image->width();
            $height = $image->height();
        


            $update = DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $form_id . '',
                    'meta_key' => 'width'
                ],
                [
                    'meta_value' => $width,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            $update = DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $form_id . '',
                    'meta_key' => 'height'
                ],
                [
                    'meta_value' => $height,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Standardize the image orientation and dimensions
            if ($width > $height || $exif['Orientation']==6) {
                // Rotate the image if it's wider than it is tall (landscape)
                $image->rotate(90);
                $update = DB::table('forms_data')->updateOrInsert(
                    [
                        'dossier_id' => '' . $dossier->id . '',
                        'form_id' => '' . $form_id . '',
                        'meta_key' => 'rotate'
                    ],
                    [
                        'meta_value' => 'yes',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            $image = $image->fit(595, 842); // 595x842 pixels corresponds to 210x297mm at 72dpi

            $tempImagePath = storage_path('app/public/' . $directory . '/temp_image.jpg');
            $image->save($tempImagePath);

            // Define the PDF file name and path
            $pdfFileName = $request->input('template') . '.pdf';
            $pdfFilePath = storage_path('app/public/' . $directory . '/' . $pdfFileName);

            if (file_exists($pdfFilePath)) {
                // Append to existing PDF
                $pdf = new FPDI();
                $pageCount = $pdf->setSourceFile($pdfFilePath);

                // Import existing pages
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }

                // Add a new page for the new image
                $pdf->AddPage('P', 'A4');
                $pdf->Image($tempImagePath, 0, 0, 210, 297);
            } else {
                // Create a new PDF
                $pdf = new FPDF();
                $pdf->AddPage('P', 'A4');
                $pdf->Image($tempImagePath, 0, 0, 210, 297);
            }

            // Save the updated or new PDF
            $pdf->Output($pdfFilePath, 'F');

            // Optionally, delete the temporary image file
            unlink($tempImagePath);


            $update = DB::table('forms_data')->updateOrInsert(
                [
                    'dossier_id' => '' . $dossier->id . '',
                    'form_id' => '' . $form_id . '',
                    'meta_key' => '' . $template . ''
                ],
                [
                    'meta_value' => '' . $directory . '/' . $pdfFileName . '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            return $directory . '/' . $pdfFileName;
        }

        if ($index != '') {
            $config = \DB::table('forms_config')
                ->where('form_id', $form_id)
                ->where('name', $template)
                ->first();

            $table = new Table($config, $template, $form_id, $dossier->id);
            $table->save_value();
        }


        return $filePath;



    }
    protected function callOcrAnalyzeDirectly(Request $request)
    {
        $ocrController = new OcrAnalyze();
        $response = $ocrController->index($request);

        if ($response->getStatusCode() === 200) {
            $responseData = json_decode($response->getContent(), true);
            // Add your custom logic here based on the OCR response
            return $responseData;
        }

        return false;
    }
    public function deleteImage(Request $request)
    {


        $value = DB::table('forms_data')
            ->where('dossier_id', $request->dossier_id)
            ->where('meta_key', $request->tag)
            ->first();
        if ($value) {
            $json_value = json_decode($value->meta_value);


            if (is_array($json_value) && in_array($request->link, $json_value)) {
                // Remove the value from the array
                $json_value = array_filter($json_value, function ($item) use ($request) {
                    return $item !== $request->link;
                });

                // Reindex the array
                $json_value = array_values($json_value);
                // Encode back to JSON
                $new_json_value = json_encode($json_value);
                // Update the meta_value in the database
                DB::table('forms_data')
                    ->where('id', $value->id) // Assuming 'id' is the primary key
                    ->update(['meta_value' => $new_json_value]);
            } else {
                DB::table('forms_data')
                    ->where('id', $value->id) // Assuming 'id' is the primary key
                    ->delete();
            }
        }
        unlink(storage_path('app/public/' . $request->link));


    }
}
