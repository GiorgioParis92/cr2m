<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
    }

    .header,
    .footer {
        color: #333;
        text-align: center;
        font-size: 10px;
        width: 100%;
        position: absolute;
    }

    .header {
        border-bottom: 1px solid #ccc;
        padding-bottom: 3mm;
    }

    .footer {
        border-top: 1px solid #ccc;
        padding-top: 3mm;
    }

    .page-number::after {
        content: counter(page);
    }

    .container {
        width: 80%;
        margin: auto;
        margin-left: -60px;
    }

    .items-table {
        width: 100%;
        margin-top: 25px;
        border-collapse: collapse;
    }

    .items-table th,
    .items-table td {
        border: 2px solid white;
        padding: 5px;
        text-align: center;
    }

    .items-table th {
        background-color: #336dea;
        color: white;
        font-weight: bold;
    }

    .items-table td {
        background: #eaf7ff;
        color: rgb(108, 108, 108);
    }

    .items-table td.description {
        text-align: left;
    }

    .horizontal_container {
        position: relative;
        left: 150px;
    }

    .footer {
        background: #eaf7ff;
        padding: 5px;
        color: black;
        text-align: center;
        max-width: 95%;
        margin: auto;
    }

    .footer_block {
        max-width: 80%;
        color: black;
    }

    .observations {
        font-size: 9px;
        font-style: italic;
    }

    h1 {
        text-align: center;
        font-size: 20px;
    }

    page {
        padding: 10mm;
    }

    p {
        text-align: justify;
        font-size: 11px;
        line-height: 15px;
    }

    .my-4 {
        width: 95%;
    }

    .my-4 h3 {
        margin-top: 20px;
        margin-bottom: -10px;
        font-size: 13px;
        color: rgb(0, 118, 214)
    }

    .my-4 h4 {
        margin-top: 20px;
        margin-bottom: -10px;
        font-size: 12px;
        color: rgb(0, 118, 214)
    }
    h2 {
        margin-top: 20px;
        margin-bottom: -10px;
        font-size: 14px;
    }
    .section {
        text-align: justify;
        line-height: 16px;
    }

    .section-title {
        font-weight: bold;
        font-size: 13px;
        text-transform: uppercase;
    }

    ul {
        font-size: 11px;

    }

    li {
        font-size: 11px;

    }

    table {
        border-collapse: collapse;
        max-width: 80%;
        min-width: 80%;
        width: 80%;
        margin: auto;
        margin-top: 30px;

    }

    table .background_td {
        background: rgb(190, 190, 190);
    }

    table .border-bottom {
        /* border-bottom:1px solid #3333335d; */
    }

    table .text-center {
        text-align: center
    }

    td .text-center p {
        text-align: center !important
    }

    table td {
        font-size: 11px;
        max-width: 200px;
        padding: 8px;
        border-collapse: collapse;
        border-left: 1px solid #3333335d;
        border-right: 1px solid #3333335d;
        border-top: 1px solid #3333335d;
        border-bottom: 1px solid #3333335d;
        width: 100%;
    }

    table td ul {
        max-width: 200px;
    }
</style>


