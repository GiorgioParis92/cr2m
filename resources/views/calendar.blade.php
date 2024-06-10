<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-token" content="{{ Auth::user()->api_token }}"> <!-- Replace with your actual token -->
    <title>FullCalendar Example</title>
    <!-- FullCalendar CSS -->
   

</head>
<body>
    <div class="row">
        <div class="form-group">
            <select class="form-control" id="form_config_user_id">
                <option value="">Choisir un auditeur / Voir tous les auditeurs</option>
                @foreach($auditeurs as $auditeur)
                <option value="{{$auditeur->id}}">{{$auditeur->name}}</option>

                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div id="calendar"></div>
        </div>
    </div>
    <!-- Initialize Calendar -->
    <script>
    
    
//       $(document).ready(function() {
//         var calendarEl = document.getElementById('calendar');
//         var token = $('meta[name="api-token"]').attr('content'); // Get token from meta tag

      
//         var calendar = new FullCalendar.Calendar(calendarEl, {
//             // plugins: [ 'interaction', 'dayGrid', 'timeGrid' ], // Ensure all required plugins are listed here
//             initialView: 'timeGridWeek', // Set default view to timeGridWeek
//             editable: true,
//             locale: 'fr',
//             headerToolbar: {  // Use headerToolbar for header configuration
//                 start: 'title', // will normally be on the left. if RTL, will be on the right
//   center: '',
//   end: 'today prev,next timeGrid timeGridWeek dayGridMonth' // will normally be on the right. if RTL, will be on the left
//             },
//             slotMinTime: "08:00:00", // Set start hour to 8:00 AM
//     slotMaxTime: "20:00:00", // Set end hour to 8:00 PM
//     slotDuration: '01:00:00', // Set slot duration to 1 hour

//             buttonText: {
//                 prev: 'Précédent',
//                 next: 'Suivant',
//                 today: "Aujourd'hui",
//                 month: 'Mois',
//                 timeGrid: 'Journée',
//                 week: 'Semaine',
//                 day: 'Jour'
//             },
//             weekText: 'Sem.',
//             allDayText: 'Toute la journée',
//             moreLinkText: 'en plus',
//             noEventsText: 'Aucun événement à afficher',
//             events: [] // Initialize with an empty array
//         });

//         calendar.render();

//         function fetchAndRenderEvents(userId) {
//             $.ajax({
//                 url: '/api/rdvs',
//                 type: 'GET',
//                 headers: {
//                     'Authorization': 'Bearer ' + token // Include bearer token
//                 },
//                 data: { user_id: userId },
//                 success: function(data) {
//                     console.log(data);
//                     var events = data.map(function(rdv) {
//                         var startDate = new Date(rdv.date_rdv);
//                         var endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // Add 1 hour to the start date

//                         return {
//                             title: rdv.nom + ' ' + rdv.prenom,
//                             start: rdv.date_rdv,
//                             end: endDate.toISOString(),
//                             description: 'Address: ' + rdv.adresse + ', ' + rdv.ville
//                         };
//                     });
//                     console.log(events);
//                     calendar.removeAllEvents(); // Clear existing events
//                     calendar.addEventSource(events); // Add new events
//                     calendar.refetchEvents(); // Refetch the events to render them
//                 },
//                 error: function(xhr, status, error) {
//                     console.error('Error fetching RDVs:', error);
//                 }
//             });
//         }

//         // Initial fetch
//         fetchAndRenderEvents($('#form_config_user_id').val());

//         // Fetch and render events when the dropdown value changes
//         $('#form_config_user_id').change(function() {
//             var selectedUserId = $(this).val();
//             fetchAndRenderEvents(selectedUserId);
//         });
//       });
    </script>
</body>
</html>
