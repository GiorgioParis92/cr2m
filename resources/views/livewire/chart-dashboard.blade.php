<div>
    <div class="row">
        <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4">
            <div class="mb-3">
                <label for="clientFilter">Filtrer par client</label>
                <select wire:model="selectedClient" id="clientFilter" class="form-control no_select2">
                    <option value="">All Clients</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->client_title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4">
            <div class="mb-3">
                <label for="clientFilter">Date de début</label>
                <input type="text"  id="startDate" wire:model="startDate"  class="form-control datepicker">
                 
            </div>
        </div>
      
    </div>

    <div class="row">
        @foreach($charts as $chart)
            <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4">
                <div class="card mb-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="">
                                <div class="">
                                    <div class="card-header pb-0 p-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-2">
                                                {{ $chart['title'] }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <canvas id="{{ $chart['id'] }}"></canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
        

    function initializeDatepickers() {
        // Initialize all datepickers and attach event listeners
        $('#startDate').datepicker({
            format: 'yyyy/mm/dd',
            autoclose: true
        }).on('change', function (e) {
            // Emit the updated value to Livewire
            var inputId = $(this).attr('id');
            var selectedDate = $(this).val();
            Livewire.emit('dateUpdated', inputId, selectedDate);
        });
    }


    document.addEventListener('livewire:update', function () {
        initializeDatepickers(); // Reinitialize datepickers after DOM update
    });



    document.addEventListener('livewire:load', function () {
        initializeDatepickers();
        var charts = {};

        function initializeCharts(chartsData) {
            chartsData.forEach(function(chartData) {
                var ctx = document.getElementById(chartData.id).getContext('2d');
                console.log(chartData)
                // Format date labels and data points
                var labels = chartData.data.map(function(item) {
                    var date = new Date(item.creation_date);
                    var day = date.getDate();
                    return date.toLocaleDateString('fr-FR');
                });

                var dataPoints = chartData.data.map(function(item) {
                    return parseFloat(item.average_delay).toFixed(2);
                });

                charts[chartData.id] = new Chart(ctx, {
                    type: chartData.type, // Use the type from your chart data
                    data: {
                        labels: labels,
                       
                        datasets: [{
                            label: 'T',
                            data: dataPoints,
                            borderColor: chartData.borderColor,
                          
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: chartData.total
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'TOTAL : '+chartData.total
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        }

        // Initialize charts with initial data
        var chartsData = @json($charts);
        initializeCharts(chartsData);

        window.addEventListener('refresh-charts', function (event) {
            var chartsData = event.detail.charts;
        
            chartsData.forEach(function(chartData) {
               
                var chart = charts[chartData.id];
                var total=0;
                if (chart) {
                    // Update chart data
                    var labels = chartData.data.map(function(item) {
                        var date = new Date(item.creation_date);
                        console.log(item)
                        var day = date.getDate();
                        return  date.toLocaleDateString('fr-FR');
                    });

                    var dataPoints = chartData.data.map(function(item) {
                        return parseFloat(item.average_delay).toFixed(2);
                    });
                    chart.data.labels = labels;
                    chart.data.datasets[0].data = dataPoints;
                  
                    chart.update();
                } else {
                    // Initialize chart if it doesn't exist
                    initializeCharts([chartData]);
                }
            });
        });
    });
</script>
