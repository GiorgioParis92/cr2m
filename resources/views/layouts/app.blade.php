<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('frontend/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('frontend/assets/img/favicon.ico') }}">
    <title>
        GENIUS MARKET
    </title>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('frontend/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{ asset('frontend/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/assets/css/custom_css.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('frontend/assets/css/soft-ui-dashboard.css?v=1.0.7') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/dropzone.min.css" rel="stylesheet">

    <link id="pagestyle" href="{{ asset('frontend/assets/css/jquery-radiocharm.css') }}" rel="stylesheet" />
    <script src="{{ asset('frontend/assets/js/jquery-radiocharm.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">


    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">


    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/bootstrap/main.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzcaFvxwi1XLyRHmPRnlKO4zcJXPOT5gM&callback=initMap"></script>

    @livewireStyles
</head>

<body class="g-sidenav-show bg-gray-100">
    @include('frontend.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('frontend.navbar')

        @yield('content')

        @include('frontend.footer')
    </main>
    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <iframe id="pdfFrame" frameborder="0"></iframe>
        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="" id="imageInModal" class="img-fluid" alt="Image">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="calendar_modal" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Calendrier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="eventTitle"></span></p>
                    <p><strong>Description:</strong> <span id="eventDescription"></span></p>
                    <p><strong>Start:</strong> <span id="eventStart"></span></p>
                    <p><strong>End:</strong> <span id="eventEnd"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="rdv_modal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="    min-width: 50%;">
            <div class="modal-content" style="    height: auto;    min-height: auto !important;">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Rdv</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <input class="form-control" type="hidden" id="rdv_id" name="rdv_id">
                            <input class="form-control" type="hidden" id="rdv_dossier_id" name="dossier_id">
                            <input class="form-control" type="hidden" id="rdv_client_id" name="client_id">
                            <input class="form-control" type="hidden" id="rdv_telephone" name="telephone">
                            <input class="form-control" type="hidden" id="rdv_telephone_2" name="telephone_2">
                            <input class="form-control" type="hidden" id="rdv_email" name="email">
                            <input class="form-control" type="hidden" id="rdv_nom" name="nom">
                            <input class="form-control" type="hidden" id="rdv_prenom" name="prenom">
                            <input class="form-control" type="hidden" id="rdv_adresse" name="adresse">
                            <input class="form-control" type="hidden" id="rdv_cp" name="cp">
                            <input class="form-control" type="hidden" id="rdv_ville" name="ville">
                            <input class="form-control" type="hidden" id="rdv_lat" name="lat">
                            <input class="form-control" type="hidden" id="rdv_lng" name="lng">
                            <input class="form-control" type="hidden" id="rdv_type_rdv" name="type_rdv">
                            <label>Date</label>
                            <input class="form-control datepicker" type="text" id="rdv_french_date"
                                name="date_rdv">
                        </div>
                        <div class="form-group">
                            <label>Heure</label>
                            <div class="row">
                                <div class="col-6">
                                    <select class="form-control" id="rdv_hour" name="hour">
                                        @for ($hour = 0; $hour < 24; $hour++)
                                            <option value="{{ sprintf('%02d', $hour) }}">{{ sprintf('%02d', $hour) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-control" id="rdv_minute" name="minute">

                                        @for ($minute = 0; $minute < 60; $minute += 5)
                                            <option value="{{ sprintf('%02d', $minute) }}">
                                                {{ sprintf('%02d', $minute) }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            @if(isset($auditeurs))
                            <div class="form-group">
                                <label>Auditeur</label>
                                <select class="form-control" id="rdv_user_id" name="user_id">
                                    <option value="">Choisir un auditeur</option>
                                    @foreach ($auditeurs as $auditeur)
                                        <option value="{{ $auditeur->id }}">{{ $auditeur->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                            @endif
                            <div class="form-group">


                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <div id="save_rdv" class="btn btn-primary">Enregistrer</div>

                    <button type="button" class="btn btn-secondary close_button" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    @include('frontend.fixed-plugin')

    <!-- Core JS Files -->
    <script src="{{ asset('frontend/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/dropzone.min.js"></script>

    @yield('scripts')
    <script>
        $(document).ready(function() {


            $('.close').on('click', function() {

                $('.modal').modal('hide');
            });
            $('.close_button').on('click', function() {

                $('.modal').modal('hide');
            });
            $('select').each(function() {
                if ($(this).closest('.modal').length === 0) {
                    $(this).select2();
                }
            });
            $('.datepicker').datepicker({
                language: 'fr',
                dateFormat: 'dd/mm/yy', // See format options on parseDate

            });
            $('.datatable').DataTable()

    
            $('#save_rdv').click(function() {
                // Collect form data
                var formData = {
                    rdv_id: $('#rdv_id').val(),
                    date_rdv: $('#rdv_french_date').val(),
                    hour: $('#rdv_hour').val(),
                    minute: $('#rdv_minute').val(),
                    user_id: $('#rdv_user_id').val(),
                    type_rdv: $('#rdv_type_rdv').val(),
                    nom: $('#rdv_nom').val(),
                    prenom: $('#rdv_prenom').val(),
                    adresse: $('#rdv_adresse').val(),
                    cp: $('#rdv_cp').val(),
                    ville: $('#rdv_ville').val(),
                    telephone: $('#rdv_telephone').val(),
                    telephone_2: $('#rdv_telephone_2').val(),
                    email: $('#rdv_email').val(),
                    dossier_id: $('#rdv_dossier_id').val(),
                    client_id: $('#rdv_client_id').val(),
                    lat: $('#rdv_lat').val(),
                    lng: $('#rdv_lng').val(),
                    // Include additional fields if present
                };

                // Perform AJAX call
                updateRdv(formData);
            });

            function updateRdv(data) {
                $.ajax({
                    url: '/api/rdvs/update', // Adjust the URL to match your endpoint
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            // Close modal and refresh parent page or redirect
                            $('#rdv_modal').modal('hide');
                            location.reload(); // Or redirect to previous page
                        } else {
                            // Handle error
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        // Handle AJAX error
                        console.log(xhr)
                        alert('An error occurred: ' + xhr.message);
                    }
                });
            }
            $(document).on('click', '.fillPDF', function(event) {
            var form_id = $(this).data('form_id');
            var dossier_id = $(this).data('dossier_id');
            var name = $(this).data('name');
            alert('ok')
            $.ajax({
                url: '/api/fill-pdf',
                type: 'GET',
                data: {
                    dossier_id: dossier_id,
                    form_id: form_id,
                    name: name
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.file_path) {
                        $('#pdfFrame').attr('src', response.file_path);
                        $('#pdfModal').css('display', 'block');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error generating PDF:', error);
                }
            });
        });
            $('.generatePdfButton').click(function() {
                var template = $(this).data('template'); // Get the template from data attribute
                var dossier_id = $(this).data('dossier_id'); // Get the dossier ID from data attribute

                $.ajax({
                    url: '/api/generate-pdf', // Adjust this URL to your actual API endpoint
                    type: 'GET',
                    data: {
                        dossier_id: dossier_id,
                        template: template
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Include CSRF token if using Laravel's CSRF protection
                    },
                    success: function(response) {
                        if (response.file_path) {
                            // Display the PDF in an iframe if a file path is returned
                            $('#pdfFrame').attr('src', response.file_path);
                            $('#pdfModal').css('display', 'block');
                        } else {
                            // Handle the response where the PDF content is returned directly
                            var blob = new Blob([response], {
                                type: 'application/pdf'
                            });
                            var url = URL.createObjectURL(blob);
                            var link = document.createElement('a');
                            link.href = url;
                            link.download = 'document.pdf';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error generating PDF:', error);
                    }
                });
            });
            $('.close').on('click', function() {
                $('#pdfModal').css('display', 'none');
                $('#imageModal').modal('hide');
                $('#pdfModal').modal('hide');
            });

            // Close the modal when the user clicks anywhere outside of the modal
            $(window).on('click', function(event) {
                if (event.target == $('#pdfModal')[0]) {
                    $('#pdfModal').css('display', 'none');
                    $('#imageModal').modal('hide');
                    $('#pdfModal').modal('hide');
                }
            });
        });
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('frontend/assets/js/soft-ui-dashboard.min.js?v=1.0.7') }}"></script>
    @livewireScripts

</body>

</html>
