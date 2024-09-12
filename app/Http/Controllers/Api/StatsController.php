<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Client;
use App\Models\User;
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

            return response()->json($request->tabai);
        }

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();


        $rdvsForMonth = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$currentMonthStart, $currentMonthEnd])
            ->groupBy('dossier_id')
            ->first();
        $stats['rdvsForMonth'] = $rdvsForMonth->rdv_count ?? 0;




        $rdvsForWeek = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$currentWeekStart, $currentWeekEnd])
            ->groupBy('dossier_id')
            ->first();
        $stats['rdvsForWeek'] = $rdvsForWeek->rdv_count ?? 0;


        $dossiersForMonth = DB::table('dossiers')
        ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
        ->count();
        $stats['dossiersForMonth'] = $dossiersForMonth ?? 0;

        $dossiersForWeek = DB::table('dossiers')
        ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
        ->count();
        $stats['dossiersForWeek'] = $dossiersForWeek ?? 0;


        return response()->json($stats);
    }
}
