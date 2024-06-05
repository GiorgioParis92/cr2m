
<script src="{{ asset('frontend/assets/js/fullcalendar/packages/core/main.js') }}"></script>
<script src="{{ asset('frontend/assets/js/fullcalendar/packages/daygrid/main.js') }}"></script>
<script src="{{ asset('frontend/assets/js/fullcalendar/packages/interaction/main.js') }}"></script>
<link rel="stylesheet" href="{{ asset('frontend/assets/js/fullcalendar/packages/core/main.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/assets/js/fullcalendar/packages/daygrid/main.css') }}">
<div class="row">
    <div class="col-6">
        <div id="calendar"></div>
    </div>
</div>
<script>


    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['interaction','dayGrid'],
        defaultDate: '2020-02-12',
        editable: true,

        code: 'fr',
    week: {
      dow: 1, // Monday is the first day of the week.
      doy: 4, // The week that contains Jan 4th is the first week of the year.
    },
    buttonText: {
      prev: 'Précédent',
      next: 'Suivant',
      today: "Aujourd'hui",
      year: 'Année',
      month: 'Mois',
      week: 'Semaine',
      day: 'Jour',
      list: 'Planning',
    },
    weekText: 'Sem.',
    allDayText: 'Toute la journée',
    moreLinkText: 'en plus',
    noEventsText: 'Aucun événement à afficher',
  
        events: [{
                title: 'All Day Event',
                start: '2020-02-01'
            },
            {
                title: 'Long Event',
                start: '2020-02-07',
                end: '2020-02-10'
            },
            {
                groupId: 999,
                title: 'Repeating Event',
                start: '2020-02-09T16:00:00'
            },
            {
                groupId: 999,
                title: 'Repeating Event',
                start: '2020-02-16T16:00:00'
            },
            {
                title: 'Conference',
                start: '2020-02-11',
                end: '2020-02-13'
            },
            {
                title: 'Meeting',
                start: '2020-02-12T10:30:00',
                end: '2020-02-12T12:30:00'
            },
            {
                title: 'Lunch',
                start: '2020-02-12T12:00:00'
            },
            {
                title: 'Meeting',
                start: '2020-02-12T14:30:00'
            },
            {
                title: 'Happy Hour',
                start: '2020-02-12T17:30:00'
            },
            {
                title: 'Dinner',
                start: '2020-02-12T20:00:00'
            },
            {
                title: 'Birthday Party',
                start: '2020-02-13T07:00:00'
            },
            {
                title: 'Click for Google',
                url: 'http://google.com/',
                start: '2020-02-28'
            }
        ]
    });
    
    calendar.render();
    </script>
