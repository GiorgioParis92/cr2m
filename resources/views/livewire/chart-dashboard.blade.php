<div>
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-12">
            <label for="clientFilter">Filtrer par Client:</label>
            <select wire:model="selectedClient" id="clientFilter" class="form-control no_select2">
                <option value="">Tous les clients</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->client_title }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-xl-4 col-sm-12">
            <label for="startDate">Date de début:</label>
            <input type="date" wire:model="startDate" id="startDate" class="form-control">
        </div>

        <div class="col-xl-4 col-sm-12">
            <label for="endDate">Date de fin:</label>
            <input type="date" wire:model="endDate" id="endDate" class="form-control">
        </div>
    </div>

    <div class="row">
        @foreach ($charts as $chart)
            <div class="col-xl-4 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2">{{ $chart['title'] }}</h6>
                        <canvas id="chart_{{ $chart['id'] }}"></canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            initCharts(@json($charts));

            Livewire.hook('message.processed', (message, component) => {
                initCharts(@json($charts));
            });
        });

        let chartInstances = {};

        function initCharts(chartsData) {
            // Destroy existing charts to prevent duplicates
            for (const chartId in chartInstances) {
                chartInstances[chartId].destroy();
            }
            chartInstances = {};

            chartsData.forEach(chart => {
                const chartData = chart.data;
                console.log(chartData)
                const labels = chartData.map(item => {
                    const date = new Date(item.creation_date);
                    return date.toLocaleDateString('fr-FR');
                });

                const data = chartData.map(item => parseFloat(item.average_delay).toFixed(2));

                const ctx = document.getElementById('chart_' + chart.id).getContext('2d');
                chartInstances[chart.id] = new Chart(ctx, {
                    type: chart.type,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: chart.title,
                            data: data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Date de création'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Délai moyen en jours'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        }
    </script>
    @endpush
</div>
<div>
    <h5>Debugging Chart Configuration:</h5>
    <ul>
        @foreach ($charts as $chart)
            <li>Chart ID: {{ $chart['id'] }} - Title: {{ $chart['title'] }} - Type: {{ $chart['type'] }}</li>

            @php print_r($chart) @endphp
        @endforeach
    </ul>
</div>