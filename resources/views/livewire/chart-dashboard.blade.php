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
      
            @if ($chart['type'] === 'count')
            @dump($chart)
                <!-- Display count card -->
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $chart['title'] }}</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ $chart['data'][0]->count ?? 0 }}
                                            <span class="text-danger text-sm font-weight-bolder">
                                                <!-- Placeholder for dynamic percentage or metric -->
                                                -87.50%
                                            </span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Display chart canvas for non-count types -->
                <div class="col-xl-4 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2">{{ $chart['title'] }}</h6>
                            <canvas id="chart_{{ $chart['id'] }}"></canvas>
                        </div>
                    </div>
                </div>
            @endif
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
                if (chart.type !== 'count') {
                    const chartData = chart.data;
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
                }
            });
        }
    </script>
    @endpush
</div>
