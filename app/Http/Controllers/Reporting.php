<?php 
namespace App\Http\Controllers;

use App\Models\DossiersData;
use App\Models\FormsData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;



class Reporting extends Controller
{
public function getCombinedMetaKeyStats(array $metaKeys): Collection
{
    $now = Carbon::now();
    $startOfCurrentMonth = $now->copy()->startOfMonth();
    $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
    $endOfLastMonth = $startOfCurrentMonth->copy()->subSecond();

    $startOfCurrentWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
    $startOfLastWeek = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
    $endOfLastWeek = $startOfCurrentWeek->copy()->subSecond();
    $startOfCurrentYear = $now->copy()->startOfYear(); 

    $results = [];

    foreach ($metaKeys as $key=>$value) {
        $results[] = [
            'meta_key'      => $value,
            'all_time'      => $this->countDossiersAcrossTables($key),
            'past_month'    => $this->countDossiersAcrossTables($key, $startOfLastMonth, $endOfLastMonth),
            'current_month' => $this->countDossiersAcrossTables($key, $startOfCurrentMonth),
            'past_week'     => $this->countDossiersAcrossTables($key, $startOfLastWeek, $endOfLastWeek),
            'current_week'  => $this->countDossiersAcrossTables($key, $startOfCurrentWeek),
            'current_year'    => $this->countDossiersAcrossTables($key, $startOfCurrentYear),

        ];
    }

    return collect($results);
}

private function countDossiersAcrossTables(string $key, Carbon $from = null, Carbon $to = null): int
{
    $query1 = DossiersData::query()->where('meta_key', $key);
    $query2 = FormsData::query()->where('meta_key', $key);

    if ($from && $to) {
        $query1->whereBetween('created_at', [$from, $to]);
        $query2->whereBetween('created_at', [$from, $to]);
    } elseif ($from) {
        $query1->where('created_at', '>=', $from);
        $query2->where('created_at', '>=', $from);
    }

    $dossierIds1 = $query1->pluck('dossier_id')->unique();
    $dossierIds2 = $query2->pluck('dossier_id')->unique();

    return $dossierIds1->merge($dossierIds2)->unique()->count();
}


public function showMetaKeyStats(Request $request)
{
    $metaKeys = ["step_1"=>'Etape 1',"audit"=>'Audits déposés',"date_1ere_visite"=>'RDV MAR 1']; // or decode from request

    $stats = $this->getCombinedMetaKeyStats($metaKeys); // from the previous helper

    return view('reporting', ['stats' => $stats]);
}


}