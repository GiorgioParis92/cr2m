<?php
namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Etape;
use App\Models\Client;
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
            ->leftJoin('forms_data as DD1', function ($join) {
                $join->on('DD1.dossier_id', '=', 'dossiers.id')
                     ->where('DD1.meta_key', '=', 'date_depot');
            })

            // Left join with dossiers_data, for meta_key = date_octroi
            ->leftJoin('forms_data as DD2', function ($join) {
                $join->on('DD2.dossier_id', '=', 'dossiers.id')
                     ->where('DD2.meta_key', '=', 'date_octroi');
            })

            // Left join with dossiers_data, for meta_key = subvention
            ->leftJoin('dossiers_data as DD3', function ($join) {
                $join->on('DD3.dossier_id', '=', 'dossiers.id')
                     ->where('DD3.meta_key', '=', 'subvention');
            })

            // Left join with dossiers_data, for meta_key = date_paiement_anah
            ->leftJoin('forms_data as DD4', function ($join) {
                $join->on('DD4.dossier_id', '=', 'dossiers.id')
                     ->where('DD4.meta_key', '=', 'date_paiement_anah');
            })
            ->leftJoin('clients as C', function ($join) {
                $join->on('C.id', '=', 'dossiers.installateur')
                     ->where('C.id', '>', '1');
            })
            ->leftJoin('clients as C2', function ($join) {
                $join->on('C2.id', '=', 'dossiers.mandataire_financier')
                     ->where('C2.id', '>', '1');
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
                'C.client_title as client_title',
                'C2.client_title as mandataire_financier',
            ])
            ->where(function ($query) {
                $query->where('annulation', 0)
                      ->orWhereNull('annulation');
            })
            ->get();

    // Extract unique, sorted etape names without HTML
    $etapeNames = Etape::orderBy('order_column')->get();
    $liste_clients = Client::where('type_client',3)->orderBy('client_title')->get();
    $liste_mandataire = Client::where('type_client',2)->orderBy('client_title')->get();

    return view('financier', compact('financier', 'etapeNames','liste_clients','liste_mandataire'));
        }
}
