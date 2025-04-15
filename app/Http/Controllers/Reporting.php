<?php

namespace App\Http\Controllers;

use App\Models\DossiersData;
use App\Models\FormsData;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Reporting extends Controller
{
    /**
     * Return stats for multiple meta_keys. If no rows exist
     * for a given meta_key under the same date range and filters,
     * count will be 0.
     */
    public function getCombinedMetaKeyStats(array $metaKeys, array $filters = []): Collection
    {
        $now = Carbon::now();

        // Prepare date boundaries
        $startOfCurrentMonth = $now->copy()->startOfMonth();
        $startOfLastMonth    = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth      = $startOfCurrentMonth->copy()->subSecond();

        $startOfCurrentWeek  = $now->copy()->startOfWeek(Carbon::MONDAY);
        $startOfLastWeek     = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
        $endOfLastWeek       = $startOfCurrentWeek->copy()->subSecond();

        $startOfCurrentYear  = $now->copy()->startOfYear();

        $results = [];

        // For each meta key, compute totals across data & forms
        foreach ($metaKeys as $actualMetaKey => $labelForDisplay) {
            $results[] = [
                // Store the label or the key as you see fit
                'meta_key'      => $labelForDisplay,

                // All Time
                'all_time'      => $this->countDossiersAcrossTables($actualMetaKey, null, null, $filters),

                // Past Month
                'past_month'    => $this->countDossiersAcrossTables($actualMetaKey, $startOfLastMonth, $endOfLastMonth, $filters),

                // Current Month
                'current_month' => $this->countDossiersAcrossTables($actualMetaKey, $startOfCurrentMonth, null, $filters),

                // Past Week
                'past_week'     => $this->countDossiersAcrossTables($actualMetaKey, $startOfLastWeek, $endOfLastWeek, $filters),

                // Current Week
                'current_week'  => $this->countDossiersAcrossTables($actualMetaKey, $startOfCurrentWeek, null, $filters),

                // Current Year
                'current_year'  => $this->countDossiersAcrossTables($actualMetaKey, $startOfCurrentYear, null, $filters),
            ];
        }

        return collect($results);
    }

    /**
     * Count how many unique dossiers across both tables match
     * the given meta_key AND the optional date ranges AND the filters.
     */
    private function countDossiersAcrossTables(string $metaKey, ?Carbon $from, ?Carbon $to, array $filters = []): int
    {
        $dossierIds1 = $this->pluckDossierIdsFromTable('dossiers_data', $metaKey, $from, $to, $filters);
        $dossierIds2 = $this->pluckDossierIdsFromTable('forms_data', $metaKey, $from, $to, $filters);
    
        // Merge, remove duplicates, then count
        return $dossierIds1->merge($dossierIds2)->unique()->count();
    }

    /**
     * Query the given table for rows matching the metaKey, date range,
     * and any allowed filters, returning only the unique dossier_id values.
     */
    private function pluckDossierIdsFromTable(
        string $table,
        string $metaKey,
        ?Carbon $from,
        ?Carbon $to,
        array $filters
    ): Collection {
        $query = DB::table($table)
            ->join('dossiers', 'dossiers.id', '=', "$table.dossier_id")
            ->where("$table.meta_key", $metaKey);

        // Date constraints
        if ($from && $to) {
            $query->whereBetween("$table.created_at", [$from, $to]);
        } elseif ($from) {
            $query->where("$table.created_at", '>=', $from);
        }

        // Additional filters (installateur, etc.)
        foreach ($filters as $column => $value) {
            $query->where("dossiers.$column", $value);
        }

        return $query->pluck("$table.dossier_id")->unique();
    }

    /**
     * Controller endpoint. Grabs known metaKeys, fetches stats,
     * applies user filters, and displays them in a view.
     */
    public function showMetaKeyStats(Request $request)
    {
        // The meta keys we want to count in our tables
        $metaKeys = [
    
            'date_1ere_visite' => 'RDV MAR 1',
            'step_23' => 'Pré-audit (Etape 5 validée)',
            'step_8' => 'Audits (Etape 6 validée)',
            'step_12' => 'Devis déposés (Etape 5.1 validée)',
            'step_11' => 'Dépôt ANAH (Etape 7.1 validée)',
            'attestation_rapprochement' => 'Attestation de rapprochement',
            'espace_conseil' => 'Informations transmises à l\'espace conseil',
            'courrier_confirmation' => 'Courrier de confirmation envoyé',
            'step_13' => 'MPR Octroyés',
            'montant_aide_locale' => 'Aides locales obtenues',
            'refus_anah' => 'MPR refusés',
            'step_15' => 'RDV MAR 2 (Etape 11 validée)',
            'rapport_cofrac'=>'Rapport d\'inspection effectué',
            'step_16'=>'Règlements déposés (étape 14 validée)',
            'date_paiement_anah'=>'Règlement obtenu',
            'date_paiement_aides'=>'Aides locales reçues',
        ];

        // Only allow filtering on columns that actually exist in 'dossiers' table
        $allowedFilters  = Schema::getColumnListing('dossiers');
        $requestFilters  = $request->only($allowedFilters);

        // Filter out null/empty values so we only apply "where" on non-empty filters
        $filters = array_filter($requestFilters, function ($value) {
            return $value !== null && $value !== '';
        });

        // Build stats (your method that uses $filters)
        $stats = $this->getCombinedMetaKeyStats($metaKeys, $filters);

        // Other data for the view
        $installateurs = Client::where('type_client', 3)->get();
        $mandataires   = Client::where('type_client', 2)->get();
        $mar           = Client::where('type_client', 1)->get();

        return view('reporting', [
            'stats'         => $stats,
            'installateurs' => $installateurs,
            'mar'           => $mar,
            'mandataires'   => $mandataires,
        ]);
    }
}
