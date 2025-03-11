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
    
        $allowedModels = config('allowed_models');
    
        if (!isset($allowedModels[$chartConfig->model])) {
            abort(403, 'Unauthorized model');
        }
    
        $modelConfig = $allowedModels[$chartConfig->model];
        $modelClass = $modelConfig['model'];
        $query = $modelClass::query();
    
        // Apply Select Fields
        $selectFields = $chartConfig->parameters['select'] ?? $modelConfig['fields'];
        $query->select($selectFields);
    
        // Apply Relations
        if (!empty($chartConfig->parameters['with'])) {
            $relations = array_intersect($chartConfig->parameters['with'], array_keys($modelConfig['relations']));
            $query->with($relations);
        }
    
        // Apply Where Conditions
        if (!empty($chartConfig->parameters['where'])) {
            foreach ($chartConfig->parameters['where'] as $condition) {
                $query->where(
                    $condition['column'],
                    $condition['operator'],
                    $condition['value']
                );
            }
        }
    
        // Apply Additional Conditions (e.g., whereHas)
        if (!empty($chartConfig->parameters['additional'])) {
            foreach ($chartConfig->parameters['additional'] as $additional) {
                if ($additional['type'] === 'whereHas') {
                    $relation = $additional['relation'];
                    if (in_array($relation, array_keys($modelConfig['relations']))) {
                        $query->whereHas($relation, function ($q) use ($additional, $modelConfig) {
                            foreach ($additional['conditions'] as $condition) {
                                if (in_array($condition['column'], $modelConfig['relations'][$relation])) {
                                    $q->where(
                                        $condition['column'],
                                        $condition['operator'],
                                        $condition['value']
                                    );
                                }
                            }
                        });
                    }
                }
                // Add other condition types as needed
            }
        }
    
        // Apply Auth-based Conditions
        if (Auth::check() && Auth::user()->client_id > 0) {
            $query->where('installateur', Auth::user()->client_id);
        }
    
        $data = $query->get();
    
        // Format data for the chart
        return response()->json($data);
    }
    
   
    
    
}
