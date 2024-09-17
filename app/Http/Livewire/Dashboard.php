<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\DossiersActivity;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
class Dashboard extends Component
{


    public function mount()
    {
       $this->refresh();
    }
    public function refresh()
    {
        $stats = [];


        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $rdvsForMonth = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$currentMonthStart, $currentMonthEnd])
            ->first();
        $stats['rdvsForMonth'] = $rdvsForMonth->rdv_count ?? 0;

        $rdvsLastMonth = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$lastMonthStart, $lastMonthEnd])
            ->first();
        $stats['rdvsLastMonth'] = $rdvsLastMonth->rdv_count ?? 0;


        $rdvsForWeek = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$currentWeekStart, $currentWeekEnd])
            ->first();
        $stats['rdvsForWeek'] = $rdvsForWeek->rdv_count ?? 0;

        $rdvsLastWeek = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$lastWeekStart, $lastWeekEnd])
            ->first();
        $stats['rdvsLastWeek'] = $rdvsLastWeek->rdv_count ?? 0;

        $dossiersForMonth = DB::table('dossiers')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $stats['dossiersForMonth'] = $dossiersForMonth ?? 0;

        $dossiersLastMonth = DB::table('dossiers')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
        $stats['dossiersLastMonth'] = $dossiersLastMonth ?? 0;

        $dossiersForWeek = DB::table('dossiers')
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->count();
        $stats['dossiersForWeek'] = $dossiersForWeek ?? 0;

        $dossiersLastWeek = DB::table('dossiers')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->count();
        $stats['dossiersLastWeek'] = $dossiersLastWeek ?? 0;
        $this->stats = $stats;


        $secondsAgo = Carbon::now()->subSeconds(60);
        $this->activities = DossiersActivity::where('updated_at', '>=', $secondsAgo)
        ->with(['dossier','dossier.beneficiaire', 'user', 'form']) // Eager load relationships
        ->latest()                          // Get latest updated records
        ->get();



        $completionDataByForm = DossiersActivity::select(
            'dossiers_activities.form_id',
            'forms.form_title as form_name',
            DB::raw('AVG(CAST(score AS DECIMAL(5,2))) as avg_completion_rate')
        )
        ->join('forms', 'dossiers_activities.form_id', '=', 'forms.id') // Join with users table
        ->whereNotNull('score')
        ->groupBy('dossiers_activities.user_id', 'forms.id') // Group by user_id and user_name
        ->get();
        

        $this->data_byform = $completionDataByForm->map(function ($data) {
            return [
       
                'form_id' => $data->form_id,
                'form_name' => $data->form_name,
                'avg_completion_rate' => $data->avg_completion_rate
            ];
        });

        $completionDataByUser = DossiersActivity::select(
            'dossiers_activities.user_id',
            'users.name as user_name',
            DB::raw('AVG(CAST(score AS DECIMAL(5,2))) as avg_completion_rate')
        )
        ->join('users', 'dossiers_activities.user_id', '=', 'users.id') // Join with users table
        ->whereNotNull('score')
        ->groupBy('dossiers_activities.user_id', 'users.name') // Group by user_id and user_name
        ->get();
        
        $this->data_byuser = $completionDataByUser->map(function ($data) {
            return [
                'user_id'            => $data->user_id,
                'user_name'          => $data->user_name,
                'avg_completion_rate' => $data->avg_completion_rate
            ];
        });
// dd($this->chartData);
    }
    public function render()
    {
        return view('livewire.dashboard');
    }
}
