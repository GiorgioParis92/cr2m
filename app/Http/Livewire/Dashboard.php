<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\DossiersActivity;
use App\Models\Client;
use App\Models\ClientLinks;
use App\Models\User;
use Carbon\Carbon;
class Dashboard extends Component
{

    public $donutData = [];

    public function mount()
    {
        $this->donutData = $this->getDonutData();

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
            ->join('dossiers', 'rdv.dossier_id', '=', 'dossiers.id');

        $rdvsForMonth = $this->filter_dossiers($rdvsForMonth);
        $rdvsForMonth = $rdvsForMonth->first();
        $stats['rdvsForMonth'] = $rdvsForMonth->rdv_count ?? 0;

        $rdvsLastMonth = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$lastMonthStart, $lastMonthEnd])
            ->join('dossiers', 'rdv.dossier_id', '=', 'dossiers.id');

        $rdvsLastMonth = $this->filter_dossiers($rdvsLastMonth);
        $rdvsLastMonth = $rdvsLastMonth->first();
        $stats['rdvsLastMonth'] = $rdvsLastMonth->rdv_count ?? 0;


        $rdvsForWeek = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$currentWeekStart, $currentWeekEnd])
            
            ->join('dossiers', 'rdv.dossier_id', '=', 'dossiers.id');

        $rdvsForWeek = $this->filter_dossiers($rdvsForWeek);
        $rdvsForWeek = $rdvsForWeek->first();
        $stats['rdvsForWeek'] = $rdvsForWeek->rdv_count ?? 0;


        $rdvsLastWeek = DB::table('rdv')
            ->select(DB::raw('COUNT(*) as rdv_count'))
            ->whereBetween('date_rdv', [$lastWeekStart, $lastWeekEnd])
            ->join('dossiers', 'rdv.dossier_id', '=', 'dossiers.id');

        $rdvsLastWeek = $this->filter_dossiers($rdvsLastWeek);
        $rdvsLastWeek = $rdvsLastWeek->first();
        $stats['rdvsLastWeek'] = $rdvsLastWeek->rdv_count ?? 0;

        $dossiersForMonth = DB::table('dossiers')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
            
            $dossiersForMonth = $this->filter_dossiers($dossiersForMonth);

            $dossiersForMonth=$dossiersForMonth->count();
        $stats['dossiersForMonth'] = $dossiersForMonth ?? 0;

        $dossiersLastMonth = DB::table('dossiers')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd]);
            $dossiersLastMonth = $this->filter_dossiers($dossiersLastMonth);

            $dossiersLastMonth=$dossiersLastMonth->count();
        $stats['dossiersLastMonth'] = $dossiersLastMonth ?? 0;

        $dossiersForWeek = DB::table('dossiers')
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd]);
            $dossiersForWeek = $this->filter_dossiers($dossiersForWeek);

            $dossiersForWeek=$dossiersForWeek->count(); 
        $stats['dossiersForWeek'] = $dossiersForWeek ?? 0;

        $dossiersLastWeek = DB::table('dossiers')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd]);
            $dossiersLastWeek = $this->filter_dossiers($dossiersLastWeek);

            $dossiersLastWeek=$dossiersLastWeek->count(); 
        $stats['dossiersLastWeek'] = $dossiersLastWeek ?? 0;
        $this->stats = $stats;


        $secondsAgo = Carbon::now()->subSeconds(60);
        $this->activities = DossiersActivity::where('updated_at', '>=', $secondsAgo)
            ->with(['dossier', 'dossier.beneficiaire', 'user', 'form']) // Eager load relationships
            ->latest()                          // Get latest updated records
            ->get();



        $completionDataByForm = DossiersActivity::select(
            'dossiers_activities.form_id',
            'forms.form_title as form_name',
            DB::raw('AVG(CAST(score AS DECIMAL(5,2))) as avg_completion_rate')
        )
            ->join('forms', 'dossiers_activities.form_id', '=', 'forms.id') // Join with forms table
            ->whereNotNull('score')
            ->groupBy('dossiers_activities.form_id', 'forms.form_title') // Group by form_id and form_name
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
                'user_id' => $data->user_id,
                'user_name' => $data->user_name,
                'avg_completion_rate' => $data->avg_completion_rate
            ];
        });

        // dd($this->chartData);
    }
    public function render()
    {
        return view('livewire.dashboard');
    }



    public function filter_dossiers($dossiers)
    {
        $user = auth()->user();
        $client = Client::where('id', $user->client_id)->first();

        if ($user->client_id > 0 && ($client->type_client == 1)) {
            $dossiers = $dossiers->where(function ($query) use ($user) {
                $query->where('dossiers.client_id', $user->client_id)
                    ->orWhere('dossiers.mar', $user->client_id);
            });
        }

        if ($user->client_id > 0 && ($client->type_client == 2)) {
            $dossiers = $dossiers->where(function ($query) use ($user) {
                $query->where('dossiers.mandataire_financier', $user->client_id);
            });
        }
        if ($user->client_id > 0 && ($client->type_client == 3)) {
            $dossiers = $dossiers->where(function ($query) use ($user) {
                $query->where('dossiers.installateur', $user->client_id);
            });
        }
        if ($user->client_id > 0 && ($client->type_client == 4)) {
            $has_child = ClientLinks::where('client_parent', $client->id)->pluck('client_id')->toArray();

            $dossiers = $dossiers->where(function ($query) use ($user) {
                $query->where('dossiers.installateur', $user->client_id);
            });
        }


        if (auth()->user()->client_id > 0 && ($client->type_client == 4)) {
            $has_child = ClientLinks::where('client_parent', $client->id)->pluck('client_id')->toArray();


            $dossiers = $dossiers->where(function ($query) use ($user, $has_child) {
                $query->where('dossiers.installateur', $user->client_id)
                    ->orWhereIn('dossiers.installateur', $has_child);
            });
        }

        
        return $dossiers;
    }


    // Assuming this is in your Livewire component or controller
public function getDonutData()
{
 


        $data = DB::table('etapes')
        ->leftJoin('dossiers', 'dossiers.etape_number', '=', 'etapes.id')
        ->select('etapes.id', 'etapes.etape_name', 'etapes.etape_desc', 'etapes.etape_icon', 'etapes.order_column', DB::raw('COUNT(dossiers.id) as dossier_count'))
        ->groupBy('etapes.id', 'etapes.etape_name', 'etapes.etape_desc', 'etapes.etape_icon', 'etapes.order_column')
        ->orderBy('etapes.order_column');
        $data=$this->filter_dossiers($data);
        $data =$data->get();
    


    
    return $data;
}

}
