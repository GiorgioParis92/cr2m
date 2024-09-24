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
    <div class="row">
        <div class="col-12" style="max-width:40%;margin:auto;text-align:center;margin-top:-30px">

            @if (isset($dossier->client->main_logo) && file_exists(storage_path('app/public/' . $dossier->client->main_logo)))
                <img src="{{ asset('storage/' . $dossier->client->main_logo) }}" alt="Logo">
            @endif
        </div>
    </div>

    <h1 class="text-center">Attestation de 1ère visite</h1>

    <div class="my-4">
        <p>Madame,Monsieur,</p>
        <p>
            À la suite de notre visite du {{ date('d/m/Y', strtotime('now')) }} à votre domicile, nous résumons les
            principaux points sur lesquels nous avons échangé
        </p>

        <h3>Les missions de Mon Accompagnateur Rénov (MAR)</h3>
        <h4>1ère visite</h4>
        <ul>
            <li>Information du ménage sur les aides disponibles et le déroulé de l'accompagnement</li>
            <li>Signature du contrat d'accompagnement MAR </li>
            <li>Relevé d'informations pour un diagnostic de situation initiale</li>
            <li>Évaluation simplifiée du niveau de dégradation et d'insalubrité du logement et du niveau d'autonomie du
                ménage</li>
        </ul>


        <h4>Après la 1ère visite</h4>
        <ul>

            <li>Réalisation de l'audit énergétique</li>
            <li>Évaluation simplifiée du niveau de dégradation et d'insalubrité du logement et du niveau d'autonomie du
                ménage</li>
            <li>Élaboration du projet de travaux et du plan de financement</li>
            <li>Aide pour la sélection des artisans RGE </li>
            <li>Aide au montage du dossier de demande de subventions et suivi administratif</li>
            <li>Aide au suivi de la réalisation des travaux</li>


        </ul>
        <h4>2ème visite</h4>
        <ul>

            <li>Cohérence des travaux et conseils écogestes</li>
            <li>Remise du rapport d'accompagnement et attestation de fin de prestation</li>
            <li>Solde de subvention pour travaux et accompagnement</li>
            <li>Questionnaire de satisfaction rempli par les ménages.</li>


        </ul>
    </div>

</page>
<page backtop="25mm" backleft="10mm" backright="10mm" backimg="" backbottom="10mm">
    @include('pdf.header')
    @include('pdf.footer')
    <div class="my-4">



        <h3>Les obligations de Mon Accompagnateur Rénov (MAR)</h3>
        <h4>Une indépendance et une neutralité du MAR</h4>
        <ul>

            <li>Analyse des devis de façon rigoureuse avec des critères objectifs (prix, disponibilités, réalité des
                scenarios, travaux conformes au scenario retenu).
            </li>
            <li>Préconisation de façon neutre et objective, de la solution des travaux répondant au mieux à l'efficacité
                énergétique,</li>



        </ul>

        <h4>Une réorientation vers un accompagnement social renforcé pour les ménages les plus précaires
        </h4>
        <ul>

            <li>Une obligation de signalement auprès des autorités compétentes des situations de fragilités constatées.

            </li>
            <li>Orientation vers un accompagnateur social renforcé pour les ménages qui le nécessitent.</li>



        </ul>
    </div>
    <div class="my-4">
        <p>Nous restons à votre écoute tout au long de notre accompagnement, si vous souhaitez plus de précisions. </p>
        <p>Nom du bénéficiaire : {{ $all_data['beneficiaire_data']['nom'] }} </p>
        <p>Prénom du bénéficiaire : {{ $all_data['beneficiaire_data']['prenom'] }} </p>
    </div>


    <table style="width:100%">
        <tr>
            <td style="border:none;width:50%">
                <p><b>Le bénéficiaire</b></p>
                {{-- <p>Signature précédé de la mention manuscrite <br /><i>Adresse complète du bénéficiaire </i> --}}
                
            </td>


            <td style="border:none;width:50%;padding-left:45px;">
                <p><b>Mon Accompagnateur Rénov'</b> <br />{{ $dossier->client->client_title }} </p>
                @if(isset($dossier->client->signature) && file_exists(storage_path('app/public/' . $dossier->client->signature)))

                <img style="max-width:150px;margin-top:-10px" src="{{ asset('storage/' . $dossier->client->signature) }}" alt="Logo">
    
                @endif


            </td>

        </tr>
    </table>
</page>
