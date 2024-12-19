<div class=" col-lg-12 col-sm-12" >
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
                    <i data-dossier_id="'.$dossier_id.'" data-tag="' .
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

<script>
        initializeDeleteButtons();
        var confTitle = @json($conf['title']);
    var confName = @json($conf['name']);
    var dossierId = @json($dossier_id);
    var confId = @json($conf['id']);
    function initDropzone() {
        // Put your Dropzone initialization code here
        Dropzone.autoDiscover = false;
        var dropzoneElementId = '#dropzone-{{ preg_replace('/[^A-Za-z0-9\-]/', '_', $conf["name"]) }}';
        var dropzoneElement = document.querySelector(dropzoneElementId);

        if (dropzoneElement && !dropzoneElement.dropzone) {
            var dropzone = new Dropzone(dropzoneElement, {
                url: '{{ $uploadUrl }}',
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{{ $csrfToken }}'
                },
                maxFilesize: 50000,
                paramName: 'file',
                sending: function(file, xhr, formData) {
                    formData.append('dossier_id', '{{ $dossier_id }}');
                    formData.append('clientId', '{{ $dossier->folder }}');
                    formData.append('folder', 'dossiers');
                    formData.append('template', '{{ $conf["name"] }}');
                    formData.append('config', '{{ $conf["id"] }}');
                    formData.append('random_name', 'true');
                    formData.append('form_id', '{{$form_id}}');
                },
                init: function() {
                    this.on('success', function(file, response) {
    console.log('Successfully uploaded:', response);
    // 'response' now only contains 'url'

    // Since we have the other variables from Blade as JS variables:
    var url = "{{ asset('storage') }}/" + response;
    var title = confTitle;  // previously defined from Blade variables
    var name = confName;
    var id = dossierId;

    // Determine if it's PDF by checking the URL extension
    var isPdf = url.toLowerCase().endsWith('.pdf');

    var newBlockHtml = '';

    if (!isPdf) {
    // Use `response` for data-val (the file's relative path)
    newBlockHtml =
        '<div style="display:inline-block">' +
            '<i data-dossier_id="' + id + '" ' +
            'data-tag="' + name + '" ' +
            'data-index="" ' +
            'data-val="' + response + '" ' + // Only the relative path here
            'data-img-src="' + url + '" ' + // Keep the full URL for preview
            'class="delete_photo btn btn-danger fa fa-trash bg-danger"></i>' +

            '<button type="button" ' +
            'class="btn btn-success btn-view imageModal" ' +
            'data-toggle="modal" data-target="imageModal" ' +
            'data-img-src="' + url + '" ' +
            'data-val="' + response + '" ' + // Also ensure consistency here
            'data-name="' + title + '">' +
                '<img src="' + url + '">' +
                '<i style="display:block" class="fas fa-eye"></i>' + title +
            '</button>' +
        '</div>';
} else {
    // Similarly for PDF:
    newBlockHtml =
        '<div class="btn btn-success btn-view pdfModal" ' +
        'data-toggle="modal" ' +
        'data-img-src="' + url + '" ' +
        'data-val="' + response + '" ' + // Relative path only
        'data-name="' + title + '">' +
            '<i class="fas fa-eye"></i>' + title +
        '</div>';
}


    $(dropzoneElement).closest('.row').find('.col-lg-3').append(newBlockHtml);
    initializeDeleteButtons();
});


                    this.on('error', function(file, response) {
                        console.log('Upload error:', response);
                    });
                }
            });
        }
    }

    // Check if Livewire is already available
    if (window.Livewire) {
        // Livewire is already loaded, so we can initialize Dropzone now
        initDropzone();
    } else {
        // If not loaded yet, listen for the event
        document.addEventListener('livewire:load', initDropzone);
    }

    function initializeDeleteButtons() {
    $('.delete_photo').off('click').on('click', function() {
        var _this = $(this); // Store a reference to the clicked element
        var link = _this.data('val');

        $.ajax({
            url: '/delete_file',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                link: link,
                dossier_id: _this.data('dossier_id'),
                tag: _this.data('tag'),
            },
            success: function(response) {
                console.log('Successfully deleted:', response);
                // Remove the parent block containing the image and delete button
                _this.closest('div[style="display:inline-block"]').remove();
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(', ');
                }
                console.log(errorMessage);
            }
        });
    });
}

</script>


    @endif

 
    
        

</div>
{{-- <script>
    function initDropzone() {
        // Your Dropzone initialization code here
        Dropzone.autoDiscover = false;
        var dropzoneElementId = '#dropzone-{{ preg_replace('/[^A-Za-z0-9\-]/', '_', $conf["name"]) }}';
        var dropzoneElement = document.querySelector(dropzoneElementId);
        
        if (dropzoneElement && !dropzoneElement.dropzone) {
            console.log(dropzoneElementId);
            const uploadUrl = dropzoneElement.getAttribute('data-upload-url');
            
            var dropzone = new Dropzone(dropzoneElement, {
                url: '{{ $uploadUrl }}',
                method: 'post',
                headers: { 'X-CSRF-TOKEN': '{{ $csrfToken }}' },
                maxFilesize: 50000,
                paramName: 'file',
                sending: function(file, xhr, formData) {
                    formData.append('dossier_id', '{{ $dossier_id }}');
                    formData.append('clientId', '{{ $dossier->folder }}');
                    formData.append('folder', 'dossiers');
                    formData.append('template', '{{ $conf["name"] }}');
                    formData.append('config', '{{ $conf["id"] }}');
                    formData.append('random_name', 'true');
                    formData.append('form_id', '{{$form_id}}');
                },
                init: function() {
                    this.on('success', function(file, response) {
                        console.log('Successfully uploaded:', response);
                        Livewire.emit('fileUploaded', response);
                    });
                    this.on('error', function(file, response) {
                        console.log('Upload error:', response);
                    });
                }
            });
        }
    }

    // If Livewire is already available, just run initDropzone
    if (window.Livewire) {
        initDropzone();
    } else {
        // If not, wait for the event
        document.addEventListener('livewire:load', initDropzone);
    }

</script>
 --}}

