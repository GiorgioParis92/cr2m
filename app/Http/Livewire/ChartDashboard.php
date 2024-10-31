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
        $parameters = is_string($chart->parameters) ? json_decode($chart->parameters, true) : $chart->parameters;
    
        if (!is_array($parameters)) {
            throw new \Exception("The 'parameters' field is not a valid JSON format.");
        }
    
        // Determine the model dynamically
        $modelClass = '\\App\\Models\\' . $chart->model;
        $query = $modelClass::query();
    
        // Apply relationships if specified
        if (isset($parameters['with'])) {
            $query->with($parameters['with']);
        }
    
        // Apply select fields and handle aggregate functions with DB::raw
        if (isset($parameters['select'])) {
            $selectFields = array_map(function ($field) {
                return str_contains($field, 'COUNT(') ? DB::raw($field) : $field;
            }, $parameters['select']);
            $query->select($selectFields);
        }
    
        // Apply where conditions
        if (isset($parameters['where'])) {
            foreach ($parameters['where'] as $condition) {
                $column = $condition['column'];
                $operator = $condition['operator'];
                $value = $condition['value'];
    
                if (is_array($value) && $value[0] === 'currentMonthStart') {
                    $start = now()->startOfMonth()->toDateString();
                    $end = now()->endOfMonth()->toDateString();
                    $query->whereBetween($column, [$start, $end]);
                } elseif (is_array($value) && $value[0] === 'currentWeekStart') {
                    $start = now()->startOfWeek()->toDateString();
                    $end = now()->endOfWeek()->toDateString();
                    $query->whereBetween($column, [$start, $end]);
                } else {
                    $query->where($column, $operator, $value);
                }
            }
        }
    
        // Apply additional conditions (e.g., whereHas, orWhereHas, etc.)
        if (isset($parameters['additional'])) {
            foreach ($parameters['additional'] as $additional) {
                $type = $additional['type'];
                $relation = $additional['relation'] ?? null;
                $conditions = $additional['conditions'] ?? [];
    
                if ($type === 'whereHas' && $relation) {
                    $query->whereHas($relation, function ($q) use ($conditions) {
                        foreach ($conditions as $condition) {
                            $q->where($condition['column'], $condition['operator'], $condition['value']);
                        }
                    });
                } elseif ($type === 'orWhereHas' && $relation) {
                    $query->orWhereHas($relation, function ($q) use ($conditions) {
                        foreach ($conditions as $condition) {
                            $q->where($condition['column'], $condition['operator'], $condition['value']);
                        }
                    });
                } elseif ($type === 'whereDoesntHave' && $relation) {
                    $query->whereDoesntHave($relation, function ($q) use ($conditions) {
                        foreach ($conditions as $condition) {
                            $q->where($condition['column'], $condition['operator'], $condition['value']);
                        }
                    });
                } elseif ($type === 'when') {
                    if (auth()->check() && auth()->user()->client_id > 0 && auth()->user()->client->type_client == 3) {
                        foreach ($conditions as $condition) {
                            $query->where($condition['column'], $condition['operator'], auth()->user()->client_id);
                        }
                    }
                }
            }
        }
    
        // Apply group by clause if specified
        if (isset($parameters['groupBy'])) {
            $query->groupBy($parameters['groupBy']);
        }
    
        // Return the results
        return $query->get();
    }
    
    
    
    
    
    

    public function render()
    {
        return view('livewire.chart-dashboard', [
            'clients' => $this->clients
        ]);
    }
}
