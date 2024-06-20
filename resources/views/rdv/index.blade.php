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

    <script>
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
                var content = getEventContent(arg.event.title, arg.event.extendedProps.description);
                eventDiv.innerHTML = content;
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
                handleEventClick(info.event); // Redirect on event click
            }
        });

        calendar.render();

        var map;
        var markers = [];

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 48.8566, lng: 2.3522 },
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
                        var eventEnd = new Date(eventStart.getTime() + 60 * 60 * 1000); // Add 1 hour to the start date

                        // Check if event is within the current calendar view
                        if (eventStart >= start && eventEnd <= end) {
                            // Create event object for FullCalendar
                            var event = {
                                title: rdv.nom + ' ' + rdv.prenom,
                                start: rdv.date_rdv,
                                end: eventEnd.toISOString(),
                                description: rdv.adresse + '<br/>'+ rdv.cp + ' '+rdv.ville,
                                backgroundColor: rdv.color,
                                borderColor: rdv.color,
                                dossier_id: rdv.dossier_id
                            };
                            console.log(event)
                            // Create marker for Google Maps
                            var content = getEventContent(rdv.nom + ' ' + rdv.prenom, rdv.adresse + '<br/>'+ rdv.cp + ' '+rdv.ville);
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

        // function handleDateClick(info) {
        //     // Redirect to URL on date click
        //     var dateStr = info.dateStr;
        //     window.location.href = `/dossier/${dateStr}`;
        // }

        function handleEventClick(event) {
            console.log(event)
            alert('ok')
            window.location.href = `/dossier/show/${event.dossier_id}`;
        }
    </script>
@endsection
