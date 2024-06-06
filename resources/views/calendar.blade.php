<script src="{{ asset('frontend/assets/js/fullcalendar/packages/core/main.js') }}"></script>
<script src="{{ asset('frontend/assets/js/fullcalendar/packages/daygrid/main.js') }}"></script>
<script src="{{ asset('frontend/assets/js/fullcalendar/packages/interaction/main.js') }}"></script>
<link rel="stylesheet" href="{{ asset('frontend/assets/js/fullcalendar/packages/core/main.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/assets/js/fullcalendar/packages/daygrid/main.css') }}">
<link href='https://unpkg.com/@fullcalendar/core@4.3.1/main.min.css' rel='stylesheet' />
<link href='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.css' rel='stylesheet' />
<link href='https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.min.css' rel='stylesheet' />
<script src='https://unpkg.com/@fullcalendar/core@4.3.1/main.min.js'></script>
<script src='https://unpkg.com/@fullcalendar/interaction@4.3.0/main.min.js'></script>
<script src='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.js'></script>
<script src='https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.min.js'></script>
<script src='https://unpkg.com/@fullcalendar/core@4.3.1/locales-all.min.js'></script>
<meta name="api-token" content="{{ Auth::user()->api_token }}">

<div class="row">
    <div class="col-12">
        <div id="calendar"></div>
    </div>
</div>
<script>
       $(document).ready(function() {
        var calendarEl = document.getElementById('calendar');
        var token = $('meta[name="api-token"]').attr('content'); // Get token from meta tag

            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['interaction', 'dayGrid', 'timeGrid'],
                initialView: 'timeGridWeek',
                editable: true,
                locale: 'fr',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'  // Buttons for month, week, and day views
                },
                firstHour : 8, // Set start hour to 8:00 AM
                slotMaxTime: '18:00:00', // Set end hour to 6:00 PM

                buttonText: {
                    prev: 'Précédent',
                    next: 'Suivant',
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour'
                },
                weekText: 'Sem.',
                allDayText: 'Toute la journée',
                moreLinkText: 'en plus',
                noEventsText: 'Aucun événement à afficher',
                events: [] // Initialize with an empty array
            });

            calendar.render();

            function fetchAndRenderEvents(userId) {
                $.ajax({
                    url: '/api/rdvs',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token // Include bearer token
                    },
                    data: { user_id: userId },
                    success: function(data) {
                        console.log(data)
                        var events = data.map(function(rdv) {
                            var startDate = new Date(rdv.date_rdv);
                            var endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // Add 1 hour to the start date

                            return {
                                title: rdv.nom + ' ' + rdv.prenom,
                                start: rdv.date_rdv,
                                end: endDate.toISOString(),
                                description: 'Address: ' + rdv.adresse + ', ' + rdv.ville
                            };
                        });
                        console.log(events)
                        calendar.removeAllEvents(); // Clear existing events
                        calendar.addEventSource(events); // Add new events
                        calendar.refetchEvents(); // Refetch the events to render them
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching RDVs:', error);
                    }
                });
            }

            // Initial fetch
            fetchAndRenderEvents($('#form_config_user_id').val());

            // Fetch and render events when the dropdown value changes
            $('#form_config_user_id').change(function() {
                var selectedUserId = $(this).val();
                fetchAndRenderEvents(selectedUserId);
            });
        });

</script>
