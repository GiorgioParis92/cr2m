<?php 
namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Dossier;
use App\Models\Clients;
use Illuminate\Http\Request;

class Stats extends Component
{
    public $statistics;
    public $delays;
    public $averageDelays;
    public $auditDelays;
    public $auditDelays2;
    public $selectedClient = null;
    public $clients;
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->clients = Clients::get();
        $this->startDate = null;
        $this->endDate = null;
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $query = DB::table('dossiers')
            ->join('dossiers_data', 'dossiers.id', '=', 'dossiers_data.dossier_id')
            ->where('dossiers_data.meta_key', 'step_2')
            ->select(
                DB::raw('DATE(dossiers.created_at) as creation_date'),
                DB::raw('AVG(TIMESTAMPDIFF(DAY, dossiers.created_at, dossiers_data.meta_value)) as average_delay')
            );

        if ($this->selectedClient) {
            $query->where('dossiers.installateur', $this->selectedClient);
        }

        if ($this->startDate) {
            $query->whereDate('dossiers.created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('dossiers.created_at', '<=', $this->endDate);
        } else {
            $query->whereDate('dossiers.created_at', '<=', Carbon::now()->toDateString());
        }

        $this->averageDelays = $query->groupBy('creation_date')
            ->orderBy('creation_date')
            ->havingRaw('COUNT(dossiers.id) > 0')
            ->get();

        // Load audit delays
        $auditQuery = Dossier::select(
                DB::raw('DATE(dossiers.created_at) as creation_date'),
                DB::raw('AVG(TIMESTAMPDIFF(DAY, dossiers.created_at, IFNULL(forms_data.created_at, CURDATE()))) as average_delay')
            )
            ->join('forms_data', function ($join) {
                $join->on('dossiers.id', '=', 'forms_data.dossier_id')
                     ->where('forms_data.meta_key', '=', 'audit');
            })
            ->where('status_id', '!=', 15)
            ->where('annulation','!=',1)

            ->where('etape_number', '>=', function ($query) {
                $query->selectRaw('MIN(order_column)')
                      ->from('etapes')
                      ->where('etape_name', '>=', 'audit');
            });

        if ($this->selectedClient) {
            $auditQuery->where('dossiers.installateur', $this->selectedClient);
        }

        if ($this->startDate) {
            $auditQuery->whereDate('dossiers.created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $auditQuery->whereDate('dossiers.created_at', '<=', $this->endDate);
        } else {
            $auditQuery->whereDate('dossiers.created_at', '<=', Carbon::now()->toDateString());
        }

        $this->auditDelays = $auditQuery->groupBy('creation_date')
            ->orderBy('creation_date')
            ->havingRaw('COUNT(dossiers.id) > 0')
            ->get();



            $auditQuery2 = Dossier::select(
                DB::raw('DATE(dossiers.created_at) as creation_date'),
                DB::raw('AVG(TIMESTAMPDIFF(DAY, dossiers_data.created_at, IFNULL(forms_data.created_at, CURDATE()))) as average_delay')
            )
            ->join('dossiers_data', function ($join) {
                $join->on('dossiers.id', '=', 'dossiers_data.dossier_id')
                     ->where('dossiers_data.meta_key', '=', 'date_1ere_visite');
            })
            ->join('forms_data', function ($join) {
                $join->on('dossiers.id', '=', 'forms_data.dossier_id')
                     ->where('forms_data.meta_key', '=', 'audit');
            })
            ->where('status_id', '!=', 15)
            ->where('annulation','!=',1)

            ->where('etape_number', '>=', function ($query) {
                $query->selectRaw('MIN(order_column)')
                      ->from('etapes')
                      ->where('etape_name', '>=', 'audit');
            });

        if ($this->selectedClient) {
            $auditQuery2->where('dossiers.installateur', $this->selectedClient);
        }

        if ($this->startDate) {
            $auditQuery2->whereDate('dossiers.created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $auditQuery2->whereDate('dossiers.created_at', '<=', $this->endDate);
        } else {
            $auditQuery2->whereDate('dossiers.created_at', '<=', Carbon::now()->toDateString());
        }

        $this->auditDelays2 = $auditQuery2->groupBy('creation_date')
            ->orderBy('creation_date')
            ->havingRaw('COUNT(dossiers.id) > 0')
            ->get();
    }

    public function updatedSelectedClient()
    {
        $this->loadStatistics();
        $this->dispatchBrowserEvent('refresh-chart', ['averageDelays' => $this->averageDelays, 'auditDelays' => $this->auditDelays,'auditDelays2'=>$this->auditDelays2]);
    }

    public function updatedStartDate()
    {
        $this->loadStatistics();
        $this->dispatchBrowserEvent('refresh-chart', ['averageDelays' => $this->averageDelays, 'auditDelays' => $this->auditDelays,'auditDelays2'=>$this->auditDelays2]);

    }

    public function updatedEndDate()
    {
        $this->loadStatistics();
        $this->dispatchBrowserEvent('refresh-chart', ['averageDelays' => $this->averageDelays, 'auditDelays' => $this->auditDelays,'auditDelays2'=>$this->auditDelays2]);

    }

    public function render()
    {
        // return view('livewire.stats');
        return view('livewire.stats');
    }





}
