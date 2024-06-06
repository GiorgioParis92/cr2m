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
    <div class="col-6">
        <div id="calendar"></div>
    </div>
</div>
<script>
       $(document).ready(function() {
    var calendarEl = document.getElementById('calendar');
    var token = $('meta[name="api-token"]').attr('content'); // Get token from meta tag
        console.log(token)
    $.ajax({
        url: '/api/rdvs',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token // Include bearer token
        },
        success: function(data) {
console.log(data)
var events = data.map(function(rdv) {
    var startDate = new Date(rdv.date_rdv);
                        var endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // Add 1 hour to the start date

                return {
                    title: rdv.nom + ' ' + rdv.prenom, // Example: using the name as the title
                    start: rdv.date_rdv, // Use date_rdv as the start date
                    end: rdv.date_rdv,   // Assuming same end date for simplicity
                    url: '#',            // You can adjust this if you have a URL field
                };
            });
            console.log(events)
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['interaction', 'dayGrid', 'timeGrid'], // Added timeGrid plugin for week and day views
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
    $('.fc-timeGridWeek-button').click()
    calendar.render();
});
</script>
