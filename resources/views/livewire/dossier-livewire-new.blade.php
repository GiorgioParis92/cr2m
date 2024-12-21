<div>

    <div wire:loading wire:target="add_row,remove_row,display_form,setTab,handleFieldUpdated,set_form" class="loader-overlay">
     
        <div class="spinner"></div>
    </div>

    <div class="container-fluid my-3 py-3">
        @if (session()->has('message'))
            <div class="alert alert-success" id="flashMessage">
                {{ session('message') }}
            </div>
        @endif
        <div class="row mb-5">
            <div class="col-lg-3">
                <div class="card">

                    <div class="timeline timeline-one-side">
                        @foreach ($etapes as $index => $e)
                            @php
                                $isActive = false;
                                $isCurrent = false;
                                $isTab = false;
                                if ($e['order_column'] <= $dossier['etape']['order_column']) {
                                    $isActive = true;
                                }
                                if (
                                    $e['order_column'] == $dossier['etape']['order_column'] &&
                                    is_user_allowed($e['etape_name']) == true
                                ) {
                                    $isCurrent = true;
                                }

                                if ($e['id'] == $last_etape) {
                                    $isTab = true;
                                }
                                if (is_user_allowed($e['etape_name']) == false) {
                                    $isAllowed = false;
                                } else {
                                    $isAllowed = true;
                                }
                                if (is_user_forbidden($e['etape_name']) == true) {
                                    $isAllowed = false;
                                    $isCurrent = false;
                                }

                            @endphp
                            @if($isActive && $isAllowed)
                            <div class="pe-auto cursor-pointer timeline-block mb-3 p-3 {{ $isCurrent ? 'bg-primary' : '' }} {{ $tab == $e['id'] ? 'bg-secondary' : '' }}"
                                @if ($isActive && $isAllowed) wire:click="setTab({{ $e['etape_number'] }})" @endif>
                                <span class="timeline-step">
                                    <span>{{ $e['etape_icon'] ?? '' }}</span>
                                </span>

                                <div class="timeline-content">
                                    <h6
                                        class="{{ $tab == $e['id'] ? 'text-white' : 'text-dark' }} text-sm font-weight-bold mb-0">
                                        {{ strtoupper_extended($e['etape_desc']) }}</h6>
                                    @if (!empty($steps) && isset($steps['step_' . $e['etape_number']]))
                                        <p
                                            class="{{ $tab == $e['id'] ? 'text-white' : 'text-secondary' }} font-weight-bold text-xs mt-1 mb-0">
                                            validée le :
                                            {{ format_date($steps['step_' . $e['etape_number']]['meta_value']) ?? '' }}
                                            par {{ $steps['step_' . $e['etape_number']]['user_name'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                </div>
            </div>
            <div class="col-lg-9 mt-lg-0 mt-4">
                <!-- Card Profile -->
                <div class="card card-body" id="profile">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-sm-auto col-4">

                        </div>
                        <div class="col-sm-auto col-8 my-auto">
                            <div class="h-100">
                                <h4 class="mb-1 font-weight-bolder">
                                    {{ $dossier['beneficiaire']['nom'] }} {{ $dossier['beneficiaire']['prenom'] }}
                                </h4>
                                <p class="mb-0 font-weight-bold text-sm">
                                    {{ strtoupper_extended(($dossier['beneficiaire']['numero_voie'] ?? '') . ' ' . $dossier['beneficiaire']['adresse'] . ' ' . $dossier['beneficiaire']['cp'] . ' ' . $dossier['beneficiaire']['ville']) }}
                                </p>
                                <p class="mb-0 font-weight-bold text-sm">
                                    <b>Tél : {{ $dossier['beneficiaire']['telephone'] }}</b> -
                                    Email : {{ $dossier['beneficiaire']['email'] }}<br />
                                </p>
                                <p class="mb-0 font-weight-bold text-sm">
                                <div class="btn btn-primary">{{ $dossier['fiche']['fiche_name'] }}</div>
                                <div
                                    class="btn bg-primary bg-{{ couleur_menage($dossier->beneficiaire->menage_mpr) }}">
                                    {{ strtoupper(texte_menage($dossier['beneficiaire']['menage_mpr'])) }}
                                </div>
                                </p>

                            </div>
                        </div>
                        <div class="col-sm-auto col-4">

                        </div>
                        <div class="col-sm-auto col-8 my-auto">
                            <div>
                                @if (isset($dossier->mar_client))
                                    @if (Storage::disk('public')->exists($dossier->mar_client->main_logo))
                                        <img style="max-width: 150px"
                                            src="{{ asset('storage/' . $dossier->mar_client->main_logo) }}">
                                    @endif
                                    {{ $dossier->mar_client->client_title }}
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3">

                            <div class="">

                                @if (auth()->user()->client_id == 0 ||
                                        (auth()->user()->client_id != 3 && auth()->user()->type_id != 7 && auth()->user()->type_id != 4))
                                    @if ($dossier['annulation'] != 1)
                                        <a wire:click="toggleDossier({{ $dossier->id }})"
                                            class="btn btn-danger">Annuler le dossier</a>
                                    @else
                                        <a wire:click="toggleDossier({{ $dossier->id }})"
                                            class="btn btn-warning">Rétablir le dossier</a>
                                    @endif
                                @endif
                            </div>
                            <br />
                            <div>
                                <select class="no_select2 form-control" name="installateur"
                                    wire:change="update_installateur($event.target.value)">
                                    <option value="">Choisir un installateur</option>
                                    @foreach ($installateurs as $installateur)
                                        <option @if ($dossier['installateur'] == $installateur['id']) selected @endif
                                            value="{{ $installateur['id'] }}">
                                            {{ $installateur['client_title'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row mt-4">
                    <div class="col-12 col-lg-12">
                        <div class="card ">
                            <div class="card-header pb-0 p-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-2">Documents du dossier</h6>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <x-document-table-component :docs="$docs" :dossier="$dossier" />

                            </div>
                        </div>
                    </div>
                </div>

                @if ($forms)

                    <div class="row">
                        <div class="col-12">
                            <div class="card mt-4" id="basic-info">
                                <div class="card-header">
                                    <h5>Formulaires</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="nav-wrapper position-relative end-0">
                                            <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                        
                                                @foreach ($forms as $form)
                                                    @if ($form->type == 'form')
                                                        <li class="nav-item active" wire:click="set_form({{$form->id}})">
                                                            
                                                            <a wire:click="set_form({{$form->id}})"
                                                                class="nav-link mb-0 px-0 py-1 {{ $form->id == $set_form ? 'active' : '' }}">
                                                                {{ $form->form_title }}

                                      
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card mt-4" id="basic-info">
                                <div class="card-header">
                                    <h5>Documents</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="nav-wrapper position-relative end-0">
                                            <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                                @foreach ($forms as $form)
                                                    @if ($form->type == 'document')
                                                    <div class="table-responsive" wire:poll>


                                                        <table class="table align-items-center">
                                                            <tbody>
                                                                @php                 
                                                                $handler = new App\FormModel\FormConfigHandler($this->dossier, $form);
                                                                @endphp
                                                                {{-- @foreach ($forms_configs as $index => $form_handler)
                                                                    @if ($form_handler->form->etape_number == $tab && $form_handler->form->type == 'document') --}}
                                                                        {!! $handler->render([]) !!} <!-- Render without error array -->
                                                                    {{-- @endif
                                                                @endforeach --}}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="card mt-4 pl-4 pr-4 pb-3" id="basic-info">
                            {{-- <div class="card-header">
                                <h5>Titre Formulaire</h5>
                            </div> --}}
                            <div class="card-body p-0">
                                <div class="row">
                                
                                    @if(isset($config))
                                    
                                   
                                    @foreach($config as $conf)

                                        @if(View::exists('livewire.forms.' . $conf['type']))

                                        @livewire("forms.{$conf['type']}", ['conf' => $conf,'form_id'=>$set_form,'dossier_id'=>$dossier->id], key($conf['id']))
                                        
                                      
                                        @else
                                            <p style="background:red">Component for type "{{ $conf['type'] }}" not found.</p>
                                        @endif
                                        @if($conf['type']=='table')
                           
                                    @endif
                                    @endforeach
                            
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
    <style>
        .board {
            display: block;
            padding: 20px;
            overflow-x: auto;
            height: auto;
            width: 100%
        }
    
        .column {
            /* background-color: #ebecf0; */
            border-radius: 3px;
    
            margin-right: 1%;
            padding: 10px;
            flex-shrink: 0;
            /* max-height: 280px; */
            overflow-x: hidden;
            overflow-y: scroll;
        }
    
        .column-header {
            font-weight: bold;
            padding-bottom: 10px;
        }
    
        .ticket {
            background-color: white;
            border-radius: 3px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: move;
            box-shadow: 0 1px 0 rgba(9, 30, 66, .25);
        }
    
        .add-column,
        .add-ticket {
            background-color: rgba(9, 30, 66, .04);
            color: #172b4d;
            border: none;
            padding: 10px;
            border-radius: 3px;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }
    
        .add-column:hover,
        .add-ticket:hover {
            background-color: rgba(9, 30, 66, .08);
        }
    
        #new-column {
            width: 272px;
            margin-right: 10px;
        }
    
        .column {
    
    
            /* Hide scrollbar for IE, Edge and Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    
        .column::-webkit-scrollbar {
            display: none;
        }
    
        .col-xl-4.col-sm-4.mb-xl-0.mb-4.column {
            max-width: 32%;
        }
    
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
    
        /* CSS for the spinner */
        .spinner {
            border: 8px solid rgba(0, 0, 0, 0.1);
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
            margin: auto;
            top: 46vh;
            position: relative;
        }
    
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
    
            to {
                transform: rotate(360deg);
            }
        }
    
        .thumbnail_hover {
            display: none;
            position: absolute;
            pointer-events: none;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-shadow: 8px 7px 9px;
            z-index: 9999999999;
        }
    
        .p_thumbnail:hover {
            cursor: pointer;
        }
    
        .p_thumbnail:hover .thumbnail_hover {
            display: block;
        }
    </style>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function(event) {
   document.addEventListener('livewire:load', () => {
       console.log('livewire loaded');
       // ... your dropzone initialization code here
   });
});

document.addEventListener('photoComponentUploaded', function(event) {
    if (window.Livewire && typeof Livewire.emit === 'function') {
        alert('emit')
        Livewire.emit('fileUploaded', event.detail);
    } else {
        // If Livewire isn't ready, you can store these events and emit later
        // or handle accordingly
    }
});
</script>
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
    document.addEventListener('DOMContentLoaded', function(event) {


        // Your code here


        Livewire.on('loadCalendar', function() {


        });

        function get_calendar() {


            var calendarEl = document.getElementById('calendar');
            var token = $('meta[name="api-token"]').attr('content'); // Get token from meta tag

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                editable: true,
                locale: 'fr',
                headerToolbar: {
                    start: 'title',
                    center: '',
                    end: 'today prev,next timeGrid timeGridWeek dayGridMonth'
                },
                slotMinTime: "07:00:00",
                slotMaxTime: "21:00:00",
                slotDuration: '00:15:00',
                eventColor: '#cb0c9f',
                buttonText: {
                    prev: 'Précédent',
                    next: 'Suivant',
                    today: "Aujourd'hui",
                    month: 'Mois',
                    timeGrid: 'Journée',
                    week: 'Semaine',
                    day: 'Jour'
                },
                allDaySlot: false,
                weekText: 'Sem.',
                allDayText: 'Toute la journée',
                moreLinkText: 'en plus',
                noEventsText: 'Aucun événement à afficher',
                events: [],
                eventContent: function(arg) {
                    var eventDiv = document.createElement('div');
                    var content = getEventContent(arg.event.title, arg.event.extendedProps
                        .description);
                    eventDiv.innerHTML = content;
                    return {
                        domNodes: [eventDiv]
                    };
                },
                datesSet: function(info) {
                    fetchAndRenderEvents(info.start, info
                        .end); // Fetch events for the visible date range
                }
            });

            calendar.render();

            var map;
            var markers = [];

            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: 48.8566,
                        lng: 2.3522
                    },
                    zoom: 5
                });
            }

            function fetchAndRenderEvents(start, end) {
                $.ajax({
                    url: '/api/rdvs',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: {
                        user_id: $('#form_config_user_id').val(),
                        dpt: $('#dpt').val(),
                    },
                    success: function(data) {
                        clearMarkers(); // Clear existing markers
                        var events = data.map(function(rdv) {
                            var eventStart = new Date(rdv.date_rdv);
                            var eventEnd = new Date(eventStart.getTime() + 60 * 60 *
                                1000); // Add 1 hour to the start date

                            // Check if event is within the current calendar view
                            if (eventStart >= start && eventEnd <= end) {
                                // Create event object for FullCalendar
                                var event = {
                                    title: rdv.nom + ' ' + rdv.prenom,
                                    start: rdv.date_rdv,
                                    end: eventEnd.toISOString(),
                                    description: rdv.adresse + '<br/>' + rdv.cp + ' ' +
                                        rdv.ville,
                                    backgroundColor: rdv.color,
                                    borderColor: rdv.color
                                };

                                // Create marker for Google Maps
                                var content = getEventContent(rdv.nom + ' ' + rdv.prenom,
                                    rdv.adresse + '<br/>' + rdv.cp + ' ' + rdv.ville);
                                var marker = new google.maps.Marker({
                                    position: {
                                        lat: parseFloat(rdv.lat),
                                        lng: parseFloat(rdv.lng)
                                    },
                                    map: map,
                                    title: rdv.nom + ' ' + rdv.prenom
                                });

                                var infowindow = new google.maps.InfoWindow({
                                    content: content
                                });

                                marker.addListener('click', function() {
                                    infowindow.open(map, marker);
                                });

                                markers.push(marker);

                                return event;
                            }
                        }).filter(event => event !== undefined);

                        calendar.removeAllEvents();
                        calendar.addEventSource(events);
                        calendar.refetchEvents();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching RDVs:', error);
                    }
                });
            }

            // Clear markers from map
            function clearMarkers() {
                markers.forEach(marker => marker.setMap(null));
                markers = [];
            }

            // Generate HTML content for events and markers
            function getEventContent(title, description) {
                return `<div>
                        <strong>${title}</strong>
                        <br>
                        <span>${description}</span>
                    </div>`;
            }

            // Fetch and render events when the dropdown value changes
            $('#form_config_user_id').change(function() {
                var start = calendar.view.activeStart;
                var end = calendar.view.activeEnd;
                clearMarkers();
                fetchAndRenderEvents(new Date(start), new Date(end));
            });

            function handleSelectionChange(select) {
                var start = calendar.view.activeStart;
                var end = calendar.view.activeEnd;
                fetchAndRenderEvents(new Date(start), new Date(end));
            }

            // Initial fetch
            $(document).ready(function() {
                var start = calendar.view.activeStart;
                var end = calendar.view.activeEnd;
                fetchAndRenderEvents(new Date(start), new Date(end));
            });

        }
    });

    $(document).ready(function() {
        $('.isTab').click();
        $('#etape').val($('#current_etape').val()); // Assuming `etape_number` exists

        // Optionally trigger the change event to notify Livewire or any other event handler
        $('#etape').trigger('change');
    });

    document.addEventListener('livewire:update', function() {
        initializeDeleteButtons();

    });
    document.addEventListener('livewire:load', function() {
        // After Livewire component is fully loaded, trigger the fetch
        // Livewire.emit('fetchResponseData');
    });

    function initializeDeleteButtons() {
        $('.delete_photo').off('click').on('click', function() {
            var link = $(this).data('val');
            $.ajax({
                url: '/delete_file',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    link: link,
                },
                success: function(response) {
                    console.log('Successfully deleted:', response);
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

    $(document).ready(function() {
        initializeDeleteButtons();
    });
    document.addEventListener('DOMContentLoaded', function() {
        $('#copyButton').on('click', function() {
            // The data to be copied to the clipboard
            const data = $(this).data('folder');
            $(this).removeClass('btn-primary')
            $(this).addClass('btn-success')
            // Create a temporary textarea element to hold the text
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(data).select();

            // Copy the text to the clipboard
            try {
                document.execCommand('copy');

            } catch (err) {
                alert('Failed to copy data to clipboard:', err);
            }

            // Remove the temporary element
            $temp.remove();
        });
        Livewire.on('pageLoaded', (data) => {

            var configs = data.forms_configs;

            initializeDropzones(configs);



        })

        Livewire.on('setTab', (data) => {



            const firstKey = Object.keys(data.forms_configs)[0];

            // Get the first element using that key
            const firstElement = data.forms_configs[firstKey];

            // Log the first element

            $('#etape').val(firstElement.form.etape_number); // Assuming `etape_number` exists

            // Optionally trigger the change event to notify Livewire or any other event handler
            $('#etape').trigger('change');


            var configs = data.forms_configs;
            initializeDropzones(configs);
            $('.delete_photo').click(function() {

                var link = $(this).data('val');
                $.ajax({
                    url: '/delete_file',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        link: link,

                    },
                    success: function(response) {


                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors)
                                .join(', ');
                        }

                    }
                });
            })
        });


        Livewire.on('ok_photo', (data) => {

        });
        // Listen for the Livewire event to reinitialize Dropzone
        Livewire.on('initializeDropzones', (data) => {




            $('.datepicker').datepicker({
                language: 'fr',
                dateFormat: 'dd/mm/yy', // See format options on parseDate

            });

            initializePdfModals();


            var configs = data.forms_configs;
            initializeDropzones(configs);




        });


    });

    function initializeDropzones(configs) {

        // Destroy existing Dropzone instances
        if (Dropzone.instances.length > 0) {
            Dropzone.instances.forEach(instance => instance.destroy());
        }

        Dropzone.autoDiscover = false;

        // Find all elements with the class "dropzone"
        const dropzoneElements = document.querySelectorAll('.dropzone');

        dropzoneElements.forEach((dropzoneElement) => {
            // Extract the unique ID and upload URL from the element
            const dropzoneId = dropzoneElement.id;
            const key = dropzoneId.replace('dropzone-', '');
            const uploadUrl = dropzoneElement.getAttribute('data-upload-url');
            const form_id = dropzoneElement.getAttribute('data-form_id');

            if (!dropzoneElement) {
                return;
            }

            if (dropzoneElement.dropzone) {
                return;
            }

            // Initialize the dropzone with the dynamic upload URL
            new Dropzone(dropzoneElement, {
                url: uploadUrl,
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token if using Laravel's CSRF protection
                },
                paramName: 'file',
                sending: function(file, xhr, formData) {
                    formData.append('folder', 'dossiers');
                    formData.append('template', key);
                },
                init: function() {
                    this.on("success", function(file, response) {

                        $('#doc-' + dropzoneId).val(response);
                        $('#doc-' + dropzoneId).blur();

                        Livewire.emit('fileUploaded', [form_id, key, response]);

                    });
                    this.on("error", function(file, response) {

                    });
                }
            });
        });
    }








    function initializePdfModals() {




        $('.close').on('click', function() {

            $('.modal').modal('hide');
        });
        $('.close_button').on('click', function() {

            $('.modal').modal('hide');
        });
        // Remove existing event listeners to prevent multiple bindings
        $(document).off('click', '.signable').off('click', '.pdfModal').off('click', '.imageModal').off('click',
            '.fillPDF').off('click', '.generatePdfButton').off('click', '.generateConfig').off('click',
            '.check_signature').off('rdv_modal',
            '.generatePdfButton');

        // Attach new event listeners
        $(document).on('click', '.imageModal', function(event) {
            $('#imageInModal').attr('src', '');
            var imgSrc = $(this).data('img-src');
            imgSrc += `?time=${new Date().getTime()}`;
            $('#imageInModal').attr('src', imgSrc);
            $('#imageModal').modal('show');
        });
        // Attach new event listeners
        $(document).on('click', '.show_rdv', function(event) {
            var rdv_id = $(this).data('rdv_id');

            if (rdv_id == undefined || rdv_id == '') {
                rdv_id = 0
            }

            $.ajax({
                url: '/api/rdvs', // Adjust this URL to your actual API endpoint
                type: 'GET',
                data: {
                    rdv_id: rdv_id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Include CSRF token if using Laravel's CSRF protection
                },
                success: function(response) {

                    // Clear previous data
                    $('#rdv_id').val('0');
                    $('#rdv_french_date').val('');
                    $('#rdv_hour').val('');
                    $('#rdv_minute').val('');
                    $('#rdv_user_id').val('');
                    $('#rdv_status').val('');
                    $('#rdv_observations').val('');
                    $('#rdv_type_rdv').val($('#type_rdv').val());
                    $('#rdv_nom').val("{!! $dossier['beneficiaire']['nom'] ?? '' !!}");
                    $('#rdv_prenom').val("{!! $dossier['beneficiaire']['prenom'] ?? '' !!}");
                    $('#rdv_adresse').val("{!! $dossier['beneficiaire']['adresse'] ?? '' !!}");
                    $('#rdv_cp').val("{!! $dossier['beneficiaire']['cp'] ?? '' !!}");
                    $('#rdv_ville').val("{!! $dossier['beneficiaire']['ville'] ?? '' !!}");
                    $('#rdv_telephone').val("{!! $dossier['beneficiaire']['telephone'] ?? '' !!}");
                    $('#rdv_email').val("{!! $dossier['beneficiaire']['email'] ?? '' !!}");
                    $('#rdv_telephone_2').val("{!! $dossier['beneficiaire']['telephone_2'] ?? '' !!}");
                    $('#rdv_dossier_id').val("{!! $dossier['id'] ?? '' !!}");
                    $('#rdv_client_id').val("{!! $dossier['client_id'] ?? '' !!}");
                    $('#rdv_lat').val("{!! $dossier['beneficiaire']['lat'] ?? '' !!}");
                    $('#rdv_lng').val("{!! $dossier['beneficiaire']['lng'] ?? '' !!}");

                    if (response && response.length > 0) {

                        var rdv = response[0];
                        $.each(rdv, function(key, value) {

                            // Populate form fields
                            $('#rdv_' + key).val(value);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading rdv data:', error);
                }
            });

            $('#rdv_modal').modal('show');
        });

        $(document).on('click', '.pdfModal', function(event) {
            $('#pdfFrame').attr('src', '');

            var imgSrc = $(this).data('img-src');
            imgSrc += `?time=${new Date().getTime()}`;

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
                        $('#pdfFrame').attr('src', '');

                        var filePathWithTimestamp = response.file_path + '?t=' + new Date()
                            .getTime();

                        // Display the PDF in an iframe
                        $('#pdfFrame').attr('src', filePathWithTimestamp);
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
            var name = $(this).data('name'); // Get the template from data attribute
            var dossier_id = $(this).data('dossier_id'); // Get the dossier ID from data attribute
            var form_id = $(this).data('form_id'); // Get the dossier ID from data attribute
            var generation = $(this).data('generation'); // Get the dossier ID from data attribute
            var identify = $(this).data('identify'); // Get the dossier ID from data attribute

            $.ajax({
                url: '/api/generate-pdf', // Adjust this URL to your actual API endpoint
                type: 'POST',
                data: {
                    dossier_id: dossier_id,
                    generation: generation,
                    form_id: form_id,
                    name: name,
                    identify: identify,
                    template: template
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Include CSRF token if using Laravel's CSRF protection
                },
                success: function(response) {
                    if (response.file_path) {
                        $('#pdfFrame').attr('src', '');

                        var filePathWithTimestamp = response.file_path + '?t=' + new Date()
                            .getTime();

                        // Display the PDF in an iframe
                        $('#pdfFrame').attr('src', filePathWithTimestamp);
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
        $(document).on('click', '.generateConfig', function(event) {

            var template = $(this).data('template'); // Get the template from data attribute
            var dossier_id = $(this).data('dossier_id'); // Get the dossier ID from data attribute
            var form_id = $(this).data('form_id'); // Get the dossier ID from data attribute
            var config_id = $(this).data('config_id'); // Get the dossier ID from data attribute
            var generation = $(this).data('generation'); // Get the dossier ID from data attribute
            var title = $(this).data('title'); // Get the dossier ID from data attribute

            $.ajax({
                url: '/api/generate-config', // Adjust this URL to your actual API endpoint
                type: 'POST',
                data: {
                    dossier_id: dossier_id,
                    generation: generation,
                    form_id: form_id,
                    config_id: config_id,
                    title: title,
                    template: template
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Include CSRF token if using Laravel's CSRF protection
                },
                success: function(response) {
                    if (response.file_path) {
                        $('#pdfFrame').attr('src', '');

                        var filePathWithTimestamp = response.file_path + '?t=' + new Date()
                            .getTime();

                        // Display the PDF in an iframe
                        $('#pdfFrame').attr('src', filePathWithTimestamp);
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
        $(document).on('click', '.check_signature', function(event) {
            var template = $(this).data('template'); // Get the template from data attribute
            var dossier_id = $(this).data('dossier_id'); // Get the dossier ID from data attribute
            var form_id = $(this).data('form_id'); // Get the dossier ID from data attribute
            var generation = $(this).data('generation'); // Get the dossier ID from data attribute
            var signature_request_id = $(this).data(
                'signature_request_id'); // Get the dossier ID from data attribute
            var document_id = $(this).data('document_id'); // Get the dossier ID from data attribute

            $.ajax({
                url: '/api/yousign-status', // Adjust this URL to your actual API endpoint
                type: 'POST',
                data: {
                    dossier_id: dossier_id,
                    generation: generation,
                    form_id: form_id,
                    signature_request_id: signature_request_id,
                    document_id: document_id,
                    template: template
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Include CSRF token if using Laravel's CSRF protection
                },
                success: function(response) {


                    if (response == 'ongoing') {
                        $('#message_' + template).html('Le document est en cours de signature');
                    }

                },
                error: function(xhr, status, error) {
                    console.error('Error generating PDF:', error);
                }
            });

        });


        $(document).on('click', '.signable', function(event) {

            var template = $(this).data('template'); // Get the template from data attribute
            var dossier_id = $(this).data('dossier_id'); // Get the dossier ID from data attribute
            var form_id = $(this).data('form_id'); // Get the dossier ID from data attribute
            var generation = $(this).data('generation'); // Get the dossier ID from data attribute
            var fields = $(this).data('fields'); // Get the dossier ID from data attribute
            var name = $(this).data('name'); // Get the dossier ID from data attribute

            $.ajax({
                url: '/api/yousign', // Adjust this URL to your actual API endpoint
                type: 'POST',
                data: {
                    dossier_id: dossier_id,
                    generation: generation,
                    form_id: form_id,
                    fields: fields,
                    name: name,
                    template: template
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Include CSRF token if using Laravel's CSRF protection
                },
                success: function(response) {
                    console.log(response)
                },
                error: function(xhr, status, error) {
                    console.error('Error generating PDF:', error);
                }
            });
        });

    }
</script>
