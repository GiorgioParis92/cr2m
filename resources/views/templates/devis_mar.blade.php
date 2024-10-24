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
        text-align: center !important
    }

    td .text-center p {
        text-align: center !important
    }

    table .text-right {
        text-align: right !important
    }

    td .text-right {
        text-align: right !important
    }

    td .text-right p {
        text-align: right !important
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

    .text-right {
        text-align: right
    }
</style>

<page backtop="25mm" backleft="10mm" backright="10mm" backimg="" backbottom="10mm">
    @include('pdf.header')
    @include('pdf.footer')
    <div class="my-4"
        style="margin-top:20px;margin-left:350px;border:1px solid #ccc;border-radius:5px;width:280px;padding:12px">
        <p>
            <b>{{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }}</b><br />
            {{ $dossier->beneficiaire->numero_voie }} {{ $dossier->beneficiaire->adresse }} <br />
            {{ $dossier->beneficiaire->cp }} {{ $dossier->beneficiaire->ville }}<br />
            Tél. : {{ $dossier->beneficiaire->telephone }} @if (!empty($dossier->beneficiaire->telephone_2))
                / {{ $dossier->beneficiaire->telephone_2 }}
            @endif <br />
            Courriel : {{ $dossier->beneficiaire->email }}


        </p>



    </div>
    <div class="my-4" style="margin-top:10px;margin-left:350px;width:380px;">

        <p>
            <b>Devis n° {{ $all_data['dossiers_data'][0]['numero_devis'] ?  $all_data['dossiers_data'][0]['numero_devis'] : ''}}
            </b><br />
            Date : {{ $all_data['dossiers_data'][0]['date_devis_mar'] ?  date('d/m/Y',strtotime($all_data['dossiers_data'][0]['date_devis_mar'])) : ''}}


        </p>

    </div>

    <table>
        <tr>
            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">Libellé</b></p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">Montant H.T.</b></p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">TVA</b></p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">Montant TTC</b></p>
            </td>
        </tr>


        <tr>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-center">
                Prestation Mon Accompagnateur Rénov’ 1ère
                Visite et Audit Énergétique Réglementaire
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                1 500,00€
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                300,00€
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                <b>1 800,00€</b>
            </td>
        </tr>
        <tr>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-center">
                Prestation Mon Accompagnateur Rénov’ 2ème
                Visite obligatoire
            </td>
            <td width="25%" style="width:25%;min-width:25%;text-align:right" class=" border-bottom text-right">
                166,67€
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                33,33€
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                <b>200,00€</b>
            </td>
        </tr>
        <tr>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom background_td text-right">
                <b>Total</b>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom background_td text-right">
                <b>1 666,67€</b>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom background_td text-right">
                <b>333,33€€</b>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom background_td text-right">
                <b>2 000,00€</b>
            </td>
        </tr>

        <tr>
            <td width="25%" colspan="4" style="width:25%;min-width:25%;border:none" class="text-center">

            </td>


        </tr>

        <tr>
            <td width="25%" colspan="4" style="width:25%;min-width:25%;border-left:none;border-right:none"
                class="text-center border-bottom">

            </td>


        </tr>

        <tr>
            <td width="25%" colspan="3" style="width:25%;min-width:25%;border-top: 1px solid #3333335d!important;"
                class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">Aide prévisionnelle de l’ANAH</b></p>
            </td>

            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">TTC</b></p>
            </td>
        </tr>


        <tr>
            <td width="25%" colspan="3" style="width:25%;min-width:25%" class=" border-bottom text-center">
                <p style="width:600px"> <b>Financement par l’ANAH de l’Accompagnateur Rénov’ selon le plafond
                        de ressources</b></p>
            </td>


            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">

            </td>
        </tr>

        @php
            $array = [
                'bleu' => ['Très modestes', '2000'],
                'jaune' => ['Modestes', '1600'],
                'violet' => ['Intermédiaires', '1200'],
                'rose' => ['Supérieurs', '800'],
            ];
        @endphp

        @foreach ($array as $key => $element)
            <tr>
                <td width="25%" colspan="3" style="width:25%;min-width:25%" class=" border-bottom text-right">
                    {{ $element[0] }} ({{ $key }})
                </td>


                <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                    @if ($dossier->beneficiaire->menage_mpr == $key)
                        - {{ $array[$dossier->beneficiaire->menage_mpr][1] }} €
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td width="25%" colspan="3" style="width:25%;min-width:25%"
                class="background_td border-bottom text-right">
                <b style="font-size:13px;">Reste à charge à payer</b>
            </td>

            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-right">
                <b>{{ 2000 - $array[$dossier->beneficiaire->menage_mpr][1] }} €</b>
            </td>
        </tr>

        <tr>
            <td width="25%" colspan="4" style="width:25%;min-width:25%" class="background_td border-bottom">
                En votre aimable règlement sur le compte <b>{{ $dossier->client->client_title }}</b><br /><br />
                {{ $dossier->client->bank ?? '' }}
            </td>


        </tr>


    </table>



    <div class="my-4" style="margin-top:10px;">


        <p>
            Validité du devis : 3 mois à compter de la date d’émission<br />
            Date de début de la prestation : à compter de la date de signature du contrat<br />
            Date de fin de la prestation : 2 mois à compter de la date de fin des travaux.<br />
            Conditions de règlement : payable 8 jours après la date de signature du devis.<br />
        </p>

    </div>


    <div class="my-4" style="margin-top:10px;margin-left:380px">


        <p>
            Le Bénéficiaire<br />
            @if(isset(($all_data['form_data'][13]['signature_beneficiaire'])) && file_exists(storage_path('app/public/' . json_decode($all_data['form_data'][13]['signature_beneficiaire'])[0])))

                    <img style="max-width:150px;margin-top:-10px" src="{{ asset('storage/' . json_decode($all_data['form_data'][13]['signature_beneficiaire'])[0]) }}" alt="Logo">
        
                    @endif
            {{-- Signature précédé de la mention manuscrite<br />
            <i>"Bon pour accord et exécution du devis »</i> --}}
        </p>

    </div>

</page>


@if($dossier->beneficiaire->menage_mpr!='bleu')
<page backtop="25mm" backleft="10mm" backright="10mm" backimg="" backbottom="10mm">
    @include('pdf.header')
    @include('pdf.footer')
    <div class="my-4"
        style="margin-top:20px;margin-left:350px;border:0px solid #ccc;border-radius:5px;width:280px;padding:12px">
        <p>
            <b>FATURE PRO-FORMA</b><br />
            <b>Acquitée</b>


        </p>



    </div>

    <div class="my-4"
        style="margin-top:20px;margin-left:350px;border:1px solid #ccc;border-radius:5px;width:280px;padding:12px">
        <p>
            <b>{{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }}</b><br />
            {{ $dossier->beneficiaire->adresse }} <br />
            {{ $dossier->beneficiaire->cp }} {{ $dossier->beneficiaire->ville }}<br />
            Tél. : {{ $dossier->beneficiaire->telephone }} @if (!empty($dossier->beneficiaire->telephone_2))
                / {{ $dossier->beneficiaire->telephone_2 }}
            @endif <br />
            Courriel : {{ $dossier->beneficiaire->email }}


        </p>



    </div>
    <div class="my-4" style="margin-top:10px;margin-left:350px;width:380px;">

        <p>
            <b>Devis n° {{ $all_data['dossiers_data'][0]['numero_devis'] ?  $all_data['dossiers_data'][0]['numero_devis'] : ''}}
            </b><br />
            Date : {{ $all_data['dossiers_data'][0]['date_devis_mar'] ?  date('d/m/Y',strtotime($all_data['dossiers_data'][0]['date_devis_mar'])) : ''}}


        </p>

    </div>
    <table>
        <tr>
            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">Prestation</b></p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">Montant H.T.</b></p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">TVA</b></p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">Montant TTC</b></p>
            </td>
        </tr>

        @php
            $array = [
                'bleu' => ['Très modestes', '2000'],
                'jaune' => ['Modestes', '1600'],
                'violet' => ['Intermédiaires', '1200'],
                'rose' => ['Supérieurs', '800'],
            ];
        @endphp
        <tr>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-center">
                <p><b style="font-size:13px;">Reste à charge sur le devis de Mon Accompagnateur Rénov'</b></p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                <p style="text-align: right">
               {{ number_format(((2000 - $array[$dossier->beneficiaire->menage_mpr][1])/1.2),2) }} €
                </p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                <p style="text-align: right">
                   {{ number_format(((2000 - $array[$dossier->beneficiaire->menage_mpr][1])/1.2*0.2),2) }} €

                </p>
            </td>
            <td width="25%" style="width:25%;min-width:25%" class=" border-bottom text-right">
                <p style="text-align: right">
                   {{ number_format(((2000 - $array[$dossier->beneficiaire->menage_mpr][1])),2) }} €
                </p>
            </td>
        </tr>
     


    </table>
</page>

@endif