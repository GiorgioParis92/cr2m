<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('frontend/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('frontend/assets/img/favicon.ico') }}">
    <title>
        GENIUS MARKET
    </title>
    <!-- Fonts and icons -->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link id="pagestyle" href="{{ asset('frontend/assets/css/jquery-radiocharm.css') }}" rel="stylesheet" />
    <script src="{{ asset('frontend/assets/js/jquery-radiocharm.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('frontend/assets/js/index.global.js') }}"></script>
    <script src='{{ asset('frontend/assets/js/fullcalendar/core/locales/es.global.js') }}'></script>
    <link href="{{ asset('frontend/assets/css/calendar.css') }}" rel="stylesheet" />

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <script src="https://cdn.tiny.cloud/1/3wjbcf76icbvo12wykh2qn03z2aqpej8z6gkkbhv1ay5ie8b/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">


    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/bootstrap/main.css" rel="stylesheet">

    <script src="{{ asset('frontend/assets/js/fullcalendar/packages/core/main.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/fullcalendar/packages/daygrid/main.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/fullcalendar/packages/interaction/main.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('frontend/assets/js/fullcalendar/packages/core/main.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/js/fullcalendar/packages/daygrid/main.css') }}">


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
        <div class="modal-dialog"     style="min-width: 90%;">
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
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        $('input:radio').radiocharm({

        });

        $(document).ready(function() {


            $('.fillPDF').click(function() {
                var form_id = $(this).data('form_id'); // Get the template from data attribute
                var dossier_id = $(this).data('dossier_id'); // Get the dossier ID from data attribute
                var name = $(this).data('name'); // Get the dossier ID from data attribute

                $.ajax({
                    url: '/api/fill-pdf', // Adjust this URL to your actual API endpoint
                    type: 'GET',
                    data: {
                        dossier_id: dossier_id,
                        form_id: form_id,
                        name: name
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

        $(document).ready(function() {
            $('select').select2();
            $('.datepicker').datepicker();
            $('.imageModal').on('click', function(event) {

                var imgSrc = $(this).data('img-src');


                $('#imageModal').css('display', 'block');
                $('#imageModal').modal('show');
                $('#imageInModal').attr('src', imgSrc);
                $('#imageModalLabel').html($(this).data('name'))
            });



            $('.pdfModal').on('click', function(event) {

                var imgSrc = $(this).data('img-src');

                console.log(imgSrc)
                $('#pdfModal').css('display', 'block');
                //   $('#pdfModal').modal('show');
                $('#pdfFrame').attr('src', imgSrc);

            });

        });
        $(document).ready(function() {

            const $etapeTabs = $('#etapeTabs .nav-link');
            const $stepContents = $('.tab-pane.step-content');
            const $tabItems = $('#etapeTabs .nav-item');

            const currentEtapeId = {{ $dossier->etape_number ?? 0 }};
            const etapes = @json($etapes ?? '');
            const currentEtapeIndex = etapes.findIndex(etape => etape.etape_number === currentEtapeId);

            // Initialize tab states
            $tabItems.each(function(index) {
                if (index < currentEtapeIndex) {
                    $(this).addClass('active');
                } else if (index === currentEtapeIndex) {
                    $(this).addClass('current active');
                } else {
                    $(this).addClass('disabled');
                }
            });


            // Show the current step content
            $stepContents.each(function(index) {
                if (index === currentEtapeIndex) {
                    $(this).show();

                } else {
                    $(this).hide();
                }
            });

            $etapeTabs.on('click', function(event) {

                event.preventDefault();
                $('.fc-timeGridWeek-button').click()
               
                const $parent = $(this).parent();

                if (!$(this).hasClass('active') && !$(this).hasClass('current')) return;

                const index = parseInt($(this).data('index'));

                // Remove active and current class from all tab items
                $tabItems.removeClass('active current');

                // Hide all contents
                $stepContents.hide();

                // Add active class to the current <li> and all previous <li> elements
                for (let i = 0; i <= index; i++) {
                    $tabItems.eq(i).addClass('active');
                }

                // Add current class to the clicked <li>
                $parent.addClass('current');

                // Show the content corresponding to the clicked tab
                $stepContents.eq(index - 1).show();
                $('.fc-timeGridWeek-button').click()
            });

            $('input.choice_checked').trigger('click');

            $('#form_config_user_id').change(function() {


            });

            
        });
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('frontend/assets/js/soft-ui-dashboard.min.js?v=1.0.7') }}"></script>
</body>

</html>
