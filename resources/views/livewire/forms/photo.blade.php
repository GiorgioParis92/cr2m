<div class=" col-lg-12 col-sm-12">
    @if ($check_condition)
        <label>{{ $conf['title'] ?? '' }}</label>
        @php

            $csrfToken = csrf_token();

            $data = '';

            $uploadUrl = route('upload_file', [
                'form_id' => $form_id,
                'folder' => 'dossiers',
                'clientId' => $dossier->folder,
                'template' => $conf['name'],
                'random_name' => 'true',
                'config' => $conf,
            ]);
        @endphp

        <div class="row">
            <div class="col-lg-3">
                <div class="dropzone photo_button bg-secondary"
                    id="dropzone-{{ preg_replace('/[^A-Za-z0-9\-]/', '_', $conf['name']) }}"
                    data-upload-url="{{ $uploadUrl }}" data-csrf-token="{{ $csrfToken }}"
                    data-dossier-id="{{ $dossier_id }}" data-folder="{{ $dossier->folder }}"
                    data-form-id="{{ $form_id }}" data-tag="{{ $conf['name'] }}" data-title="{{ $conf['title'] }}">
                    {{ csrf_field() }}
                    <div style="color:white" class="dz-message">
                        <i class="fas fa-camera"></i> {{ $conf['title'] }}
                    </div>
                </div>
                <!-- Existing code to display uploaded files goes here -->
            </div>
        </div>
        @php

            // $data .= "<div class='row'>";
            // $data .= "<div class='col-lg-3'>";

            // $data .=
            //     '<div style="cursor:pointer" data-form_id="' .
            //     $form_id .
            //     '" data-upload-url="' .
            //     $uploadUrl .
            //     '" class="dropzone photo_button bg-secondary" id="dropzone-' .
            //     $conf['name'] .
            //     '">';
            // $data .= csrf_field(); // This will generate the CSRF token input field
            // $data .=
            //     '<div style="color:white" class="dz-message"><i class="fas fa-camera"></i> ' .
            //     $conf['title'] .
            //     '</div>';
            // $data .= '</div>';

            $deleteUrl = route('delete_file');

            if (!is_array($values)) {
                $values = [$values]; // Transform into array if not already an array
            }
           
            if (is_array($values)) {
                $data='';
                foreach ($values as $val) {
                    if ($val) {
                     
                        $extension = explode('.', $val);

                        $val_thumbnail = str_replace('.', '_thumbnail.', $val);
                        $filePath_thumbnail = storage_path('app/public/' . $val_thumbnail);

                        if (file_exists($filePath_thumbnail)) {
                            // $val = $val_thumbnail;
                        }

                        $filePath = storage_path('app/public/' . $val); // File system path

                        if (count($extension) > 2) {
                            $first = explode('/', $extension[0]);
                            $tag = $first[2];
                            $index = $extension[2];
                        }
                        if (end($extension) != 'pdf') {
                            $data.='<div style="display: inline-block;max-width:20%;">';
                                $data .='<i 
                                data-dossier_id="' . $dossier_id .
                                '" data-tag="' .
                                ($tag ?? $conf['name']) .
                                '" data-index="' .
                                ($index ?? '') .
                                '" data-val="' .
                                $val .
                                '" data-img-src="' .
                                asset('storage/' . $val) .
                                '" class="delete_photo btn btn-danger fa fa-trash bg-danger"></i>';
                            $data.='<img  data-toggle="modal" data-target="imageModal" data-dossier_id="' .$dossier_id .
                                '" data-tag="' .
                                ($tag ?? $conf['name']) .
                                '" data-index="' .
                                ($index ?? '') .
                                '" data-val="' .
                                $val .
                                '" data-img-src="' .
                                asset('storage/' . $val) .
                                '"';
                            $data.=' src="'.asset('storage/' . $val).'" style="        height: 30% !important;
    width: 30% !important;
    display: inline-block;" class="avatar me-2 imageModal cursor-pointer" alt="avatar image">';

                                $data.='</div>';
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

            // $data .= '</div>';
            // $data .= '</div>';

            echo $data;
        @endphp

        {{-- <div class="row">
            <div class="col-lg-3">
                <div class="dropzone photo_button bg-secondary"
                    id="dropzone-{{ preg_replace('/[^A-Za-z0-9\-]/', '_', $conf['name']) }}"
                    data-upload-url="{{ $uploadUrl }}" data-csrf-token="{{ $csrfToken }}"
                    data-dossier-id="{{ $dossier_id }}" data-folder="{{ $dossier->folder }}"
                    data-form-id="{{ $form_id }}" data-tag="{{ $conf['name'] }}" data-title="{{ $conf['title'] }}">
                    {{ csrf_field() }}
                    <div style="color:white" class="dz-message">
                        <i class="fas fa-camera"></i> {{ $conf['title'] }}
                    </div>
                </div>
                <!-- Existing code to display uploaded files goes here -->
            </div>
        </div> --}}

        <script>
            $(document).ready(function() {
                initializeDeleteButtons();
                initAllDropzones();
            });
            document.addEventListener('livewire:update', function() {
                initializeDeleteButtons();
                initAllDropzones();

            });
            document.addEventListener('livewire:load', function() {
                console.log('livewire loaded')
                initializeDeleteButtons();
                initAllDropzones();

            });

            function initAllDropzones() {

                Dropzone.autoDiscover = false;
                var dropzoneElements = document.querySelectorAll('.dropzone');

                dropzoneElements.forEach(function(dropzoneElement) {
                    if (dropzoneElement.dropzone) {
                        // Already initialized
                        return;
                    }

                    var uploadUrl = dropzoneElement.getAttribute('data-upload-url');
                    var dossierId = dropzoneElement.getAttribute('data-dossier-id');
                    var folder = dropzoneElement.getAttribute('data-folder');
                    var formId = dropzoneElement.getAttribute('data-form-id');
                    var tag = dropzoneElement.getAttribute('data-tag');
                    var title = dropzoneElement.getAttribute('data-title');

                    var dz = new Dropzone(dropzoneElement, {
                        url: uploadUrl,
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        maxFilesize: 50000,
                        paramName: 'file',
                        sending: function(file, xhr, formData) {
                            formData.append('dossier_id', dossierId);
                            formData.append('clientId', folder);
                            formData.append('folder', 'dossiers');
                            formData.append('template', tag);
                            formData.append('config', formId);
                            formData.append('random_name', 'true');
                            formData.append('form_id', formId);
                        },
                        init: function() {
                            this.on('success', function(file, response) {
                                console.log('Successfully uploaded:', response);

                                var url = "{{ asset('storage') }}/" + response;
                                var isPdf = url.toLowerCase().endsWith('.pdf');

                                var newBlockHtml = '';
                                if (!isPdf) {
                                    newBlockHtml =
                                        '<div style="display:inline-block">' +
                                        '<i data-dossier_id="' + dossierId + '" ' +
                                        'data-tag="' + tag + '" ' +
                                        'data-val="' + response + '" ' +
                                        'data-img-src="' + url + '" ' +
                                        'class="delete_photo btn btn-danger fa fa-trash bg-danger"></i>' +
                                        '<button type="button" class="btn btn-success btn-view imageModal" ' +
                                        'data-toggle="modal" data-target="imageModal" ' +
                                        'data-img-src="' + url + '" ' +
                                        'data-val="' + response + '" ' +
                                        'data-name="' + title + '">' +
                                        '<img src="' + url + '">' +
                                        '<i style="display:block" class="fas fa-eye"></i>' + title +
                                        '</button>' +
                                        '</div>';
                                } else {
                                    newBlockHtml =
                                        '<div class="btn btn-success btn-view pdfModal" ' +
                                        'data-toggle="modal" ' +
                                        'data-img-src="' + url + '" ' +
                                        'data-val="' + response + '" ' +
                                        'data-name="' + title + '">' +
                                        '<i class="fas fa-eye"></i>' + title +
                                        '</div>';
                                }

                                $(dropzoneElement).closest('.row').find('.col-lg-3').append(
                                    newBlockHtml);
                                initializeDeleteButtons();
                            });

                            this.on('error', function(file, response) {
                                console.log('Upload error:', response);
                            });
                        }
                    });
                });
            }

            function initializeDeleteButtons() {
                $('.delete_photo').off('click').on('click', function() {
                    var _this = $(this);
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
                            tag: _this.data('tag')
                        },
                        success: function(response) {
                            console.log('Successfully deleted:', response);
                            // Remove the parent container
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

            if (window.Livewire) {
                initAllDropzones();
            } else {
                document.addEventListener('livewire:load', initAllDropzones);
            }
        </script>
    @endif



<style>
    .delete_photo {
    position: absolute;
    top: -22px;
}
</style>

</div>
