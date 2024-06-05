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
        color: rgb(108,108,108);
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
      
</style>

<page style="width:210mm" backtop="1mm" backleft="1mm" backright="1mm" backbottom=" 1mm">

    <table class="header-table">
        <tr>
            <td style="width:50%;">
                <img style="max-width:200px; margin-top:-10px;" src="{{ asset('storage/images/logo.png') }}">
            </td>
            <td style="width:50%;">
                <div class="invoice-header"
                    style=" justify-content: space-between; align-items: center; margin-top:0; text-align:right;padding-right:45px">
                    <h1 style="font-size: 24px; margin: 0;">Devis</h1>
                    <p style="margin: 0;">Date: {{ date('d/m/Y', strtotime($devis->created_at)) }}</p>
                    <p style="margin: 0;">N°: {{ $devis->devis_name }}</p>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">
                <div style="margin-top:-20px;margin-left:8px; color:#696969;">
                    <p>MODEL INTELLIGENCE AGENCY<br />
                        7 rue de l'Amiral Courbet<br /> 94160 SAINT-MANDE<br />
                        <i>SIRET : 88427857300024</i>
                    </p>
                </div>
            </td>
            <td style="width:50%;"></td>
        </tr>
    </table>

    <table class="address-table">
        <tr>
            <td style="width:50%;"></td>
            <td style="width:50%;">
                <div
                    style="margin-left:60px;border:1px solid #b3b3b3;border-radius:5px;max-width:75%;width:60%;padding-left:8px;padding-bottom:15px">
                    <p><b>{{ $devis->client->client_title }}</b><br />
                        {{ $devis->client->adresse }}<br />
                        {{ $devis->client->cp }} {{ $devis->client->ville }}
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table" style="">
        <thead>
            <tr>
                <th>Description</th>
                <th style="max-width:8%;width:8%">Qté</th>
                <th style="max-width:12%;width:12%">Prix U.</th>
                <th style="max-width:12%;width:12%">Remise</th>
                <th style="max-width:12%;width:12%">Total H.T.</th>
                <th style="max-width:12%;width:12%">TVA<br />20%</th>
                <th style="max-width:12%;width:12%">Total TTC</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @php $i = 0; @endphp
            @foreach ($devis->items as $item)
                <tr>
                    @php
                        $total_ht =
                            $item->quantity * $item->price - ($item->discount * $item->quantity * $item->price) / 100;
                        $total = $total + $total_ht;
                        $i++;
                    @endphp
                    <td class="description" style=" padding: 15px;">
                        <div style="min-width:180px;width:180px;">
                            {{ $item->product_name }}
                            @if ($item->observations)
                                <br />
                                <div style="margin-top:8px" class="observations">{{ $item->observations }}</div>
                            @endif
                        </div>
                    </td>
                    <td style=" padding: 15px;max-width:8%;width:8%">
                        {{ $item->quantity }}
                    </td>
                    <td class="right-align" style=" padding: 15px;">{{ number_format($item->price, 2, ',', ' ') }}
                    </td>
                    <td class="right-align" style=" padding: 15px;">
                        {{ number_format($item->discount, 2, ',', ' ') }}</td>
                    <td class="right-align" style=" padding: 15px;">{{ number_format($total_ht, 2, ',', ' ') }}
                    </td>
                    <td class="right-align" style=" padding: 15px;">
                        {{ number_format(($item->quantity * $item->price * 20) / 100, 2, ',', ' ') }}</td>
                    <td class="right-align" style=" padding: 15px;">
                        {{ number_format($item->quantity * $item->price * 1.2, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
            <tr style="border-bottom:1px solid white;">
                <td
                    style="background-color: white;padding:10px; height:18px; border:0 solid white; border-left:1px solid white; border-right:1px solid white;">
                </td>
                <td
                    style="background-color: white;padding:10px; height:18px; border:0 solid white; border-left:1px solid white; border-right:1px solid white;">
                </td>
                <td
                    style="background-color: white;padding:10px; height:18px; border:0 solid white; border-left:1px solid white; border-right:1px solid white;">
                </td>
                <td
                    style="background-color: white;padding:10px; height:18px; border:0 solid white; border-left:1px solid white; border-right:1px solid white;">
                </td>
                <td
                    style="background-color: white;padding:10px; height:18px; border:0 solid white; border-left:1px solid white; border-right:1px solid white;">
                </td>
                <td
                    style="background-color: white;padding:10px; height:18px; border:0 solid white; border-left:1px solid white; border-right:1px solid white;">
                </td>
            </tr>
        </tbody>
    </table>





    <div class="tables-container">
        <div class="table-container" style="margin-right:150px;margin-left:2px">
            <div class="tva-table">
                <div class="div_head">
                    <div>Taux TVA</div>
                    <div>Montant TVA</div>
                    <div>Montant H.T.</div>
                </div>
                <div class="div_contain">
                    <div>{{ number_format('20', 2, ',', ' ') }} %</div>
                    <div>{{ number_format($total * 0.2, 2, ',', ' ') }} €</div>
                    <div>{{ number_format($total, 2, ',', ' ') }} €</div>
                </div>
            </div>
        </div>
        <div class="table-container" style="top:-58px; right:0px;margin-left:60px">
            <div class="horizontal_container">
                <div class="horizontal_left">Total H.T.</div>
                <div class="horizontal_right">{{ number_format($total, 2, ',', ' ') }} €</div>
            </div>
            <div class="horizontal_container">
                <div class="horizontal_left">TVA 20%</div>
                <div class="horizontal_right">{{ number_format($total * 0.2, 2, ',', ' ') }} €</div>
            </div>
            <div class="horizontal_container">
                <div class="horizontal_left"><b>Total TTC</b></div>
                <div class="horizontal_right"><b>{{ number_format($total * 1.2, 2, ',', ' ') }} €</b></div>
            </div>
        </div>
    </div>
    <table style="margin-top:25px;margin-left:10px;font-size:10px;color:#b3b3b3">
        <tr>
            <td>
                <div>Modalité de paiement : 30% d'acompte à la signature de ce devis. Solde à la livraison.</div>
            </td>
        </tr>
    </table>
    <div>
        <div>
            <div style="width:50%;"></div>
            <div style="width:50%;">
                <div
                    style="margin-left:380px;border:1px solid #b3b3b3;border-radius:5px;max-width:75%;width:60%;padding-left:8px;padding-bottom:130px">
                    <p>Bon pour accord. Signature :
                    </p>
                </div>
            </div>
        </div>
    </div>
    <table style="position: absolute; bottom:80px;">
        <tr>
            <td>
                <div style="text-align:center; font-style:italic; font-size:10px; color:#8f8f8f;">
                    Dans le cadre de la loi n°2008-776 du 4 aout 2008, nous vous précisons que tout retard de paiement
                    donnera lieu à l'application<br />
                    d'une pénalité égale à trois fois le taux d'intérêt légal. La société acquitte la T.V.A. sur les
                    encaissements.<br />
                </div>
                <div style="text-align:center; font-style:italic; font-size:10px; color:#8f8f8f;">
                    <b>Voir conditions générales au dos</b><br />
                </div>
            </td>
        </tr>
    </table>




    <div class="footer" style="position: absolute;bottom:0px;margin:auto">
        <span class="footer_block">
            MIA - MODEL INTELLIGENCE AGENCY
        </span><br />
        <span class="footer_block">
            R.C.S. Créteil 884 278 573 - APE : 6201Z
        </span><br />
        <span class="footer_block">
            N° de TVA : FR94884278573
        </span>
    </div>


</page>
<style>
    .section {
        width: 320px;
        max-width: 320px;
        text-align: justify;
        line-height:16px
    }
    .section-title {
        font-weight:bold;
        font-size:13px;
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
