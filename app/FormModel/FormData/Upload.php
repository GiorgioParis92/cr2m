<?php
namespace App\FormModel\FormData;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Illuminate\Support\Facades\Storage;

class Upload extends AbstractFormData
{
    public function render(bool $is_error)
    {

        $data = '';




        $data = '<tr>';


        $data .= '<td>';
        $data .= '<div class="d-flex px-2 py-1 align-items-center">';
        $data .= $this->config->title;

        $data .= '</div>';
        $data .= '</td>';


        $data .= '<td>';

        $data .= '</td>';

        $data .= '<td class="w-30" ><div wire:poll.visible>';

        $extension = explode('.', $this->value);

      

            $filePath = storage_path('app/public/' . $this->value);  // File system path

        // $data.=$filePath;
            $data.=$this->value;
            if (file_exists($filePath)) {
                if (end($extension) != 'pdf') {
                    $data .= '<button type="button" class="btn btn-success btn-view imageModal"
            data-toggle="modal" data-target="imageModal"
            data-img-src="' . asset('storage/' . $this->value) . '?time=' . strtotime('now') . '"
            data-name="' . $this->config->title . '">
            <i class="fas fa-eye"></i> Visualiser
        </button> ';
                } else {
                    $data .= '<div class="btn btn-success btn-view pdfModal"
            data-toggle="modal" 
             
            data-img-src="' . asset('storage/' . $this->value) . '?time=' . strtotime('now') . '"
            data-name="' . $this->config->title . '">
            <i class="fas fa-eye"></i> Visualiser</div>';
                }
            }

        




        $data .= '</div></td>';


        $data .= '<td class="align-middle text-sm">';
        $data .= '<form action="' . route("upload_file", ["form_id" => $this->form_id, "folder" => "dossiers", "clientId" => $this->dossier_id, "template" => $this->name]) . '" class="dropzone dropzone_button" id="dropzone-' . $this->name . '">';
        $data .= csrf_field(); // This will generate the CSRF token input field
        $data .= '<div class="dz-message"><i class="fas fa-arrow-up"></i> Upload';
        $data .= '</div>';
        $data .= '</form>';

        $data .= '</td>';
        $data .= '</tr>';

        return $data;
    }


    public function check_value()
    {

        return Storage::disk('public')->exists($this->value);
    }

}