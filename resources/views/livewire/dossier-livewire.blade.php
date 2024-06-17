<div>
    <div class="row">

        <div class="col-12">
            <div class="card form-register">
                <div class="card-header pb-0 clearfix">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">
                                <b>{{ $dossier['beneficiaire']['nom'] }}
                                    {{ $dossier['beneficiaire']['prenom'] }}</b><br />
                                {{ strtoupper_extended($dossier['beneficiaire']['adresse'] . ' ' . $dossier['beneficiaire']['cp'] . ' ' . $dossier['beneficiaire']['ville']) }}<br />
                            </h5>

                            <h6 class="mb-0">
                                <b>Tél : {{ $dossier['beneficiaire']['telephone'] }}</b> -
                                Email : {{ $dossier['beneficiaire']['email'] }}<br />
                            </h6>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <div class="btn btn-primary">{{ $dossier['fiche']['fiche_name'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <div class="card form-register">
                <div class="steps clearfix">
                    <ul role="tablist" id="etapeTabs">
                        @foreach ($etapes as $index => $e)
                            @php
                                $isActive = false;
                                $isCurrent = false;
                                if ($e->etape_number <= $dossier['etape_number']) {
                                    $isActive = true;
                                }
                                if ($e->etape_number == $dossier['etape_number']) {
                                    $isCurrent = true;
                                }
                            @endphp

                            <li @if ($isActive) wire:click="setTab({{ $e->etape_number }})" @endif
                                role="tab" aria-disabled="false"
                                class="nav-link {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}"
                                aria-selected="true">
                                <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                    <span class="current-info audible nav-link"></span>
                                    <div class="title">
                                        <span class="step-icon">{{ $e->etape_number }}</span>
                                        <span class="step-text">
                                            {{ strtoupper_extended($e->etape_desc) }}
                                            <small>
                                                @if ($dossier->etape_number == $e->etape_number)
                                                    <p>Status: {{ $dossier->status->status_name ?? '' }}</p>
                                                @endif
                                            </small>
                                        </span>
                                        <div class="inter_line"></div>

                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body px-0 pb-0">
                    @if (isset($tab))
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="border-bottom border-gray pb-2 p-5">{{ $etape_display['etape_desc'] }}</h3>
                            </div>
                            @if (isset($dossier->status->status_name) && $tab == $dossier->etape_number && $dossier->status->status_name != 'Refusé')
                                <div class="col-lg-6">
                                    <a class="btn btn-primary"
                                        href="{{ route('dossiers.next_step', $dossier->id) }}">Valider l'étape</a>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="card">
                                    <div class="card-header p-3 pb-0">
                                        <h6 class="mb-0">Formulaires</h6>
                                    </div>
                                    <div class="card-body border-radius-lg p-3">
                                        <div class="nav-wrapper position-relative end-0">
                                            <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                                @foreach ($forms_configs as $index => $form_handler)
                                                    @if ($form_handler->form->etape_number == $tab && $form_handler->form->type == 'form')
                                                        <li class="nav-item">
                                                            <a wire:click="display_form({{ $form_handler->form->id }})"
                                                                class="nav-link mb-0 px-0 py-1 {{ $form_handler->form->id == $form_id ? 'active' : '' }}">
                                                                {{ $form_handler->form->form_title }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-lg-6">
                                <div class="card">
                                    <div class="card-header pb-0 p-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-2">Documents</h6>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table align-items-center">
                                            <tbody>
                                                @foreach ($forms_configs as $index => $form_handler)
                                                    @if ($form_handler->form->etape_number == $tab && $form_handler->form->type == 'document')
                                                        {!! $form_handler->render([]) !!} <!-- Render without error array -->
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card form-register container mt-5 pt-5">
                @if (isset($form_id))

                    @php $form = $forms_configs[$form_id] @endphp

                    @if ($form->form->type == 'form')
                        <div class="row">
                            <div class="">
                                <h4>{{ $form->form->form_title }}</h4>

                                <form wire:submit.prevent="submit" wire:poll.visible>
                                    @csrf
                                    <input type="hidden" name="form_id" value="{{ $form->form->id }}">
                                    <input type="hidden" name="dossier_id" value="{{ $dossier->id }}">

                                    {!! $form->render([]) !!}

                                    <div class="row">
                                        <div class="form-group">
                                            <button class="btn btn-secondary" type="submit">Enregistrer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    @if ($form->form->type == 'rdv')
                        {!! $form->render([]) !!}
                        <div class="card container mt-5 pd-5">

                        @include('calendar')
                    </div>
                    @endif

                @endif

            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
<!-- jQuery -->
<!-- FullCalendar Core JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<!-- FullCalendar Plugins JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/daygrid/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/timegrid/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/interaction/main.min.js"></script>
<!-- FullCalendar Locale -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/fr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Existing initialization code
        console.log('DOM fully loaded and parsed');
        console.log(calendar)
        get_calendar();
        console.log(calendar)
        // Listen for the custom event from Livewire
        Livewire.on('loadCalendar', function() {

            console.log(calendar)
            get_calendar();
            console.log(calendar)

        });

        function get_calendar() {
            var calendarEl = document.getElementById('calendar');
            var token = $('meta[name="api-token"]').attr('content');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                editable: true,
                locale: 'fr',
                headerToolbar: {
                    start: 'title',
                    center: '',
                    end: 'today prev,next timeGrid timeGridWeek dayGridMonth'
                },
                slotMinTime: "08:00:00",
                slotMaxTime: "20:00:00",
                slotDuration: '00:30:00',
                allDaySlot: false,
                buttonText: {
                    prev: 'Précédent',
                    next: 'Suivant',
                    today: "Aujourd'hui",
                    month: 'Mois',
                    timeGrid: 'Journée',
                    week: 'Semaine',
                    day: 'Jour'
                },
                weekText: 'Sem.',
                allDayText: 'Toute la journée',
                moreLinkText: 'en plus',
                noEventsText: 'Aucun événement à afficher',
                events: [],
                dateClick: function(info) {
                    var date = moment(new Date(info.dateStr));
                    var formattedDate = date.format('YYYY-MM-DD HH:mm:ss');
                    $.ajax({
                        url: '/api/rdvs/save',
                        method: 'POST',
                        data: {
                            dossier_id: {{ $dossier->id ?? '' }},
                            start: formattedDate,
                            user_id: $('#form_config_user_id').val(),
                            type_rdv: $('#type_rdv').val(),
                        },
                        success: function(response) {
                            if (response.success) {
                                calendar.addEvent({
                                    start: formattedDate
                                });
                            } else {
                                alert('Failed to save event');
                            }
                        },
                        error: function() {
                            alert('Error occurred while saving event');
                        }
                    });
                },
                eventContent: function(arg) {
                    var eventDiv = document.createElement('div');
                    var titleDiv = document.createElement('div');
                    var descriptionDiv = document.createElement('div');

                    titleDiv.innerHTML = arg.event.title;
                    descriptionDiv.innerHTML = arg.event.extendedProps.description;

                    eventDiv.appendChild(titleDiv);
                    eventDiv.appendChild(descriptionDiv);

                    return {
                        domNodes: [eventDiv]
                    };
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    openEventModal(info.event);
                }
            });

            calendar.render();

            function fetchAndRenderEvents(userId) {
                $.ajax({
                    url: '/api/rdvs',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: {
                        user_id: userId
                    },
                    success: function(data) {
                        console.log(data)
                        var events = data.map(function(rdv) {
                            var startDate = new Date(rdv.date_rdv);
                            var endDate = new Date(startDate.getTime() + 60 * 60 * 1000);

                            return {
                                title: rdv.nom + ' ' + rdv.prenom,
                                start: startDate.toISOString(),
                                end: endDate.toISOString(),
                                description: 'Address: ' + rdv.adresse + ', ' + rdv.ville
                            };
                        });
                        calendar.removeAllEvents();
                        calendar.addEventSource(events);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching RDVs:', error);
                    }
                });
            }

            fetchAndRenderEvents($('#form_config_user_id').val());

            $('#form_config_user_id').change(function() {
                var selectedUserId = $(this).val();
                fetchAndRenderEvents(selectedUserId);
            });

            function openEventModal(event) {
                document.getElementById('eventTitle').textContent = event.title;
                document.getElementById('eventDescription').textContent = event.extendedProps.description;
                document.getElementById('eventStart').textContent = new Date(event.start).toLocaleString();
                document.getElementById('eventEnd').textContent = new Date(event.end).toLocaleString();
                $('#eventModal').modal('show');
            }
        }
    });
    document.addEventListener('DOMContentLoaded', function() {

        Livewire.on('initializeDropzones', (data) => {
            initializePdfModals();

        });
        // get_calendar();

        // Listen for the Livewire event to reinitialize Dropzone
        Livewire.on('initializeDropzones', (data) => {
            console.log('initializeDropzones');
            //     $('input:radio').radiocharm({

            // });

            $('input.choice_checked').trigger('click');
            $('select').select2();
            $('.datepicker').datepicker();


            // initializePdfModals();
            // Retrieve and parse the forms_configs
            var configs = data.forms_configs;
            initializeDropzones(configs);
            // Remove existing Dropzones to prevent multiple instances
            if (Dropzone.instances.length > 0) {
                Dropzone.instances.forEach(instance => instance.destroy());
            }

            Dropzone.autoDiscover = false;

            // Convert object to array and loop through configs
            Object.values(configs).forEach((formConfig) => {
                console.log(formConfig)
                if (formConfig.form.type === 'document') {
                    Object.keys(formConfig.formData).forEach((key) => {
                        console.log(key)

                        var dropzoneElementId = `#dropzone-${key}`;
                        var dropzoneElement = document.querySelector(dropzoneElementId);
                        if (!dropzoneElement) {
                            console.warn(`Element ${dropzoneElementId} not found.`);
                            return;
                        }
                        new Dropzone(dropzoneElement, {
                            method: 'post',
                            headers: {
                                // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            paramName: 'file',
                            sending: function(file, xhr, formData) {
                                formData.append('folder', 'dossiers');
                                formData.append('template', key);
                            },
                            init: function() {
                                this.on("success", function(file,
                                    response) {
                                    console.log(
                                        'Successfully uploaded:',
                                        response);
                                });
                                this.on("error", function(file, response) {
                                    console.log('Upload error:',
                                        response);
                                });
                            }
                        });
                    });
                }
            });




        });




        // Use Livewire's hook to run scripts after DOM updates
        Livewire.hook('message.processed', (message, component) => {
            // initializeDropzones();
            // initializePdfModals();
            get_calendar();
        });
    });

    function initializeDropzones(configs) {

        if (Dropzone.instances.length > 0) {
            Dropzone.instances.forEach(instance => instance.destroy());
        }

        Dropzone.autoDiscover = false;

        // Convert object to array and loop through configs
        Object.values(configs).forEach((formConfig) => {
            console.log(formConfig)
            if (formConfig.form.type === 'document') {
                Object.keys(formConfig.formData).forEach((key) => {
                    console.log(key)

                    var dropzoneElementId = `#dropzone-${key}`;
                    var dropzoneElement = document.querySelector(dropzoneElementId);
                    if (!dropzoneElement) {
                        console.warn(`Element ${dropzoneElementId} not found.`);
                        return;
                    }
                    new Dropzone(dropzoneElement, {
                        method: 'post',
                        headers: {
                            // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        paramName: 'file',
                        sending: function(file, xhr, formData) {
                            formData.append('folder', 'dossiers');
                            formData.append('template', key);
                        },
                        init: function() {
                            this.on("success", function(file,
                                response) {
                                console.log(
                                    'Successfully uploaded:',
                                    response);
                            });
                            this.on("error", function(file, response) {
                                console.log('Upload error:',
                                    response);
                            });
                        }
                    });
                });
            }
        });

    }

    function get_calendar() {
        var calendarEl = document.getElementById('calendar');
        var token = $('meta[name="api-token"]').attr('content');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            editable: true,
            locale: 'fr',
            headerToolbar: {
                start: 'title',
                center: '',
                end: 'today prev,next timeGrid timeGridWeek dayGridMonth'
            },
            slotMinTime: "08:00:00",
            slotMaxTime: "20:00:00",
            slotDuration: '00:30:00',
            allDaySlot: false,
            buttonText: {
                prev: 'Précédent',
                next: 'Suivant',
                today: "Aujourd'hui",
                month: 'Mois',
                timeGrid: 'Journée',
                week: 'Semaine',
                day: 'Jour'
            },
            weekText: 'Sem.',
            allDayText: 'Toute la journée',
            moreLinkText: 'en plus',
            noEventsText: 'Aucun événement à afficher',
            events: [],
            dateClick: function(info) {
                var date = moment(new Date(info.dateStr));
                var formattedDate = date.format('YYYY-MM-DD HH:mm:ss');
                $.ajax({
                    url: '/api/rdvs/save',
                    method: 'POST',
                    data: {
                        dossier_id: {{ $dossier->id ?? '' }},
                        start: formattedDate,
                        user_id: $('#form_config_user_id').val(),
                        type_rdv: $('#type_rdv').val(),
                    },
                    success: function(response) {
                        if (response.success) {
                            calendar.addEvent({
                                start: formattedDate
                            });
                        } else {
                            alert('Failed to save event');
                        }
                    },
                    error: function() {
                        alert('Error occurred while saving event');
                    }
                });
            },
            eventContent: function(arg) {
                var eventDiv = document.createElement('div');
                var titleDiv = document.createElement('div');
                var descriptionDiv = document.createElement('div');

                titleDiv.innerHTML = arg.event.title;
                descriptionDiv.innerHTML = arg.event.extendedProps.description;

                eventDiv.appendChild(titleDiv);
                eventDiv.appendChild(descriptionDiv);

                return {
                    domNodes: [eventDiv]
                };
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                openEventModal(info.event);
            }
        });

        calendar.render();

        function fetchAndRenderEvents(userId) {
            $.ajax({
                url: '/api/rdvs',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: {
                    user_id: userId
                },
                success: function(data) {
                    console.log(data)
                    var events = data.map(function(rdv) {
                        var startDate = new Date(rdv.date_rdv);
                        var endDate = new Date(startDate.getTime() + 60 * 60 * 1000);

                        return {
                            title: rdv.nom + ' ' + rdv.prenom,
                            start: startDate.toISOString(),
                            end: endDate.toISOString(),
                            description: 'Address: ' + rdv.adresse + ', ' + rdv.ville
                        };
                    });
                    calendar.removeAllEvents();
                    calendar.addEventSource(events);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching RDVs:', error);
                }
            });
        }

        fetchAndRenderEvents($('#form_config_user_id').val());

        $('#form_config_user_id').change(function() {
            var selectedUserId = $(this).val();
            fetchAndRenderEvents(selectedUserId);
        });

        function openEventModal(event) {
            document.getElementById('eventTitle').textContent = event.title;
            document.getElementById('eventDescription').textContent = event.extendedProps.description;
            document.getElementById('eventStart').textContent = new Date(event.start).toLocaleString();
            document.getElementById('eventEnd').textContent = new Date(event.end).toLocaleString();
            $('#eventModal').modal('show');
        }
    }





    function initializePdfModals() {
        // Remove existing event listeners to prevent multiple bindings
        $(document).off('click', '.pdfModal').off('click', '.imageModal').off('click', '.fillPDF').off('click', '.generatePdfButton');

        // Attach new event listeners
        $(document).on('click', '.imageModal', function(event) {
            var imgSrc = $(this).data('img-src');
            $('#imageInModal').attr('src', imgSrc);
            $('#imageModal').modal('show');
        });

        $(document).on('click', '.pdfModal', function(event) {
            var imgSrc = $(this).data('img-src');
            $('#pdfFrame').attr('src', imgSrc);
            $('#pdfModal').css('display', 'block');
        });

        $(document).on('click', '.fillPDF', function(event) {
            var form_id = $(this).data('form_id');
            var dossier_id = $(this).data('dossier_id');
            var name = $(this).data('name');

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
        $(document).on('click', '.generatePdfButton', function(event) {

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
    }
</script>
