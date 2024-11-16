<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Dossier;
use Carbon\Carbon;

class ChartDataService
{
    private $selectedClient;
    private $startDate;
    private $endDate;

    public function __construct($selectedClient = null, $startDate = null, $endDate = null)
    {
        $this->selectedClient = $selectedClient;
        $this->startDate = date('Y-m-d 00:00:00',strtotime(str_replace('/','-',$startDate)));
        $this->endDate = $endDate;
    }

    public function calculateAverageDelays()
    {
        $query = DB::table('dossiers')
            ->join('dossiers_data', 'dossiers.id', '=', 'dossiers_data.dossier_id')
            ->where('dossiers_data.meta_key', 'step_2')
            ->select(
                DB::raw('DATE(dossiers.created_at) as creation_date'),
                DB::raw('AVG(TIMESTAMPDIFF(DAY, dossiers.created_at, dossiers_data.meta_value)) as average_delay'),
                DB::raw('COUNT(dossiers.id) as total') // Add total as COUNT of dossiers

            );

        // Apply filters
        $query = $this->applyFilters($query);

        return $query->groupBy('creation_date')
            ->orderBy('creation_date')
            ->havingRaw('COUNT(dossiers.id) > 0')
            ->get();
    }

    public function calculateAuditDelays()
    {
        $query = Dossier::select(
                DB::raw('DATE(dossiers.created_at) as creation_date'),
                DB::raw('AVG(TIMESTAMPDIFF(DAY, dossiers.created_at, IFNULL(forms_data.created_at, CURDATE()))) as average_delay'),
                DB::raw('COUNT(dossiers.id) as total') // Add total as COUNT of dossiers

            )
            ->join('forms_data', function ($join) {
                $join->on('dossiers.id', '=', 'forms_data.dossier_id')
                     ->where('forms_data.meta_key', '=', 'audit');
            })
            ->where('status_id', '!=', 15)
            ->where('etape_number', '>=', function ($query) {
                $query->selectRaw('MIN(order_column)')
                      ->from('etapes')
                      ->where('etape_name', '>=', 'audit');
            });

        // Apply filters
        $query = $this->applyFilters($query);

        return $query->groupBy('creation_date')
            ->orderBy('creation_date')
            ->havingRaw('COUNT(dossiers.id) > 0')
            ->get();
    }

    public function calculateAuditDelays2()
    {
        $query = Dossier::select(
            DB::raw('DATE(dossiers.created_at) as creation_date'),
            DB::raw('AVG(TIMESTAMPDIFF(DAY, dossiers_data.created_at, IFNULL(forms_data.created_at, CURDATE()))) as average_delay'),
            DB::raw('COUNT(dossiers.id) as total') // Add total as COUNT of dossiers

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
        ->where('etape_number', '>=', function ($query) {
            $query->selectRaw('MIN(order_column)')
                  ->from('etapes')
                  ->where('etape_name', '>=', 'audit');
        });

        // Apply filters
        $query = $this->applyFilters($query);

        return $query->groupBy('creation_date')
            ->orderBy('creation_date')
            ->havingRaw('COUNT(dossiers.id) > 0')
            ->get();
    }

    /**
     * Apply common filters to the query.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    private function applyFilters($query)
    {
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


        // $query=$query->where('dossiers.created_at','>=','2024-09-01 00:00:00');

        return $query;
    }



    public function rdv_mar_1()
    {
        $query = Dossier::select(
            DB::raw("DATE_FORMAT(rdv.date_rdv, '%Y-%m') as creation_date"),
            DB::raw('COUNT(rdv.id) as average_delay'),
            DB::raw('COUNT(rdv.id) as total') // Add total as COUNT of dossiers

        )
        ->join('rdv', 'dossiers.id', '=', 'rdv.dossier_id');
    
        // Do not apply filters
        // $query = $this->applyFilters($query);
        $query = $this->applyFilters($query);

        $results = $query->groupBy('creation_date')
            ->orderBy('creation_date')
            ->get();
    
        // Calculate the global total RDVs
        $globalTotal = $results->sum('rdv_count');
    
        // Optionally, handle cases where there are no RDVs
        if ($results->isEmpty()) {
            $globalTotal = 0;
        }
        // Return the data and the global total
        return $results;
    }
    
    public function avant_octroi()
    {
        // Retrieve dossiers where associated etape's order_column <= 8
        $dossiers = Dossier::select(
            DB::raw("DATE_FORMAT(dossiers.created_at, '%Y-%m') as creation_date"),
            DB::raw('COUNT(dossiers.id) as average_delay'),
            DB::raw('COUNT(dossiers.id) as total') // Add total as COUNT of dossiers

        )->whereHas('etape', function($query) {
            $query->where('order_column', '<', 11);
        });

        $dossiers = $this->applyFilters($dossiers);



        $dossiers = $dossiers->groupBy('creation_date')
        ->orderBy('creation_date')
        ->get();
    
        // Optionally, you can return additional information or statistics
        return $dossiers;
    }

    public function apres_octroi()
    {
        // Retrieve dossiers where associated etape's order_column >= 11 and dossiers_data has meta_key = 'subvention' with meta_value > 0
        $dossiers = Dossier::join('dossiers_data', function ($join) {
            $join->on('dossiers.id', '=', 'dossiers_data.dossier_id')
                 ->where('dossiers_data.meta_key', '=', 'subvention');
        })
        ->select(
            DB::raw("DATE_FORMAT(dossiers.created_at, '%Y-%m') as creation_date"),
            DB::raw('COUNT(dossiers.id) as average_delay'), // Count the dossiers
            DB::raw('SUM(dossiers_data.meta_value) as total') // Sum the meta_value
        );
    
    $dossiers = $this->applyFilters($dossiers);
    
    $dossiers = $dossiers->groupBy('creation_date')
                         ->orderBy('creation_date')
                         ->get();
    
        // Optionally, you can return additional information or statistics
        return $dossiers;
    }
    

}
