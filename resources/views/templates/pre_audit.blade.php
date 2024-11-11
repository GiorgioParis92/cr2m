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
   
    <div class="container">
        <h1>Demande de Devis</h1>
        <h2>JACQUET JEAN CLAUDE</h2>

        <table width="100%">
            <thead>
                <tr>
                    <th>Logiciel Utilisé</th>
                    <th>Version</th>
                    <th>Date de version</th>
                    <th>Moteur de calcul</th>
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
        <table>
            <tr>
                <td><strong>Nom</strong></td>
                <td>{{$all_data['nom'] ?? ''}} {{$all_data['prenom'] ?? ''}}</td>
            </tr>
            <tr>
                <td><strong>Adresse</strong></td>
                <td>{{$all_data['numero_voie'] ?? ''}} {{$all_data['adresse'] ?? ''}}, {{$all_data['cp'] ?? ''}} {{$all_data['ville'] ?? ''}}</td>
            </tr>
            <tr>
                <td><strong>Surface Habitable</strong></td>
                <td>{{$all_data['shab'] ?? ''}} m²</td>
            </tr>
            <tr>
                <td><strong>Compteur</strong></td>
                <td>{{$all_data['type_compteur_electricite'] ?? ''}}</td>
            </tr>
            <tr>
                <td><strong>Type de Chauffage</strong></td>
                <td>{{$all_data['chauffage'] ?? ''}}</td>
            </tr>
        </table>

        <h3>État Actuel</h3>
        <table>
            <tr>
                <td><strong>Étiquette Énergétique Initiale</strong></td>
                <td>{{$all_data['classe_energetique_initiale'] ?? ''}}</td>
            </tr>
            <tr>
                <td><strong>CEF Initial</strong></td>
                <td>{{$all_data['cef_initial'] ?? ''}} kWh/m².an</td>
            </tr>
            <tr>
                <td><strong>CEP Initial</strong></td>
                <td>{{$all_data['cep_initial'] ?? ''}} kWh/m².an</td>
            </tr>
            <tr>
                <td><strong>GES Initial</strong></td>
                <td>{{$all_data['ges_initial'] ?? ''}} kgéqCO2/m².an</td>
            </tr>
        </table>

        @for($i=1;$i<=2,$i++)
        <h3>Scénario {{$i}}</h3>
        <table>
            <tr>
                <td><strong>Déperdition Scénario {{$i}}</strong></td>
                <td>{{$all_data['deperdition_'.$i] ?? ''}} kW</td>
            </tr>
            <tr>
                <td><strong>Gain Énergétique Scénario {{$i}}</strong></td>
                <td>{{$all_data['gain_energetique'.$i] ?? ''}}%</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Travaux</strong></td>
            </tr>
            <tr>
                <td>Isolation des murs par l'extérieur</td>
                <td>R ≥ 4,4, 138 m²</td>
            </tr>
            <tr>
                <td>Isolation des combles perdus</td>
                <td>R ≥ 7, 42 m²</td>
            </tr>
            <tr>
                <td>VMC Simple Flux à Caisson Basse Consommation Hygro B</td>
                <td>Puissance A.P ≤ 22 WThC, 1 unité(s)</td>
            </tr>
            <tr>
                <td>Chauffe-eau thermodynamique</td>
                <td>Cop ≥ 2,8, 200 L</td>
            </tr>
            <tr>
                <td>PAC Air/Air</td>
                <td>SCOP ≥ 4, P ≥ 14 kW, 1 unité(s)</td>
            </tr>
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
