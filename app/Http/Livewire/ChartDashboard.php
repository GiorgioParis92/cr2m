<?php


namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ChartConfig;
use App\Models\Client;
use App\Models\Dossier;
use Illuminate\Support\Facades\DB;

class ChartDashboard extends Component
{
    public $charts = [];
    public $clients = [];
    public $selectedClient;
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Fetch all clients
        $this->clients = Client::all();

        // Initialize charts
        $this->updateCharts();
    }

    public function updated($propertyName)
    {
        // Update charts whenever a filter changes
        $this->updateCharts();
    }

    public function updateCharts()
    {
        $this->charts = ChartConfig::all()->map(function ($chart) {
            return [
                'id'    => $chart->id,
                'title' => $chart->title,
                'type'  => $chart->type,
                'data'  => $this->fetchChartData($chart)
            ];
        })->toArray();
    }



    private function fetchChartData($chart)
    {
        // Fetch the raw SQL query from the chart configuration
        $rawSql = $chart->request_sql;
        $user = auth()->user();
        $clientType = $user->client->type_client ?? null;
        $clientId = $user->client_id;
        $hasChildClients = [];
    
        // If the client type is 4, get child clients if needed
        if ($clientType == 4) {
            $hasChildClients = ClientLinks::where('client_parent', $clientId)->pluck('client_id')->toArray();
        }
    
        try {
            // Use raw SQL if no dynamic filtering is required
            if (!$clientType) {
                return DB::select(DB::raw($rawSql));
            }
    
            // Modify the raw SQL query by appending dynamic filtering based on client type
            $additionalConditions = '';
    
            if ($clientType == 1) {
                $additionalConditions = " AND (dossiers.client_id = $clientId OR dossiers.mar = $clientId)";
            } elseif ($clientType == 2) {
                $additionalConditions = " AND dossiers.mandataire_financier = $clientId";
            } elseif ($clientType == 3) {
                $additionalConditions = " AND dossiers.installateur = $clientId";
            } elseif ($clientType == 4) {
                $childClients = implode(',', $hasChildClients);
                $additionalConditions = " AND (dossiers.installateur = $clientId";
                if (!empty($childClients)) {
                    $additionalConditions .= " OR dossiers.installateur IN ($childClients)";
                }
                $additionalConditions .= ")";
            }
    
            // Append the conditions to the raw SQL query
            $modifiedSql = $rawSql . $additionalConditions;
    
            // Execute the modified SQL query
            $result = DB::select(DB::raw($modifiedSql));
    
            return $result;
        } catch (\Exception $e) {
            // Handle any SQL errors gracefully
            return ['error' => 'Failed to execute query: ' . $e->getMessage()];
        }
    }
    
    
    
    
    
    
    
    
    

    public function render()
    {
        return view('livewire.chart-dashboard', [
            'clients' => $this->clients
        ]);
    }
}
