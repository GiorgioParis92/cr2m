<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .container {

        width: 90%
    }

    table {
        border-collapse: collapse;
        width: 100%;
        max-width: 300px;
        margin-bottom: 20px;
        border: 0;
        box-sizing: border-box;
    }

    table th {
        border: 0;
    }

    .header-table {
        max-width: 80%;
    }

    .header-table td {
        border: 0;
    }

    .address-table td {
        border: 0;
    }

    .items-table {
        width: 100%;
        margin-top: 25px;
        border-collapse: collapse;
    }

    .items-table th {
        border: 2px solid white;
        padding: 5px;
        background-color: #336dea;
        color: white;
        font-weight: bold;
        text-align: center;
    }

    .items-table td {
        border: 2px solid white;
        padding: 5px;
        color: rgb(108, 108, 108);
        text-align: center;
        background: #eaf7ff;
    }

    .items-table td.description {
        text-align: left;
    }

    .items-table td.right-align {
        text-align: right;
    }

    .tables-container {
        display: flex;
        width: 100%;
    }

    .table-container {
        width: 40%;
        display: inline-block;
        vertical-align: top;
        position: relative;
    }

    .div_head div,
    .div_contain div {
        display: inline;
        width: 33%;
    }

    .div_head div,
    .horizontal_left {
        border-collapse: collapse;
        background: #336dea;
        padding: 5px;
        color: white;
        text-align: right;
        border: 2px solid white;
        font-weight: bold;
    }

    .div_contain div,
    .horizontal_right {
        border-collapse: collapse;
        background: #eaf7ff;
        padding: 5px;
        color: rgb(108, 108, 108);
        text-align: right;
        border: 2px solid white;

    }

    .horizontal_right,
    .horizontal_left {
        display: inline;
        max-width: 70px;
        width: 70px;
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
        width: 95%;
        margin: auto;
    }

    .footer_block {
        max-width: 80%;
        color: black;
        display: block;
    }

    .observations {
        font-size: 9px;
        display: block;
        font-style: italic;
    }

    h1 {
        text-align: center;
        width: 80%;
        font-size: 20px;
    }

    page {
        max-width: 80%;
        margin: auto;
        padding-left: 10%;
        padding-right: 10%;
    }

    p {

        margin: auto;
        padding-left: 10%;
        padding-right: 10%;
        text-align: justify;

    }
</style>

