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

<div class="row">
    <div class="col-6">
        <div id="calendar"></div>
    </div>
</div>
<script>
         $(document).ready(function() {
            var calendarEl = document.getElementById('calendar');

            $.ajax({
                url: '/api/rdvs',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer YOUR_ACCESS_TOKEN' // Replace with your actual access token
                },
                success: function(data) {
                    // Transform the data to fit FullCalendar's event structure
                    var events = data.map(function(rdv) {
                        return {
                            title: rdv.title, // Adjust according to your API response
                            start: rdv.start, // Adjust according to your API response
                            end: rdv.end,     // Adjust according to your API response
                            url: rdv.url      // Adjust according to your API response if you have URLs
                        };
                    });

                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        plugins: [ 'interaction', 'dayGrid', 'timeGrid' ], // Added timeGrid plugin for week and day views
                        defaultView: 'timeGridWeek',  // Set default view to week
                        editable: true,
                        locale: 'fr',  // Set locale to French
                        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'  // Buttons for month, week, and day views
        },
                        week: {
                            dow: 1, // Monday is the first day of the week.
                            doy: 4  // The week that contains Jan 4th is the first week of the year.
                        },
                        buttonText: {
                            prev: 'Précédent',
                            next: 'Suivant',
                            today: "Aujourd'hui",
                            year: 'Année',
                            month: 'Mois',
                            week: 'Semaine',
                            day: 'Jour',
                            list: 'Planning'
                        },
                        weekText: 'Sem.',
                        allDayText: 'Toute la journée',
                        moreLinkText: 'en plus',
                        noEventsText: 'Aucun événement à afficher',

                        events: events // Use the transformed events array
                    });

                    calendar.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching RDVs:', error);
                    $('#rdvs-list').append('<p>Error fetching RDVs.</p>');
                }
            });
        });
</script>
