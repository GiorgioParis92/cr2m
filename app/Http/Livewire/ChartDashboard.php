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
        // $this->updateCharts();
    }
    public function updatedSelectedClient()
    {
        $this->updateCharts();
    }
    
    public function updatedStartDate()
    {
        $this->updateCharts();
    }
    
    public function updatedEndDate()
    {
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
        $rawSql = $chart->request_sql;
        $user = auth()->user();
        $clientType = $user->client->type_client ?? null;
        $clientId = $user->client_id;
        $hasChildClients = [];
    
        if ($clientType == 4) {
            $hasChildClients = ClientLinks::where('client_parent', $clientId)->pluck('client_id')->toArray();
        }
    
        try {
            $bindings = [];
    
            // Build additional conditions
            $additionalConditions = '';
    
            if ($clientType == 1) {
                $additionalConditions = " AND (dossiers.client_id = ? OR dossiers.mar = ?)";
                $bindings[] = $clientId;
                $bindings[] = $clientId;
            } elseif ($clientType == 2) {
                $additionalConditions = " AND dossiers.mandataire_financier = ?";
                $bindings[] = $clientId;
            } elseif ($clientType == 3) {
                $additionalConditions = " AND dossiers.installateur = ?";
                $bindings[] = $clientId;
            } elseif ($clientType == 4) {
                $childClients = implode(',', array_fill(0, count($hasChildClients), '?'));
                $bindings = array_merge($bindings, [$clientId], $hasChildClients);
                $additionalConditions = " AND (dossiers.installateur = ? OR dossiers.installateur IN ($childClients))";
            }
    
            // Append the conditions to the raw SQL query
            $modifiedSql = $rawSql . $additionalConditions;
    
            // Execute the modified SQL query with bindings
            $result = DB::select(DB::raw($modifiedSql), $bindings);
    
            return $result;
        } catch (\Exception $e) {
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
