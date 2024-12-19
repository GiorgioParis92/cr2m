<div class=" col-lg-12">
    @if ($check_condition)
        <label>{{ $conf['title'] ?? '' }}</label>
        @php
     
            $data = '';

            $uploadUrl = route('upload_file', [
                'form_id' => $form_id,
                'folder' => 'dossiers',
                'clientId' => $dossier->folder,
                'template' => $conf['name'],
                'random_name' => 'true',
                'config' => $conf,
            ]);

            $csrfToken = csrf_token();

            $data .= "<div class='row'>";
            $data .= "<div class='col-lg-3'>";

            $data .=
                '<div style="cursor:pointer" data-form_id="' .
                $form_id .
                '" data-upload-url="' .
                $uploadUrl .
                '" class="dropzone photo_button bg-secondary" id="dropzone-' .
                $conf['name'] .
                '">';
            $data .= csrf_field(); // This will generate the CSRF token input field
            $data .=
                '<div style="color:white" class="dz-message"><i class="fas fa-camera"></i> ' .
                $conf['title'] .
                '</div>';
            $data .= '</div>';

            $deleteUrl = route('delete_file');

            $data .= "\r\n" . '</script>' . "\r\n";
            if (!is_array($values)) {
                $values = [$values]; // Transform into array if not already an array
            }
            if (is_array($values)) {
                foreach ($values as $val) {
                    if ($val) {

                        $extension = explode('.', $val);

                        $val_thumbnail = str_replace('.', '_thumbnail.', $val);
                        $filePath_thumbnail = storage_path('app/public/' . $val_thumbnail);

                        if (file_exists($filePath_thumbnail)) {
                            $val = $val_thumbnail;
                        }

                        $filePath = storage_path('app/public/' . $val); // File system path
                 
                        if (count($extension) > 2) {
                            $first = explode('/', $extension[0]);
                            $tag = $first[2];
                            $index = $extension[2];
                        }

                        if (file_exists($filePath)) {
                         
                            if (end($extension) != 'pdf') {
                            
                                $data .=
                                    '<div style="display:inline-block">
                    <i data-dossier_id="$this->dossier_id" data-tag="' .
                                    ($tag ?? $conf['name']) .
                                    '" data-index="' .
                                    ($index ?? '') .
                                    '" data-val="' .
                                    $val .
                                    '" data-img-src="' .
                                    asset('storage/' . $val) .
                                    '" class="delete_photo btn btn-danger fa fa-trash bg-danger"></i>

                    <button  type="button" class="btn btn-success btn-view imageModal"
                        data-toggle="modal" data-target="imageModal"
                        data-img-src="' .
                                    asset('storage/' . $val) .
                                    '"
                        data-val="' .
                                    $val .
                                    '"
                        data-name="' .
                                    $conf['title'] .
                                    '">';
                                $data .= '<img src="' . asset('storage/' . $val) . '">';
                                $data .=
                                    '<i style="display:block" class="fas fa-eye"></i>' .
                                    $conf['title'] .
                                    '
                    </button></div>';
                            } else {
                                
                                $data .=
                                    '<div class="btn btn-success btn-view pdfModal"
                        data-toggle="modal" 
                        data-img-src="' .
                                    asset('storage/' . $val) .
                                    '"
                        data-val="' .
                                    $val .
                                    '"
                        data-name="' .
                                    $conf['title'] .
                                    '">';
                                $data .= '<i class="fas fa-eye"></i>' . $conf['title'] . '</div>';
                            }
                        }
                    }
                }
            }

            $data .= '</div>';
            $data .= '</div>';
            echo $data;
        @endphp
    @endif
</div>