<page style="width:210mm" backtop="10mm" backleft="10mm" backright="10mm" backbottom=" 10mm">

    <div class="container">
        <div class="row">
            <div class="col-12">
                <img src="{{ asset('storage/'.$dossier->client->main_logo) }}">
                <h1>{{$dossier->client->client_title}}</h1>
            </div>
        </div>

        <h1 class="text-center">Contrat de mission Prestation Mon Accompagnateur Rénov'</h1>
        <p>Entre les soussignés,</p>

        <div class="my-4">
            <p>Mme ou M.: {{$dossier->beneficiaire->nom}} {{$dossier->beneficiaire->prenom}}</p>
            <p>Demeurant: 157 Rue des jonquilles 83600 Frejus</p>
            <p>Agissant pour son compte propre :</p>
            <p>Ci-après nommé « Le bénéficiaire »</p>
        </div>

        <div class="my-4">
            <h4>D'une part</h4>
            <br>
            <h4>Et</h4>
            <br>
            <p>Le bureau d'études LP Audit SAS ayant pour SIRET 91254114100053 dont le siège social est situé
                au 45, avenue du Président JF Kennedy - 64200 Biarritz</p>
            <p>Représenté par Mr Mathieu Dugué</p>
            <p>Agissant en qualité de Directeur Général</p>
            <p>Ci-après, désigné « Mon Accompagnateur Rénov »</p>
            <p>Numéro d'agrément numéro : MAR-64-0001235</p>
            <br>

            <h4>D'autre part</h4>
            <p><strong>IL a été convenu ce qui suit :</strong></p>

            <p>Le bureau d'études LP Audit SAS effectue des missions de Mon Accompagnateur Rénov' auprès
                des particuliers visant à faire réaliser des travaux de rénovation énergétique. C'est dans le cadre
                général de ces missions que le bénéficiaire, maître d'ouvrage, conclut le présent contrat pour un
                logement constituant sa résidence principale.
            </p>
        </div>






        <h2>Article 1: Objet du contrat</h2>
        <p>Le présent contrat a pour objet de confier au bureau d'études LP Audit SAS des prestations obligatoires de
            Mon Accompagnateur Rénov' en vue de la réalisation de travaux de rénovation énergétique conformément à
            l'article R232-3 du Code de l'énergie et au Décret n° 2022-1035 du 22 juillet 2022.</p>
        </div>
        <div class="container">
        <h2>Article 2: Validité et durée du contrat</h2>
        <p>Le contrat entre en vigueur à la date de sa signature et prend fin à la date de la signature du rapport
            d'accompagnement.</p>

        <h2>Article 3: Contenu de la prestation</h2>
        <p>Les prestations objet du présent contrat sont celles définies à l'annexe 1 de l'arrêté du 21 décembre 2022
            relatif à la mission d'accompagnement du service public de la performance énergétique de l'habitat, ainsi
            qu'il suit :</p>
        <ol class="list-modifiee">
            <li>Une phase d'information préalable comprenant une visite initiale;</li>
            <li>Évaluation de la situation économique du ménage;</li>
            <li>La réalisation ou le recours à un audit énergétique conforme aux exigences de l'article 8 de l'arrêté du
                17
                novembre 2020;</li>
            <li>Un examen de l'état du logement réalisé sur site;</li>
            <li>Accompagnement au titre de la préparation du projet de travaux;</li>
            <li>Accompagnement au titre de la réalisation du projet de travaux;</li>
            <li>Accompagnement au titre de la prise en main du logement après travaux;</li>
            <li>Production d'un rapport d'accompagnement remis et contresigné par le ménage.</li>
        </ol>

        <p>Le bénéficiaire est informé que les détails de chacune des prestations mentionnées ci-dessus peuvent être
            consultés à l'annexe 1 de l'Arrêté du 21 décembre 2022 relatif à la mission d'accompagnement du service
            public de la performance énergétique de l'habitat.
        </p>

    </div>






</page>
<style>
    .section {
        width: 320px;
        max-width: 320px;
        text-align: justify;
        line-height: 16px
    }

    .section-title {
        font-weight: bold;
        font-size: 13px;
        text-transform: uppercase
    }
