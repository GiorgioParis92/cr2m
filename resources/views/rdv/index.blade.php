@extends('layouts.app')

@section('content')
    <div>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profile') }}
            </h2>
        </x-slot>

        <select onchange="handleSelectionChange(this)">
            <option value="" disabled selected>Choisir un département</option>
            @foreach ($departments as $regionId => $regionDepartments)
                <optgroup label="{{ $regionDepartments->first()->region_name }}" data-region-id="{{ $regionId }}">
                    <option value="{{ $regionId }}" data-type="region">
                        [Toute la région {{ $regionDepartments->first()->region_name }}]
                    </option>
                    @foreach ($regionDepartments as $department)
                        <option value="{{ $department->departement_id }}" data-type="department">
                            {{ $department->departement_code}} - {{ $department->departement_nom }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>

        <div class="">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('calendar')
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
    <script>
        function handleSelectionChange(select) {
            const selectedOption = select.options[select.selectedIndex];
            const value = selectedOption.value;
            const regionOrDepartment = selectedOption.getAttribute('data-type');

            if (regionOrDepartment === 'region') {
                alert('Selected Region ID: ' + value);
            } else {
                alert('Selected Department ID: ' + value);
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            var calendarEl = document.getElementById('calendar');
            var token = $('meta[name="api-token"]').attr('content'); // Get token from meta tag


            var calendar = new FullCalendar.Calendar(calendarEl, {
                // plugins: [ 'interaction', 'dayGrid', 'timeGrid' ], // Ensure all required plugins are listed here
                initialView: 'timeGridWeek', // Set default view to timeGridWeek
                editable: true,
                locale: 'fr',
                headerToolbar: { // Use headerToolbar for header configuration
                    start: 'title', // will normally be on the left. if RTL, will be on the right
                    center: '',
                    end: 'today prev,next timeGrid timeGridWeek dayGridMonth' // will normally be on the right. if RTL, will be on the left
                },
                slotMinTime: "07:00:00", // Set start hour to 8:00 AM
                slotMaxTime: "21:00:00", // Set end hour to 8:00 PM
                slotDuration: '00:15:00', // Set slot duration to 1 hour
                eventColor: '#378006',

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
                    data: {
                        user_id: userId
                    },
                    success: function(data) {
                        console.log(data);
                        var events = data.map(function(rdv) {
                            var startDate = new Date(rdv.date_rdv);
                            var endDate = new Date(startDate.getTime() + 60 * 60 *
                            1000); // Add 1 hour to the start date

                            return {
                                title: rdv.nom + ' ' + rdv.prenom,
                                start: rdv.date_rdv,
                                end: endDate.toISOString(),
                                description: rdv.adresse + ', ' + rdv.ville,
                        backgroundColor: rdv.color, // Use the color from the API
                        borderColor: rdv.color // Optional: set the border color                     
                               };
                        });
                        console.log(events);
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
@endsection
