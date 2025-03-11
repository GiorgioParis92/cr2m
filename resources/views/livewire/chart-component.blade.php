<div class="d-flex justify-content-center mb-3">
<div class="me-2">
            <label for="startDate" onchange="update_title()" onblur="update_title()" class="form-label">Start Date:</label>
            <input type="date" id="startDate" wire:model.lazy="startDate" class="form-control">
        </div>
        <div class="me-2">
            <label for="endDate"  onchange="update_title()" onblur="update_title()" class="form-label">End Date:</label>
            <input type="date" id="endDate" wire:model.lazy="endDate" class="form-control">
        </div>
    <div class="me-2">
        <label class="form-label">From Step:</label>
        <!-- Switch to wire:model -->
        <select wire:model="selectedSteps.0"  id="selectedSteps0" onchange="update_title()" class="form-control no_select2">
            @foreach($etapes as $etape)
                <option value="step_{{ $etape->id }}">{{ $etape->etape_icon.' - '.$etape->etape_desc}}</option>
            @endforeach
        </select>
    </div>
    <div class="me-2">
        <label class="form-label">To Step:</label>
        <!-- Switch to wire:model -->
        <select wire:model="selectedSteps.1" id="selectedSteps1" onchange="update_title()" class="form-control no_select2">
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
    style="margin-bottom: 10px;opacity:0"
/>

<div class="d-flex justify-content-end mb-3">
<button class="btn btn-primary" id="btnExportImage">Export as Image</button>

    </div>
<canvas id="myChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    function update_title(){

        var startDate = new Date($('#startDate').val());
        var endDate = new Date($('#endDate').val());
    var startDateFormatted = startDate.toLocaleDateString('fr-FR');
    var endDateFormatted = endDate.toLocaleDateString('fr-FR');

    const text1 = $('#selectedSteps0 option:selected').text();
    const text2 = $('#selectedSteps1 option:selected').text();
    

    $('#startDate').focus();
        $('#startDate').blur();

        $('#endDate').focus();
        $('#endDate').blur();
    // Combine both to set the input's value
    $('#titleInput').val('Délai moyen de jours entre les étapes '+text1 + ' et ' + text2 + ' pour les dossiers créés entre le '+startDateFormatted + ' et le' + endDateFormatted);
    $('#titleInput').focus();
    $('#titleInput').blur();
        }

    document.addEventListener("DOMContentLoaded", function () {

        let ctx = document.getElementById("myChart").getContext("2d");

        let chart = new Chart(ctx, {
            type: "line",
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: "Moyenne de jours entre les étapes selectionnées",
                    data: @json($chartData['data']),
                    backgroundColor: "rgba(54, 162, 235, 0.5)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
      title: {
        display: true,
        text: ''
      }
    },
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        document.getElementById('btnExportImage').addEventListener('click', () => {
        
        // Convert chart to a Base64-encoded image stringalert()
        const base64Image = chart.toBase64Image();

        // Create a temporary anchor link to download the image
        const link = document.createElement('a');
        link.download = 'chart_export.png';  // The filename for the downloaded image
        link.href = base64Image;
        link.click(); // Programmatically trigger the download
    });


    const titleInput = document.getElementById('titleInput');
        titleInput.addEventListener('change', () => {
            // Update the chart title
          
            chart.options.plugins.title.text = titleInput.value || 'Untitled';

            // Re-render the chart to see the new title
            chart.update();
        });
        titleInput.addEventListener('blur', () => {
            // Update the chart title
          
            chart.options.plugins.title.text = titleInput.value || 'Untitled';

            // Re-render the chart to see the new title
            chart.update();
        });
        Livewire.on('chartUpdated', (chartData) => {
            chart.data.labels = chartData.labels;
            chart.data.datasets[0].data = chartData.data;
            chart.update();
        });




    });
</script>
