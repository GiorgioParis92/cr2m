<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Client;
use App\Models\User;
use App\Models\ChartConfig;
use Carbon\Carbon;

use Illuminate\Support\Facades\Schema; // Import Schema for checking columns

class StatsController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $stats = [];


        if($request->tabai) {

            if($request->tabai=='error') {
                return response()->json('error',401);
            }

            return response()->json('requete ok');
        }

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();


        $rdvsForMonth = DB::table('rdv')
        ->join('dossiers', 'rdv.dossier_id', '=', 'dossiers.id')
        ->select(DB::raw('COUNT(*) as rdv_count'))
        ->whereBetween('date_rdv', [$currentMonthStart, $currentMonthEnd])
        ->when(auth()->user()->client_id > 0, function ($query) {
            return $query->where('dossiers.installateur', auth()->user()->client_id);
        })
        ->groupBy('rdv.dossier_id')
        ->first();
        $stats['rdvsForMonth'] = $rdvsForMonth->rdv_count ?? 0;




   

            $rdvsForWeek = DB::table('rdv')
            ->join('dossiers', 'rdv.dossier_id', '=', 'dossiers.id')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$currentWeekStart, $currentWeekEnd])
            ->when(auth()->user()->client_id > 0, function ($query) {
                return $query->where('dossiers.installateur', auth()->user()->client_id);
            })
            ->groupBy('rdv.dossier_id')
            ->first();

        $stats['rdvsForWeek'] = $rdvsForWeek->rdv_count ?? 0;


        $dossiersForMonth = DB::table('dossiers')
        ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
        if(auth()->user()->client_id>0) {
            $dossiersForMonth =$dossiersForMonth->where('installateur', auth()->user()->client_id) ;
        }
      
        $dossiersForMonth =$dossiersForMonth->count();


        $stats['dossiersForMonth'] = $dossiersForMonth ?? 0;

        $dossiersForWeek = DB::table('dossiers')
        ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
        ->when(auth()->user()->client_id > 0, function ($query) {
            return $query->where('installateur', auth()->user()->client_id);
        })
        ->count();
        $stats['dossiersForWeek'] = $dossiersForWeek ?? 0;


        return response()->json($stats);
    }

    public function getChartData($id, Request $request)
    {
        $chartConfig = ChartConfig::findOrFail($id);
        $parameters = array_merge($chartConfig->parameters ?? [], $request->all());
    
        // Build each part of the SQL manually
        $selectColumns = implode(', ', json_decode($chartConfig->select_columns, true));
        $joins = $this->buildJoins(json_decode($chartConfig->join_tables, true));
        $conditions = $this->buildConditions(json_decode($chartConfig->conditions, true), $parameters);
    
        // Only add WHERE clause if conditions are not empty
        $whereClause = $conditions ? "WHERE {$conditions}" : "";
    
        // Assemble the final SQL query
        $sql = "SELECT {$selectColumns} FROM dossiers as d {$joins} {$whereClause}";
    
        // Execute the query as a raw statement
        $data = DB::select(DB::raw($sql));
    
        return response()->json([
            'chartType' => $chartConfig->chart_type,
            'data' => $data,
        ]);
    }
    
    
    // Helper to build JOIN clauses
    protected function buildJoins($joins)
    {
        $joinSql = '';
        foreach ($joins as $join) {
            $joinSql .= " LEFT JOIN {$join['table']} ON {$join['on']} ";
        }
        return $joinSql;
    }
    
    // Helper to build WHERE conditions and subqueries
    protected function buildConditions($conditions, $parameters)
    {
        $whereSql = '';
        $isFirstCondition = true;
        foreach ($conditions as $condition) {
            // Add 'AND' or 'OR' between conditions, but skip for the first condition
            if (!$isFirstCondition) {
                $whereSql .= " {$condition['clause']} ";
            }
    
            // Add the subquery without repeating 'EXISTS'
            if (isset($condition['subquery'])) {
                // Ensure that 'EXISTS' is only added once
                $whereSql .= " ({$condition['subquery']})";
            } else {
                $whereSql .= $condition['condition'];
            }
    
            $isFirstCondition = false;
        }
    
        // Replace placeholders in conditions with actual parameters
        foreach ($parameters as $key => $value) {
            $whereSql = str_replace(":{$key}", "'{$value}'", $whereSql);
        }
    
        return $whereSql;
    }
             
        
    
    
    
}