<page backtop="1mm" backleft="10mm" backright="10mm" backimg="" backbottom="10mm">

    @include('pdf.footer')


    <h1 class="text-center">Dossier juridique – Mandataire financier et administratif Anah</h1>


    <h2>Convention Tripartite</h2>

    <div class="my-4">

        <p>

            Entre les soussignés :<br/>
            1. ENERGIA EQC, société par actions simplifiée, au capital de 50000€, immatriculée au RCS de Nanterre sous
            le numéro 840 971 972, dont le siège social est situé 34 AVENUE EDOUARD VAILLANT, 92100
            BOULOGNE-BILLANCOURT, représentée par Jean-René POILLOT, en qualité de mandataire administratif et financier
            (ci-après dénommée « le Mandataire »),<br/><br/>

            2. {{ $dossier->installateur_client->client_title}}, entreprise immatriculée au RCS de {{ $dossier->installateur_client->rcs}} sous le numéro {{ $dossier->installateur_client->siren}},
            dont le siège est situé [adresse], représentée par [nom], (ci-après « l’Artisan »),<br/><br/>

            3. {{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }},  domicilié(e) {{ $dossier->beneficiaire->numero_voie }} {{ $dossier->beneficiaire->adresse }} {{ $dossier->beneficiaire->cp }}
            {{ $dossier->beneficiaire->ville }}, (ci-après « le Bénéficiaire »),<br/><br/>

            Il a été convenu ce qui suit :<br/>


        </p>
    </div>


    <div class="my-4">
        <h3>Article 1: Convention Tripartite</h3>
        <p>
            Objet : La présente convention a pour objet de fixer les conditions d’intervention du Mandataire dans le
            cadre de la gestion financière et administrative d’un dossier de subvention porté par le Bénéficiaire, et de
            la relation entre les Parties.

        </p>
    </div>
    <div class="my-4">
        <h3>Article 2: Financement</h3>


        <p>
            Le Mandataire s’engage à avancer les fonds liés à la subvention de l’Anah, sous réserve d’un engagement
            écrit du Bénéficiaire à régler le reste à charge.
        </p>

    </div>
    <div class="my-4">
        <h3>Article 3: Engagements de l'Artisan</h3>


        <p>
            L’Artisan s’engage à ne pas débuter les travaux sans confirmation écrite du Mandataire.
        </p>

    </div>

    <div class="my-4">
        <h3>Article 4: Engagements du bénéficiaire</h3>


        <p>
            Le Bénéficiaire reconnaît devoir un reste à charge d’un montant de {{$all_data['reste_a_charge'] ?? ''}} € et s’engage à le régler directement à
            l’Artisan selon les modalités fixées.
        </p>

    </div>
    <div class="my-4">
        <h3>Article 5: Responsabilités</h3>


        <p>
            Le Mandataire ne peut être tenu responsable d’un défaut d’exécution des travaux ou d’un refus de subvention
            émanant de l’Anah ou de la DDT.
        </p>

    </div>

    <div class="my-4">
        <h3>Article 5 Bis: Références juridiques</h3>


        <p>
            Conformément aux articles 1984 et suivants du Code civil relatifs au contrat de mandat, le Mandataire agit
            dans l’intérêt du Bénéficiaire pour l’obtention des aides publiques. La présente convention formalise un
            mandat d’assistance administrative et financière, excluant toute qualification de prêt au sens des articles
            1905 et suivants du Code civil.
        </p>

    </div>

    <div class="my-4">
        <h3>Article 5 Ter: Clause de remboursement à première demande</h3>


        <p>
            En cas de non-versement total ou partiel de la subvention par l’Anah, pour quelque raison que ce soit
            (irrégularité du dossier, refus administratif, rejet de justificatifs, etc.), ou en cas d'impayé du reste à
            charge par le Bénéficiaire, ce dernier s’engage expressément à rembourser au Mandataire, sur simple demande
            écrite de celui-ci, l’intégralité des sommes avancées.
            Ce remboursement devra intervenir dans un délai de quinze (15) jours à compter de la réception de ladite
            demande, sans qu’aucun recours, suspension ou contestation ne puisse être invoqué par le Bénéficiaire. Cette
            clause est stipulée comme essentielle et déterminante du présent contrat.

        </p>

    </div>

    </page>
<page backtop="1mm" backleft="10mm" backright="10mm" backimg="" backbottom="10mm">

    @include('pdf.footer')

    <div class="my-4">
        <h3>Article 5 Quater: Exclusion de responsabilité du Mandataire </h3>


        <p>
            Le Mandataire n’est en aucun cas responsable de la bonne exécution des travaux, des éventuels litiges
            après-vente (SAV), ni des défauts ou vices liés aux prestations réalisées par l’Artisan.
            En cas de non-réalisation, malfaçon, ou litige technique, le Bénéficiaire devra exclusivement se retourner
            contre l’Artisan, sans recours contre le Mandataire.
            De même, le Mandataire ne saurait être tenu responsable de l’annulation, du rejet ou de la non-instruction
            du dossier de demande de subvention par l’Anah ou tout autre organisme financeur, quelle qu’en soit la cause
            (non-éligibilité, erreur de pièce, dépassement de délai, etc.). Le Bénéficiaire fait son affaire personnelle
            de toutes conséquences de cette annulation.

        </p>

    </div>

    <div class="my-4">
        <h3>Article 6: Durée et résiliation </h3>


        <p>
            La présente convention prend effet à compter de sa signature et est valable jusqu’à liquidation de la
            subvention ou remboursement total du Mandataire.
        </p>

    </div>








    <h2>Attestation de reconnaissance du reste à charge</h2>

    <div class="my-4">

        <p>

        Je soussigné(e), {{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }}, demeurant à {{ $dossier->beneficiaire->numero_voie }} {{ $dossier->beneficiaire->adresse }} {{ $dossier->beneficiaire->cp }}
        {{ $dossier->beneficiaire->ville }}, certifie reconnaître devoir un montant de {{ $all_data['reste_a_charge'] ?? ''}} € au titre du reste à charge des travaux subventionnés dans le cadre du programme Anah.
Je m’engage à régler cette somme à l’artisan {{ $dossier->installateur_client->client_title}} dans les conditions prévues dans la convention signée.
Fait pour valoir ce que de droit,
<br/><br/><br/><br/><br/>



        </p>
    </div>

    <h2>Note juridique – Risques liés au préfinancement</h2>

    <div class="my-4">

        <p>

        <b>Objet : Évaluation des risques juridiques et financiers du préfinancement par un mandataire Anah</b><br/><br/>

