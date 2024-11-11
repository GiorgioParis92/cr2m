{{-- @dd($all_data) --}}
<page backtop="25mm" backleft="10mm" backright="10mm" backbottom="10mm">
    @includeIf('pdf.header')
    @includeIf('pdf.footer')

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 12px;
        }
        h1, h2 {
            color: #0047ab;
            text-align: center;
            margin: 0;
            font-weight: bold;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        h2 {
            font-size: 16px;
            margin-bottom: 20px;
        }
        h3 {
            color: #555;
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        table {
        
            width: 700px;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        .text-center {
            text-align: center;
        }
        .container {
            width: 100%;
            margin: auto;
        }
    </style>
    @php $width=520 @endphp
    <div class="container">
        <h1>Demande de Devis</h1>
        <h2>JACQUET JEAN CLAUDE</h2>

        <table width="100%">
            <thead>
                <tr>
                    <th style="width:{{$width/4}}px;">Logiciel Utilisé</th>
                    <th style="width:{{$width/4}}px;">Version</th>
                    <th style="width:{{$width/4}}px;">Date de version</th>
                    <th style="width:{{$width/4}}px;">Moteur de calcul</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$all_data['logiciel_audit'] ?? ''}}</td>
                    <td>{{$all_data['logiciel_version'] ?? ''}}</td>
                    <td>{{$all_data['logiciel_date'] ?? ''}}</td>
                    <td>{{$all_data['moteur_calcul'] ?? ''}}</td>
                </tr>
            </tbody>
        </table>

        <h3>Informations du Client</h3>
        <table width="100%">
            <tr>
                <th style="width:{{$width*33/100}}px;"><strong>Nom</strong></th>
                <td style="width:{{$width*80/100}}px;">{{$all_data['nom'] ?? ''}} {{$all_data['prenom'] ?? ''}}</td>
            </tr>
            <tr>
                <th><strong>Adresse</strong></th>
                <td>{{$all_data['numero_voie'] ?? ''}} {{$all_data['adresse'] ?? ''}}, {{$all_data['cp'] ?? ''}} {{$all_data['ville'] ?? ''}}</td>
            </tr>
            <tr>
                <th><strong>Surface Habitable</strong></th>
                <td>{{$all_data['shab'] ?? ''}} m²</td>
            </tr>
            <tr>
                <th><strong>Compteur</strong></th>
                <td>{{$all_data['type_compteur_electricite'] ?? ''}}</td>
            </tr>
            <tr>
                <th><strong>Type de Chauffage</strong></th>
                <td>{{$all_data['chauffage'] ?? ''}}</td>
            </tr>
        </table>

        <h3>État Actuel</h3>
        <table width="100%">
            <tr>
                <th style="width:{{$width/2}}px;min-width:{{$width/2}}px;"><strong >Étiquette Énergétique Initiale</strong></th>
                <td style="width:{{$width/2}}px;min-width:{{$width/2}}px;">{{$all_data['classe_energetique_initiale'] ?? ''}}</td>
            </tr>
            <tr>
                <th><strong>CEF Initial</strong></th>
                <td>{{$all_data['cef_initial'] ?? ''}} kWh/m².an</td>
            </tr>
            <tr>
                <th><strong>CEP Initial</strong></th>
                <td>{{$all_data['cep_initial'] ?? ''}} kWh/m².an</td>
            </tr>
            <tr>
                <th><strong>GES Initial</strong></th>
                <td>{{$all_data['ges_initial'] ?? ''}} kgéqCO2/m².an</td>
            </tr>
        </table>

        @for ($i=1;$i<=2;$i++)
        <h3>Scénario {{$i}}</h3>
        <table>
            <tr>
                <th style="width:{{$width/2}}px;min-width:{{$width/2}}px;"><strong>Etiquette énergétique finale</strong></th>
                <td style="width:{{$width/2}}px;min-width:{{$width/2}}px;">{{$all_data['classe_energetique_finale_'.$i] ?? ''}}</td>
            </tr>

         


            <tr>
                <th><strong>Nombre de sauts</strong></th>
                <td>{{$all_data['saut_classe_prevu_'.$i] ?? ''}}</td>
            </tr>
            <tr>
                <th><strong>CEF final (kWh/m².an)</strong></th>
                <td>{{$all_data['cef_final_'.$i] ?? ''}}</td>
            </tr>
      
            <tr>
                <th><strong>CEP final (kWh/m².an)</strong></th>
                <td>{{$all_data['cep_final_'.$i] ?? ''}} kW</td>
            </tr>
            <tr>
                <th><strong>GES final (kgéqCO2/m².an)</strong></th>
                <td>{{$all_data['ges_final_'.$i] ?? ''}} kW</td>
            </tr>
            <tr>
                <th><strong>Déperdition (kW)</strong></th>
                <td>{{$all_data['deperdition_'.$i] ?? ''}} kW</td>
            </tr>
            <tr>
                <th><strong>Gain Énergétique</strong></th>
                <td>{{$all_data['gain_energetique'.$i] ?? ''}}%</td>
            </tr>
        </table>
        <table style="margin-top:20px">
            <tr>
                <th colspan="5" style="text-align:center;width:{{$width/2}}px;" colspan="2"><strong>Travaux</strong></th>
            </tr>
            @php
            $travaux=['combles','iti','ite','vmc','vmc_double','terrasse','sous_sols','pac_air_air','pac_air_eau','ballon','ballon_solaire','fenetre_1','fenetre_2','poele','porte_fenetre1','porte_fenetre_2','rampants','solaire']
            @endphp

            @foreach($travaux as $value)
            @if(isset($all_data['display_'.$value.'_s'.$i]) && $all_data['display_'.$value.'_s'.$i]==1)
            <tr>
                <td style="width:20%"><strong>{{$all_data[$value.'_title_s'.$i]}}</strong></td>
                <td style="width:20%"></td>
                <td style="width:20%"></td>
                <td style="width:20%"></td>
                <td style="width:20%"></td>
            </tr>
            @endif
            @endforeach
        </table>
        @endfor
       
      

        <table>
            <tr>
                <td><strong>Date d'Audit</strong></td>
                <td>16/10/2024</td>
            </tr>
            <tr>
                <td><strong>Numéro d'Audit</strong></td>
                <td>00000</td>
            </tr>
            <tr>
                <td><strong>Moteur de Calcul</strong></td>
                <td>3CL</td>
            </tr>
        </table>

        <p class="text-center" style="margin-top: 20px; color: #555;">MERCI POUR VOTRE RETOUR !</p>
    </div>
</page>
