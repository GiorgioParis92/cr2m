<div class="d-flex justify-content-center mb-3">
    <div class="me-2">
        <label for="startDate" onchange="updateTitle()" onblur="updateTitle()" class="form-label">Start Date:</label>
        <input type="date" id="startDate" wire:model.lazy="startDate" class="form-control">
    </div>
    <div class="me-2">
        <label for="endDate" onchange="updateTitle()" onblur="updateTitle()" class="form-label">End Date:</label>
        <input type="date" id="endDate" wire:model.lazy="endDate" class="form-control">
    </div>
    <div class="me-2">
        <label class="form-label">From Step:</label>
        <select wire:model="selectedSteps.0" id="selectedSteps0" onchange="updateTitle()" class="form-control no_select2">
            @foreach($etapes as $etape)
                <option value="step_{{ $etape->id }}">{{ $etape->etape_icon.' - '.$etape->etape_desc}}</option>
            @endforeach
        </select>
    </div>
    <div class="me-2">
        <label class="form-label">To Step:</label>
        <select wire:model="selectedSteps.1" id="selectedSteps1" onchange="updateTitle()" class="form-control no_select2">
            @foreach($etapes as $etape)
                <option value="step_{{ $etape->id }}">{{ $etape->etape_icon.' - '.$etape->etape_desc}}</option>
            @endforeach
        </select>
    </div>
</div>

<input
    id="titleInput"
    type="text"
    placeholder="Type a new chart title..."
    style="margin-bottom: 10px; opacity:0"
/>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" id="btnExportImage">Export as Image</button>
</div>

<div class="row">
    <div class="col-md-12">
        <canvas id="myChart" style="max-height: 50vh;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>

<script>
    // Make chart data and global average accessible to updateTitle()
    const daysData = @json($chartData['data']); 
    let globalAverage = 0;

    /**
     * Recomputes the global average in case daysData changes dynamically.
     */
    function computeGlobalAverage() {
        if (!daysData || daysData.length === 0) {
            return 0;
        }
        const sum = daysData.reduce((acc, val) => acc + parseFloat(val), 0);
        return sum / daysData.length;
    }

    /**
     * Updates the chart title input to include date range, steps, and the global average.
     */
    function updateTitle() {
        const startDate = new Date(document.getElementById('startDate').value);
        const endDate = new Date(document.getElementById('endDate').value);

        const startDateFormatted = startDate.toLocaleDateString('fr-FR');
        const endDateFormatted = endDate.toLocaleDateString('fr-FR');

        const fromStepText = document.querySelector('#selectedSteps0 option:checked')?.textContent || '';
        const toStepText = document.querySelector('#selectedSteps1 option:checked')?.textContent || '';

        // Ensure Livewire picks up any changes (if needed):
        document.getElementById('startDate').focus();
        document.getElementById('startDate').blur();
        document.getElementById('endDate').focus();
        document.getElementById('endDate').blur();

        // Recompute the global average in case data changed
        globalAverage = computeGlobalAverage();

        // Construct the new title
        const newTitle = 
            `Délai moyen de jours entre les étapes ${fromStepText} et ${toStepText}` +
            ` pour les dossiers créés entre le ${startDateFormatted} et le ${endDateFormatted}` +
            ` (Moyenne globale: ${globalAverage.toFixed(2)} jours)`;

        // Update the hidden input value
        const titleInput = document.getElementById('titleInput');
        titleInput.value = newTitle;

        // Trigger chart title update by focusing & blurring (if needed),
        // or simply change the chart title in the chart instance below if you prefer
        titleInput.focus();
        titleInput.blur();
    }

    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById("myChart").getContext("2d");

        // Calculate initial global average
        globalAverage = computeGlobalAverage();

        // Initialize the chart
        const chart = new Chart(ctx, {
            type: "line",
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: "Moyenne de jours entre les étapes sélectionnées",
                    data: daysData,
                    backgroundColor: "rgba(54, 162, 235, 0.5)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: "" // will be set via input
                    },
                    annotation: {
                        annotations: {
                            line1: {
                                type: 'line',
                                yMin: globalAverage,
                                yMax: globalAverage,
                                borderColor: 'rgb(255, 99, 132)',
                                borderWidth: 2,
                                label: {
                                    enabled: true,
                                    content: `Moyenne globale: ${globalAverage.toFixed(2)} jours`,
                                    position: 'end'
                                }
                            }
                        }
                    }
                },
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Export as image
        document.getElementById('btnExportImage').addEventListener('click', () => {
            const base64Image = chart.toBase64Image();
            const link = document.createElement('a');
            link.download = 'chart_export.png';
            link.href = base64Image;
            link.click();
        });

        // Sync the Chart title with the input
        const titleInput = document.getElementById('titleInput');
        const syncChartTitle = () => {
            chart.options.plugins.title.text = titleInput.value || 'Untitled';
            chart.update();
        };
        titleInput.addEventListener('change', syncChartTitle);
        titleInput.addEventListener('blur', syncChartTitle);

        // Livewire event to update the chart data and re-draw
        Livewire.on('chartUpdated', (updatedChartData) => {
            // Update global array
            daysData.length = 0; // clear existing array
            daysData.push(...updatedChartData.data); // fill with new values

            // Recalculate global average
            globalAverage = computeGlobalAverage();

            // Update the annotation line
            chart.options.plugins.annotation.annotations.line1.yMin = globalAverage;
            chart.options.plugins.annotation.annotations.line1.yMax = globalAverage;
            chart.options.plugins.annotation.annotations.line1.label.content =
                `Moyenne globale: ${globalAverage.toFixed(2)} jours`;

            // Update labels & dataset
            chart.data.labels = updatedChartData.labels;
            chart.data.datasets[0].data = updatedChartData.data;
            chart.update();

            // Optionally re-trigger the title logic
            updateTitle();
        });

        // Initialize the title once on page load
        updateTitle();
    });
</script>