Le mandataire qui finance les opérations avant versement effectif des aides s'expose à plusieurs risques :<br/>
1. Risque de non-recouvrement du reste à charge en cas de défaut de l’artisan ou d’un client défaillant.<br/>
2. Risque juridique de requalification de l'avance en prêt déguisé, notamment si aucun mandat express n’est formalisé.<br/>
3. Risque de refus partiel ou total de la subvention par l’Anah après instruction, entraînant une perte financière.<br/>
4. Risque fiscal si le flux financier est mal qualifié (revenue taxable, prêt illégal, etc.).<br/>
5. Risque de responsabilité civile en cas de litige avec l’artisan ou de mauvaise exécution des travaux.<br/><br/>

Recommandations :<br/>
- Toujours faire signer une convention tripartite + attestation de reconnaissance de dette.<br/>
- Formaliser les flux dans une logique de mandat et non de prêt.<br/>
- Prévoir une garantie ou assurance pour couvrir les risques de non-versement de subvention.<br/>
- Prévoir des clauses de désolidarisation technique dans tous les actes signés.<br/><br/>

Document rédigé à titre confidentiel et interne.<br/>




        </p>
    </div>




    <div class="my-4">

        <p><b>Contrat pour la réalisation d'une mission de Mon Accompagnateur Rénov'</b></p>
        <p> Fait, en deux exemplaires originaux le
            @if(isset($all_data['dossiers_data']['date_1ere_visite']))
                {{ date('d/m/Y', strtotime(str_replace('/', '-', $all_data['dossiers_data']['date_1ere_visite']))) }}
            @else
                {{ date('d/m/Y', strtotime('now')) }}
            @endif, à {{ $dossier->beneficiaire->ville }}
        </p>
    </div>
    <div>

        <table style="width:100%;margin-top:20px">
            <tr>
                <td style="border:none;width:30%">
                    <p><b>Le Bénéficiaire</b></p>
                    {{-- <p>Signature précédé de la mention manuscrite <br /><i>"Bon pour accord et exécution du devis»
                        </i>
                    </p> --}}
                    @if(isset(($all_data['form_data'][13]['signature_beneficiaire'])) && file_exists(storage_path('app/public/' . json_decode($all_data['form_data'][13]['signature_beneficiaire'])[0])))

                        <img style="max-width:150px;margin-top:-10px"
                            src="{{ asset('storage/' . json_decode($all_data['form_data'][13]['signature_beneficiaire'])[0]) }}"
                            alt="Logo">

                    @endif
                </td>
                <td style="border:none;width:30%;padding-left:45px;">
                    <!-- <p><b>Mon Accompagnateur Rénov'</b> <br />{{ $dossier->mar_client->client_title }} -->


                        <!-- @if(isset($all_data['form_data'][3]['agence']))
                            <br />
                            {{ isset($all_data['form_data'][3]['agence']) ? 'Agence ' . $all_data['form_data'][3]['agence'] : '' }}<br />{{ $all_data['form_data'][3]['agence_adresse'] ?? '' }}
                            {{ $all_data['form_data'][3]['agence_cp'] ?? '' }}
                            {{ $all_data['form_data'][3]['agence_ville'] ?? '' }}
                            <br />
                        @endif -->
                    <!-- </p> -->
                    <p><b>L'Artisan</b></p>
                    <!-- @if(isset($dossier->mar_client->signature) && file_exists(storage_path('app/public/' . $dossier->mar_client->signature)))

                        <img style="max-width:150px;margin-top:-10px"
                            src="{{ asset('storage/' . $dossier->mar_client->signature) }}" alt="Logo">

                    @endif -->


                </td>

                <td style="border:none;width:30%;padding-left:45px;">
                    <!-- <p><b>Mon Accompagnateur Rénov'</b> <br />{{ $dossier->mar_client->client_title }} -->


                        <!-- @if(isset($all_data['form_data'][3]['agence']))
                            <br />
                            {{ isset($all_data['form_data'][3]['agence']) ? 'Agence ' . $all_data['form_data'][3]['agence'] : '' }}<br />{{ $all_data['form_data'][3]['agence_adresse'] ?? '' }}
                            {{ $all_data['form_data'][3]['agence_cp'] ?? '' }}
                            {{ $all_data['form_data'][3]['agence_ville'] ?? '' }}
                            <br />
                        @endif -->
                    <!-- </p> -->
                    <p><b>Le mandataire financier</b></p>
                    @if(isset($dossier->mandataire_financier_client->signature) && file_exists(storage_path('app/public/' . $dossier->mandataire_financier_client->signature)))

                        <img style="max-width:150px;margin-top:-10px"
                            src="{{ asset('storage/' . $dossier->mandataire_financier_client->signature) }}" alt="Logo">

                    @endif


                </td>

            </tr>
        </table>

    </div>
</page>
