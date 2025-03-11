<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Etapes;

class ChartComponent extends Component
{
    public string $startDate;
    public string $endDate;
    public array $chartData = [];
    public array $selectedSteps = ['step_1', 'step_2']; // Default steps
    public $etapes;

    protected $listeners = [
        'refreshChart' => 'fetchChartData',
        'updateSteps' => 'updateSelectedSteps'
    ];

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d'); // Default: last 30 days
        $this->endDate   = now()->format('Y-m-d');
        $this->etapes    = Etapes::orderBy('order_column')->get();

        $this->fetchChartData();
    }

    public function fetchChartData(): void
    {
        if (empty($this->selectedSteps[0]) || empty($this->selectedSteps[1])) {
            return;
        }

        $step1 = $this->selectedSteps[0];
        $step2 = $this->selectedSteps[1];

        $data = DB::table('dossiers_data as d1')
            ->join('dossiers_data as d2', function ($join) use ($step1, $step2) {
                $join->on('d1.dossier_id', '=', 'd2.dossier_id')
                     ->where('d1.meta_key', '=', $step1)
                     ->where('d2.meta_key', '=', $step2);
            })
            ->join('dossiers', 'dossiers.id', '=', 'd1.dossier_id')
            ->whereBetween('dossiers.created_at', [$this->startDate, $this->endDate])
            ->selectRaw('
                AVG(DATEDIFF(d2.meta_value, d1.meta_value)) AS avg_days,
                DATE(dossiers.created_at) AS step_date
            ')
            ->groupBy('step_date')
            ->orderBy('step_date')
            ->get();

        $this->chartData = [
            'labels' => $data->pluck('step_date')->toArray(),
            'data'   => $data->pluck('avg_days')->toArray(),
        ];

        $this->emit('chartUpdated', $this->chartData);
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['startDate', 'endDate', 'selectedSteps.0', 'selectedSteps.1'])) {
            $this->fetchChartData();
        }
    }

    public function updateSelectedSteps($steps): void
    {
        $this->selectedSteps = $steps;
        $this->fetchChartData();
    }

    /**
     * Export chart data as CSV file
     */
    public function exportCsv()
    {
        $filename = 'chart_data_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            "Content-Type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        // Use a callback to stream the CSV output
        $callback = function () {
            $output = fopen('php://output', 'w');
            // Write CSV headers
            fputcsv($output, ['Date', 'Average Days']);
            // Write data rows
            foreach ($this->chartData['labels'] as $index => $date) {
                fputcsv($output, [
                    $date,
                    $this->chartData['data'][$index] ?? 0
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export chart data as PDF file
     *
     * Requires "barryvdh/laravel-dompdf" or similar PDF library.
     * Run composer require barryvdh/laravel-dompdf
     */
    public function exportPdf()
    {

        dd('ok');
        $filename    = 'chart_data_' . now()->format('Ymd_His') . '.pdf';
        $chartData   = $this->chartData;  // Pass array to the view below

        // Load a Blade view that displays chart data in a simple table (see example below)
        $pdf = \PDF::loadView('pdf.chart-data', compact('chartData'));

        return $pdf->download($filename);
    }

    public function render()
    {
        return view('livewire.chart-component');
    }
}
