
<div>
    <div class="row">
        <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4">
            <div class="mb-3">
                <label for="clientFilter">Filter by Client:</label>
                <select wire:model="selectedClient" id="clientFilter" class="form-control no_select2">
                    <option value="">All Clients</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->client_title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4">
            <div class="card mb-4">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="">
                            <div class="">
                                <div class="card-header pb-0 p-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-2">
                                            Délai moyen de prise de RDV
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <canvas id="averageDelayChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4">
            <div class="card mb-4">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="">
                            <div class="">
                                <div class="card-header pb-0 p-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-2">
                                            Délai moyen d'audit
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <canvas id="auditDelayChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:load', function () {
        var averageCtx = document.getElementById('averageDelayChart').getContext('2d');
        var auditCtx = document.getElementById('auditDelayChart').getContext('2d');
        var averageDelays = @json($averageDelays);
        var auditDelays = @json($auditDelays);

        function updateChartData(newAverageDelays, newAuditDelays) {
            averageDelays = newAverageDelays;
            auditDelays = newAuditDelays;
            console.log(averageDelays);
            console.log(auditDelays);

            // Format date as French format (DD/MM/YYYY) and delay with 2 decimals
            var averageLabels = averageDelays.map(function(item) {
                var date = new Date(item.creation_date);
                var day = date.getDate();
                // return (day === 1 || day === 15) ? date.toLocaleDateString('fr-FR') : '';
                return date.toLocaleDateString('fr-FR');
            });

            var averageData = averageDelays.map(function(item) {
                return parseFloat(item.average_delay).toFixed(2); // 2 decimal places
            });

            var auditLabels = auditDelays.map(function(item) {
                var date = new Date(item.creation_date);
                var day = date.getDate();
                return (day === 1 || day === 15) ? date.toLocaleDateString('fr-FR') : '';
            });

            var auditData = auditDelays.map(function(item) {
                return parseFloat(item.average_delay).toFixed(2); // 2 decimal places
            });

            averageChart.data.labels = averageLabels;
            averageChart.data.datasets[0].data = averageData;
            averageChart.update();

            auditChart.data.labels = auditLabels;
            auditChart.data.datasets[0].data = auditData;
            auditChart.update();
        }

        var averageChart = new Chart(averageCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Average Delay in Days',
                    data: [],
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
                            text: 'Creation Date'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Average Delay (Days)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        var auditChart = new Chart(auditCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Average Audit Delay in Days',
                    data: [],
                    borderColor: 'rgba(255, 99, 132, 1)',
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
                            text: 'Creation Date'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Average Audit Delay (Days)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        window.addEventListener('refresh-chart', function (event) {
            updateChartData(event.detail.averageDelays, event.detail.auditDelays);
        });

        updateChartData(averageDelays, auditDelays);
    });
</script>
