<div class=" col-lg-12" wire:poll>
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

                  $data .= "<script>
            Dropzone.autoDiscover = false;

        var dropzoneElementId = '#dropzone-" . str_replace('.', '-', $conf['name']) . "';
        var dropzoneElement = document.querySelector(dropzoneElementId);
        
        if (dropzoneElement && !dropzoneElement.dropzone) {

            console.log(dropzoneElementId);
                    const dropzoneId = dropzoneElement.id;
        const key = dropzoneId.replace('dropzone-','');
        const uploadUrl = dropzoneElement.getAttribute('data-upload-url');
        const form_id = dropzoneElement.getAttribute('data-form_id');

            var dropzone = new Dropzone(dropzoneElement, {
                url: '{$uploadUrl}',
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{$csrfToken}'
                },
                maxFilesize: 50000,
                paramName: 'file',
                sending: function(file, xhr, formData) {
                    console.log(file);
                    formData.append('folder', 'dossiers');
                    formData.append('template', '{$conf['name']}');
                    formData.append('random_name', 'true');
                },
                init: function() {
                    this.on('success', function(file, response) {
                        console.log('Successfully uploaded:', response);
                        // initializeDeleteButtons();
                    });
                    this.on('error', function(file, response) {
                        console.log('Upload error:', response);
                    });
                }
            });
        }
    </script>";
            echo $data;
        @endphp
    @endif
</div>
