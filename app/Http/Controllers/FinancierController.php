<?php
namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Etape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancierController extends Controller
{
    public function index(Request $request)
    {
        $financier = Dossier::query()
            // Left join with bénéficiaires
            ->leftJoin('beneficiaires as B', 'B.id', '=', 'dossiers.beneficiaire_id')

            // Left join with étapes
            ->leftJoin('etapes as E', 'E.id', '=', 'dossiers.etape_number')

            // Left join with dossiers_data, for meta_key = date_depot
            ->leftJoin('dossiers_data as DD1', function ($join) {
                $join->on('DD1.dossier_id', '=', 'dossiers.id')
                     ->where('DD1.meta_key', '=', 'date_depot');
            })

            // Left join with dossiers_data, for meta_key = date_octroi
            ->leftJoin('dossiers_data as DD2', function ($join) {
                $join->on('DD2.dossier_id', '=', 'dossiers.id')
                     ->where('DD2.meta_key', '=', 'date_octroi');
            })

            // Left join with dossiers_data, for meta_key = subvention
            ->leftJoin('dossiers_data as DD3', function ($join) {
                $join->on('DD3.dossier_id', '=', 'dossiers.id')
                     ->where('DD3.meta_key', '=', 'subvention');
            })

            // Left join with dossiers_data, for meta_key = date_paiement_anah
            ->leftJoin('dossiers_data as DD4', function ($join) {
                $join->on('DD4.dossier_id', '=', 'dossiers.id')
                     ->where('DD4.meta_key', '=', 'date_paiement_anah');
            })

            // Optionally, specify the columns you want to select.
            // If you need them all, you can keep * but be mindful of collisions.
            ->select([
                'dossiers.*',
                'B.nom as beneficiaire_nom', // Example for beneficiary name
                'B.prenom as beneficiaire_prenom', // Example for beneficiary name
                'E.etape_name as etape_name',  // Example for etape name
                'E.etape_desc as etape_desc',  // Example for etape name
                'E.etape_icon as etape_icon',  // Example for etape name
                'E.order_column as order_column',  // Example for etape name
                'DD1.meta_value as date_depot',
                'DD2.meta_value as date_octroi',
                'DD3.meta_value as subvention',
                'DD4.meta_value as date_paiement_anah',
            ])
            ->where(function ($query) {
                $query->where('annulation', 0)
                      ->orWhereNull('annulation');
            })
            ->get();

    // Extract unique, sorted etape names without HTML
    $etapeNames = Etape::orderBy('order_column')->get();

    return view('financier', compact('financier', 'etapeNames'));
        }
}
