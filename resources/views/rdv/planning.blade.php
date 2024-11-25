@extends('layouts.app')

@section('content')
    <div>
        <div class="">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('calendar')
                    </div>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <div id="map" style="height: 600px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
    <!-- Google Maps JavaScript API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzcaFvxwi1XLyRHmPRnlKO4zcJXPOT5gM&libraries=marker&callback=initMap">
    </script>
    @php
        $user = auth()->user();
        if ($user->type_id == 4) {
            $initialView = 'timeGridDay'; // For day view
        } else {
            $initialView = 'timeGridWeek'; // For week view
        }
    @endphp
    <script>
        function formatFrenchPhoneNumber(phoneNumber) {
            // Remove any non-digit characters
            let cleaned = phoneNumber.replace(/\D/g, '');
            // Match and group digits
            let match = cleaned.match(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/);
            // Format the phone number
            if (match) {
                return match[1] + ' ' + match[2] + ' ' + match[3] + ' ' + match[4] + ' ' + match[5];
            }
            return null;
        }
        var calendarEl = document.getElementById('calendar');
        var token = $('meta[name="api-token"]').attr('content'); // Get token from meta tag

        // Define the FullCalendar instance
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: '{{ $initialView }}',

            editable: true,
            locale: 'fr',
            headerToolbar: false,
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
                var content = getEventContent(arg.event.title, arg.event.extendedProps.description);

                // // Create Waze button with an image
                // var wazeButton = document.createElement('a');
                // var wazeImage = document.createElement('img');
                // wazeImage.src = 'https://play-lh.googleusercontent.com/r7XL36PVNtnidqy6ikRiW1AHEIsjhePrZ8W5M4cNTQy5ViF3-lIDY47hpvxc84kJ7lw=w240-h480-rw'; // Replace with the path to your Waze image
                // wazeImage.alt = '';
                // wazeImage.style.width = '20px'; // Set the size of the image
                // wazeImage.style.height = '20px';

                // wazeButton.appendChild(wazeImage);
                // wazeButton.onclick = function(e) {
                //     e.stopPropagation(); // Prevent the eventClick from being triggered
                //     var location = arg.event.extendedProps.location; // Ensure your event data has this field
                //     if (location) {
                //         var wazeUrl = `https://waze.com/ul?ll=${location}&navigate=yes`;
                //         window.open(wazeUrl, '_blank'); // Open the Waze URL in a new tab/window
                //     } else {
                //         alert("Location not available for this event.");
                //     }
                // };

                eventDiv.innerHTML = content;
                // eventDiv.appendChild(wazeButton); // Append the Waze button to the event content
                return {
                    domNodes: [eventDiv]
                };
            },
            datesSet: function(info) {
                fetchAndRenderEvents(info.start, info.end); // Fetch events for the visible date range
            },
            dateClick: function(info) {
                handleDateClick(info); // Redirect on slot click
            },
            eventClick: function(info) {
                handleEventClick(info.event); // Handle event click
            }

            
        });

        calendar.render();
        document.getElementById('today-button').addEventListener('click', function() {
        calendar.today();
    });

    document.getElementById('prev-button').addEventListener('click', function() {
        calendar.prev();
    });

    document.getElementById('next-button').addEventListener('click', function() {
        calendar.next();
    });

    document.getElementById('day-view-button').addEventListener('click', function() {
        calendar.changeView('timeGridDay');
    });

    document.getElementById('week-view-button').addEventListener('click', function() {
        calendar.changeView('timeGridWeek');
    });

    document.getElementById('month-view-button').addEventListener('click', function() {
        calendar.changeView('dayGridMonth');
    });
        var map;
        var markers = [];

        function initMap() {
            var mapOptions = {
                center: {
                    lat: 48.8566,
                    lng: 2.3522
                },
                zoom: 5
            };
            var mapElement = document.getElementById('map');

            if (mapElement) {
                var map = new google.maps.Map(mapElement, mapOptions);
            } else {
                console.error('Map element not found');
            }
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
                    type_rdv: $('#type_rdv').val(),
                    client_id: {{ auth()->user()->client_id ?? 0 }},
                    start: start.toISOString(),
                    end: end.toISOString(),
                },
                success: function(data) {

                    $('#calendar-liste tbody').html('');
                    clearMarkers(); // Clear existing markers
                    var events = data.map(function(rdv) {
                        var eventStart = new Date(rdv.date_rdv);

                        if (rdv.duration) {
                            var duration = rdv.duration;
                        } else {
                            var duration = 1.5;
                        }

                        console.log(rdv)
                        var eventEnd = new Date(eventStart.getTime() + (duration * 60 * 60 *
                            1000)); // Add 1 hour to the start date

                        // Check if event is within the current calendar view
                        if (eventStart >= start && eventEnd <= end) {


                            var event = {
                                title: '' + (rdv.user_name ?? '') + '<br/>' + (rdv.nom ?? '') +
                                    ' ' + (rdv.prenom ?? '') + (rdv.type_rdv == 3 ?
                                        'Indisponibilité' : '') + (rdv.type_rdv == 4 ? 'Congés' :
                                        ''),
                                start: rdv.date_rdv,
                                end: eventEnd.toISOString(),
                                description: (rdv.adresse ?? '') + ' ' + (rdv
                                        .cp ?? '') + ' ' + (rdv
                                        .ville ?? '') + '<br/>' + (rdv.telephone ?
                                        formatFrenchPhoneNumber(rdv.telephone) : '') + (rdv.type_rdv<3 ? '<br/>RDV MAR '+rdv.type_rdv : '') +
                                    (rdv.dossier != null ? '<br/> MAR : ' + (rdv.dossier.mar > 0 ?
                                        rdv.dossier.mar.client_title : '') + ' / ' + (rdv
                                        .dossier.mandataire_financier > 0 ? ' / ' + rdv.dossier
                                        .mandataire_financier.client_title : '') : '') + (rdv
                                        .observations ? 'Observations ' + rdv.observations : ''),
                                backgroundColor: rdv.color,
                                borderColor: rdv.color,
                                rdv_id: rdv.id,
                                type_rdv_title: rdv.type_rdv_title,
                                accompagnateur: (rdv.user_name ?? ''),
                                observations: rdv.observations,
                                coordonnees: (rdv.adresse ?? '') + ' ' + (rdv.cp ?? '') + ' ' + (rdv
                                    .ville ?? '') + '<br/>' + (rdv.telephone ?
                                    formatFrenchPhoneNumber(rdv.telephone) : ''),
                                adresse: (rdv.adresse ?? '') + ' ' + (rdv.cp ?? '') + ' ' + (rdv
                                    .ville ?? ''),
                                beneficiaire: (rdv.nom ?? '') + ' ' + (rdv.prenom ?? ''),
                                dossier_id: rdv.dossier_id,
                                mar: (rdv.dossier != null ? rdv.dossier.mar.client_title : ''),
                                mandataire_financier: (rdv.dossier != null ? rdv.dossier
                                    .mandataire_financier.client_title > 0 : ''),
                                dossier_folder: rdv.dossier_folder
                            };

                            var content = getEventContent(rdv.user_name + '<br/>' + rdv.nom + ' ' + rdv
                                .prenom, rdv.adresse +
                                '<br/>' + rdv.cp + ' ' + rdv.ville);
                            console.log(rdv.lat)
                            if (rdv.lat > 0) {
                                var position = {
                                    lat: parseFloat(rdv.lat),
                                    lng: parseFloat(rdv.lng)
                                };

                                const marker = new google.maps.marker.AdvancedMarkerElement({
                                    position: position,
                                    map: map,
                                    title: rdv.nom + ' ' + rdv.prenom
                                });

                                const infowindow = new google.maps.InfoWindow({
                                    content: content
                                });

                                marker.addListener('gmp-click', () => {
                                    infowindow.open({
                                        anchor: marker,
                                        map: map,
                                        shouldFocus: false,
                                    });
                                });

                                markers.push(marker);
                            }
                            return event;
                        }
                    }).filter(event => event !== undefined);
                    calendar.removeAllEvents();
                    calendar.addEventSource(events);
                    calendar.refetchEvents();

                    fetch_table(events);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching RDVs:', error);
                }
            });
        }

        function fetch_table(events) {
            // Clear the existing table body
            $('#calendar-liste tbody').html('');

            // Loop through events and add them to the table
            events.forEach(function(event) {
                var rdv_id = event.rdv_id ?? 0; // Access the dossier_id
                var dossierId = event.dossier_id ?? null; // Access the dossier_id
                var dossierFolder = event.dossier_folder ?? null; // Access the dossier_id
                var mar = event.mar ?? ''; // Access the dossier_id
                var wazeButtonHtml = '';

                if (event.adresse) {
                    // Encode the address for use in URL
                    var encodedAddress = encodeURIComponent(event.adresse + ' ' + event.cp + ' ' + event.ville);
                    // Waze URL with the encoded address
                    var wazeUrl = `https://waze.com/ul?q=${encodedAddress}&navigate=yes`;
                    // URL to the Waze logo image
                    var wazeLogoUrl = 'data:image/png;base64,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'; // Adjust this path to your image location
                    // Create the Waze button HTML
                    wazeButtonHtml =
                        `<a href="${wazeUrl}" target="_blank"><img src="${wazeLogoUrl}" alt="Waze" style="width:20px;height:20px;"></a>`;
                }


                var rowHtml = `
            <tr>
                <td>${new Date(event.start).toLocaleString()}</td>
                <td style="color:white;background-color: ${event.backgroundColor};">${event.accompagnateur}</td>
                <td><a target="_blank" href="/dossier/show/${dossierFolder}">${event.beneficiaire}</a></td>
                <td>${event.coordonnees}</td>
                <td>${event.type_rdv_title}</td>
                                <td>${event.mar ?? ''}</td>

                <td>${event.observations ?? ''}</td>
                <td>${wazeButtonHtml}</td>
            </tr>
        `;
                // Append the row to the table body
                $('#calendar-liste tbody').append(rowHtml);
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
            console.log(start)
            console.log(end)
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

        // function handleDateClick(info) {
        //     // Redirect to URL on date click
        //     var dateStr = info.dateStr;
        //     window.location.href = `/dossier/${dateStr}`;
        // }

        function handleEventClick(event) {

            var rdv_id = event.extendedProps.rdv_id; // Access the dossier_id
            var dossierId = event.extendedProps.dossier_id; // Access the dossier_id
            var dossierFolder = event.extendedProps.dossier_folder; // Access the dossier_id

            if (dossierId) {

                window.open(`/dossier/show/${dossierFolder}`, '_blank'); // Redirect to the desired URL in a new tab
            } else {
                window.open(`/rdv/show/${rdv_id}`, '_blank'); // Redirect to the desired URL in a new tab

            }

        }
    </script>
@endsection