</style>
<page style="width:210mm" backtop="1mm" backleft="1mm" backright="1mm" backbottom="1mm">
    <div style="margin-top:18px;text-align:center;font-weight:bold;font-size:16px">CONDITIONS GENERALES DE VENTE</div>
    <table style="width:100%;margin-top:20px;">
        <tr>
            <td width="48%" style="width:48%;padding-left:25px;font-size:11px;vertical-align:top">
                <p class="section">
                <div class="section-title">1. Objet</div>
                <div>Les présentes Conditions Générales de Vente (CGV) régissent l'ensemble des prestations de services
                    fournies par MIA, société spécialisée dans la création d'images personnalisées
                    par mannequins virtuels gérés par l'IA, à ses clients professionnels.</div>
                </p>

                <p class="section">
                <div class="section-title">2. Acceptation des Conditions</div>
                <div>Toute commande passée auprès de MIA implique l'acceptation sans réserve des
                    présentes CGV par le client. Les CGV prévalent sur tout autre document émanant du client.</div>
                </p>

                <p class="section">
                <div class="section-title">3. Commandes</div>
                <div>
                    <b class="">3.1. Modalités de Commande :</b><br />
                    <div>Les commandes doivent être effectuées par écrit, par email ou via le site internet de [Nom de
                        Votre Société]. Toute commande doit préciser les spécifications détaillées des images à créer.
                    </div>
                </div><br /><br />
                <div>
                    <b class="">3.2. Confirmation de Commande :</b><br />
                    <div>La commande n'est considérée comme acceptée qu'après confirmation écrite de [Nom de Votre
                        Société] et réception du paiement de l'acompte de 50%.</div>
                </div>
                </p>

                <p class="section">
                <div class="section-title">4. Tarifs et Paiement</div>
                <div>
                    <b>4.1. Tarifs :</b><br />
                    <div>Les tarifs sont indiqués en euros hors taxes (HT) et sont majorés de la TVA applicable au taux
                        en vigueur. MIA se réserve le droit de modifier ses tarifs à tout moment. Les
                        tarifs applicables sont ceux en vigueur au jour de la commande.</div>
                </div><br /><br />
                <div>
                    <b>4.2. Modalités de Paiement :</b><br />
                    <div>Un acompte de 50% du montant total TTC est exigé à la commande. Le solde de 50% est dû à la
                        livraison des images. Les paiements sont effectués par virement bancaire aux coordonnées
                        fournies par MIA.</div>
                </div>
                </p>

                <p class="section">
                <div class="section-title">5. Livraison</div>
                <div>
                    <b>5.1. Délais de Livraison :</b><br />
                    <div>Les délais de livraison sont donnés à titre indicatif et ne constituent pas un engagement ferme
                        de MIA. En cas de retard de livraison, le client ne peut prétendre à aucune
                        indemnité.</div>
                </div><br /><br />
                <div>
                    <b>5.2. Réception :</b><br />
                    <div>Le client doit vérifier la conformité des images livrées dans un délai de 7 jours. Toute
                        réclamation doit être faite par écrit dans ce délai. En l'absence de réclamations dans ce délai,
                        les images sont considérées comme conformes et acceptées.</div>
                </div>
                </p>

            </td>
            <td width="48%" style="width:48%;padding-left:25px;font-size:11px;vertical-align:top">


                <p class="section">
                <div class="section-title">6. Propriété Intellectuelle</div>
                <div>
                    <b>6.1. Transfert de Propriété :</b><br />
                    <div>La propriété intellectuelle des images créées est transférée au client après paiement intégral
                        du prix. MIA se réserve le droit d'utiliser les images pour sa propre
                        promotion avec l'accord préalable du client.</div>
                </div><br /><br />
                <div>
                    <b>6.2. Utilisation des Images :</b><br />
                    <div>Le client s'engage à utiliser les images conformément aux droits acquis et à ne pas les
                        revendre ou les distribuer sans autorisation de MIA.</div>
                </div>
                </p>

                <p class="section">
                <div class="section-title">7. Confidentialité</div>
                <div>
                    <b>7.1. Engagement de Confidentialité :</b><br />
                    <div>MIA et le client s'engagent à garder confidentielles toutes les informations
                        échangées dans le cadre de leur collaboration.</div>
                </div>
                </p>

                <p class="section">
                <div class="section-title">8. Rétractation et Résiliation</div>
                <div>
                    <b>8.1. Droit de Rétractation :</b><br />
                    <div>Le client dispose d’un délai de 14 jours à compter de la signature de la commande pour se
                        rétracter sans frais, sous réserve qu'aucun travail n'ait commencé.</div>
                </div><br /><br />
                <div>
                    <b>8.2. Résiliation :</b><br />
                    <div>En cas de résiliation par le client après début des prestations, l'acompte versé reste acquis à
                        MIA.</div>
                </div>
                </p>

                <p class="section">
                <div class="section-title">9. Responsabilité</div>
                <div>
                    <b>9.1. Limitation de Responsabilité :</b><br />
                    <div>MIA ne peut être tenue responsable des dommages indirects subis par le
                        client. La responsabilité de MIA est limitée au montant de la commande.</div>
                </div>
                </p>

                <p class="section">
                <div class="section-title">10. Litiges</div>
                <div>
                    <b>10.1. Règlement des Litiges :</b><br />
                    <div>En cas de litige, les parties s'engagent à rechercher une solution amiable avant toute action
                        judiciaire. Les tribunaux compétents seront ceux de [Ville] en cas d'échec de la médiation.
                    </div>
                </div>
                </p>

                <p class="section">
                <div class="section-title">11. Droit Applicable</div>
                <div>Les présentes CGV sont régies par le droit français. Toute question relative aux CGV et aux ventes
                    qu'elles régissent sera soumise au droit français.</div>
                </P>

                <p class="section">
                <div>Pour toute question relative aux présentes Conditions Générales de Vente, merci de nous contacter à
                    [Votre Email] ou [Votre Numéro de Téléphone].</div>
                </p>

                <p class="section">
                <div>MIA</div>
                <div>[Nom du Dirigeant]</div>
                <div>[Votre Fonction]</div>
                <div>[Votre Email]</div>
                </p>
            </td>
        </tr>
    </table>





</page>
