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
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzcaFvxwi1XLyRHmPRnlKO4zcJXPOT5gM&libraries=marker&callback=initMap"></script>

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
        
        // Create Waze button with an image
        var wazeButton = document.createElement('a');
        var wazeImage = document.createElement('img');
        wazeImage.src = 'https://play-lh.googleusercontent.com/r7XL36PVNtnidqy6ikRiW1AHEIsjhePrZ8W5M4cNTQy5ViF3-lIDY47hpvxc84kJ7lw=w240-h480-rw'; // Replace with the path to your Waze image
        wazeImage.alt = '';
        wazeImage.style.width = '20px'; // Set the size of the image
        wazeImage.style.height = '20px';
        
        wazeButton.appendChild(wazeImage);
        wazeButton.onclick = function(e) {
            e.stopPropagation(); // Prevent the eventClick from being triggered
            var location = arg.event.extendedProps.location; // Ensure your event data has this field
            if (location) {
                var wazeUrl = `https://waze.com/ul?ll=${location}&navigate=yes`;
                window.open(wazeUrl, '_blank'); // Open the Waze URL in a new tab/window
            } else {
                alert("Location not available for this event.");
            }
        };
        
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

        var map;
        var markers = [];

        function initMap() {
            var mapOptions = {
                center: { lat: 48.8566, lng: 2.3522 },
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
            console.log($('#form_config_user_id').val())
            console.log(start)
            console.log(end)
            $.ajax({
                url: '/api/rdvs',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: {
                    user_id: $('#form_config_user_id').val(),
                    dpt: $('#dpt').val(),
                    client_id: {{auth()->user()->client_id ?? 0}},
                },
                success: function(data) {
                    console.log(data)
                    clearMarkers(); // Clear existing markers
                    var events = data.map(function(rdv) {
                        var eventStart = new Date(rdv.date_rdv);
                        var eventEnd = new Date(eventStart.getTime() + 90 * 60 *
                        1000); // Add 1 hour to the start date
                        console.log(eventStart)
                        console.log(eventEnd)
                        // Check if event is within the current calendar view
                        if (eventStart >= start && eventEnd <= end) {
                            // Create event object for FullCalendar
                            console.log(rdv)
                            var event = {
                                title: '<a  href="https://waze.com/ul?q='+ rdv.adresse + ' ' + rdv.cp + ' ' + rdv.ville+ '&navigate=yes" class="waze_button">'+ rdv.user_name+'<br/>'+rdv.nom + ' ' + rdv.prenom,
                                start: rdv.date_rdv,
                                end: eventEnd.toISOString(),
                                description: rdv.adresse + ' ' + rdv.cp + ' ' + rdv.ville + '<br/>' + formatFrenchPhoneNumber(rdv.telephone) + 
                 (rdv.dossier ? '<br/> MAR : ' + rdv.dossier.mar.client_title + ' / ' + rdv.dossier.mandataire_financier.client_title : ''),
    backgroundColor: rdv.color,
                                borderColor: rdv.color,
                                dossier_id: rdv.dossier_id,
                                dossier_folder: rdv.dossier_folder
                            };
                            console.log(event)
                            // Create marker for Google Maps
                                // Create marker for Google Maps
                                var content = getEventContent(rdv.user_name+'<br/>'+rdv.nom + ' ' + rdv.prenom, rdv.adresse +
                                    '<br/>' + rdv.cp + ' ' + rdv.ville);
                            console.log(rdv.lat)
                                    if(rdv.lat>0) {
                                var position = { lat: parseFloat(rdv.lat), lng: parseFloat(rdv.lng) };

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
                    console.log(markers)
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
            var dossierId = event.extendedProps.dossier_id; // Access the dossier_id
            var dossierFolder = event.extendedProps.dossier_folder; // Access the dossier_id
            console.log(dossierId); // Log the dossier_id
            console.log(dossierFolder); // Log the dossier_id
            window.location.href = `/dossier/show/${dossierFolder}`; // Redirect to the desired URL
        }
    </script>
@endsection
