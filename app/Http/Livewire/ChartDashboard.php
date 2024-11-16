<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ChartConfig;
use App\Models\Clients;
use App\Services\ChartDataService; // Import the service class

class ChartDashboard extends Component
{
    public $selectedClient = null;
    public $clients;
    public $startDate;
    public $endDate;
    public $charts = [];

    public function mount()
    {
        $this->clients = Clients::get();
     
        $this->loadStatistics();
    }
    
    

    public function loadStatistics()
    {
        // Fetch all chart configurations from the database
        $chartConfigs = ChartConfig::orderBy('ordering')->get();

        $this->charts = [];

        // Instantiate the ChartDataService with current filters
        $chartDataService = new ChartDataService($this->selectedClient, $this->startDate, $this->endDate);

        foreach ($chartConfigs as $config) {
            // For each chart, call the data method specified in the config
            $dataMethod = $config->data_method;

            // Check if the method exists in the ChartDataService
            if (method_exists($chartDataService, $dataMethod)) {
                $data = $chartDataService->$dataMethod();
                $total=0;
              
                foreach($data as $item) {
                   $total=$total+$item->total;
                }
                // Build the chart array
                $this->charts[] = [
                    'id' => $config->chart_id,
                    'title' => $config->title,
                    'label' => $config->label,
                    'data' => $data,
                    'total' => $total,
                   
                    'type' => $config->type ?? 'line',
                    'borderColor' => $config->border_color,
                ];
            } else {
                // Log a warning if the method doesn't exist
                \Log::warning("Data method {$dataMethod} does not exist in ChartDataService.");
            }
        }
    }

    public function dateUpdated($inputId, $selectedDate)
    {
        if ($inputId === 'startDate') {
            $this->startDate = $selectedDate;
        } elseif ($inputId === 'endDate') {
            $this->endDate = $selectedDate;
        }

        $this->loadStatistics();
        $this->dispatchBrowserEvent('refresh-charts', ['charts' => $this->charts]);
    }
    public function updatedSelectedClient()
    {
        $this->loadStatistics();
        $this->dispatchBrowserEvent('refresh-charts', ['charts' => $this->charts]);
    }

    public function updatedStartDate()
    {
        
        $this->loadStatistics();
        $this->dispatchBrowserEvent('refresh-charts', ['charts' => $this->charts]);
    }

    public function updatedEndDate()
    {
        $this->loadStatistics();
        $this->dispatchBrowserEvent('refresh-charts', ['charts' => $this->charts]);
    }
  
    public function render()
    {
        return view('livewire.chart-dashboard', [
            'clients' => $this->clients,
            'charts' => $this->charts,
        ]);
    }
}
