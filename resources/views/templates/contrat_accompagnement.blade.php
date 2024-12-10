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
            @if(isset($dossier->client->main_logo) && file_exists(storage_path('app/public/' . $dossier->client->main_logo)))

            <img src="{{ asset('storage/' . $dossier->client->main_logo) }}" alt="Logo">

            @endif
        </div>
    </div>

    <h1 class="text-center">Contrat de mission Prestation Mon Accompagnateur Rénov'</h1>

    <div class="my-4">
        <p>
            Entre les soussignés,<br />
            Mme ou M.: {{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }}<br />
            Demeurant: {{ $dossier->beneficiaire->numero_voie }}{{ $dossier->beneficiaire->adresse }} {{ $dossier->beneficiaire->cp }}
            {{ $dossier->beneficiaire->ville }}<br />
            Agissant pour son compte propre :<br />
            Ci-après nommé <b>« Le bénéficiaire »</b><br />
        </p>
    </div>

    <div class="my-4">
        <p>
            <b>D'une part</b><br />
            <b>Et</b>
        </p>
    </div>

    <div class="my-4">
        <p>
            Le bureau d'études <b>{{ $dossier->client->client_title }}</b> {{ $dossier->client->type_societe }} ayant
            pour SIRET {{ $dossier->client->siret }}<br />
            dont le siège social est situé<br />
            au {{ $dossier->client->adresse }} {{ $dossier->client->cp }} {{ $dossier->client->ville }}<br />
            représenté par {{ $dossier->client->representant ?? '' }}<br />
            agissant en qualité de {{ $dossier->client->qualite ?? '' }}<br />
            Ci-après, désigné <b>« Mon Accompagnateur Rénov »</b><br />
            Numéro d'agrément numéro : {{ $dossier->client->agrement ?? '' }}</p>

    </div>

    <div class="my-4">
        <p>
            <b>D'autre part</b><br />
            <b>Il a été convenu ce qui suit :</b>
        </p>
    </div>

    <div class="my-4">
        <p>L’accompagnateur Rénov {{ $dossier->client->client_title }} effectue des missions de Mon Accompagnateur
            Rénov' auprès
            des particuliers visant à faire réaliser des travaux de rénovation énergétique. C'est dans le cadre
            général de ces missions que le bénéficiaire, maître d'ouvrage, conclut le présent contrat pour un
            logement constituant sa résidence principale.
        </p>
    </div>

    <div class="my-4">
        <h3>Article 1: Objet du contrat</h3>
        <p>Le présent contrat a pour objet de confier au bureau d'études {{ $dossier->client->client_title }}
            des
            prestations obligatoires de
            Mon Accompagnateur Rénov' en vue de la réalisation de travaux de rénovation énergétique conformément à
            l'article R232-3 du Code de l'énergie et au Décret n° 2022-1035 du 22 juillet 2022.</p>
    </div>

    <div class="my-4">
        <h3>Article 2: Validité et durée du contrat</h3>
        <p>Le contrat entre en vigueur à la date de sa signature et prend fin à la date de la signature du rapport
            d'accompagnement.</p>
    </div>

    <div class="my-4">
        <h3>Article 3: Contenu de la prestation</h3>
        <p>Les prestations objet du présent contrat sont celles définies à l'annexe 1 de l'arrêté du 21 décembre 2022
            relatif à la mission d'accompagnement du service public de la performance énergétique de l'habitat, ainsi
            qu'il suit :</p>
        <ul>
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
        </ul>

        <p>Le bénéficiaire est informé que les détails de chacune des prestations mentionnées ci-dessus peuvent être
            consultés à l'annexe 1 de l'Arrêté du 21 décembre 2022 relatif à la mission d'accompagnement du service
            public de la performance énergétique de l'habitat.
            Les tableaux ci-dessous en font un résumé

        </p>
    </div>

</page>
<page backtop="25mm" backleft="10mm" backright="10mm" backimg="" backbottom="10mm">
    @include('pdf.header')
    @include('pdf.footer')
    <div class="my-4" style="margin-top:20px">
        <p>
            Les tableaux ci-dessous en font un résumé
        </p>
    </div>
    <table>
        <tr>
            <td class="background_td border-bottom text-center">
                <p><b style="font-size:13px;">Accompagnement technique</b></p>
            </td>

        </tr>
        <tr>

            <td class=" border-bottom text-center">

            </td>
        </tr>

        <tr>

            <td class=" border-bottom text-center">
                <p>Avant les travaux</p>
            </td>
        </tr>
        <tr>

            <td class=" border-bottom">
                <p>Lors de la 1ère visite obligatoire : </p>
                <p>
                <ul>
                    <li>
                        Visiter la maison individuelle avant travaux

                    </li>
                    <li>
                        Réaliser un état des lieux visuel du logement (énergétique, état de dégradation ou autonomie
                        du
                        ménage dans son logement).

                    </li>
                    <li>
                        Évaluer le cas échéant la situation d'indignité, d'indécence et de péril du logement sur
                        base de
                        la grille d'analyse simplifiée de l'ANAH.
                    </li>
                    <li>

                        Réaliser un audit énergétique
                    </li>
                </ul>
                </p>
            </td>
        </tr>

        <tr>

            <td class=" border-bottom">
                <p>Établir une feuille de route des travaux au regard du scénario de rénovation choisi après l'audit
                    énergétique. </p>

            </td>
        </tr>
        <tr>

            <td class=" border-bottom">
                <p>Dispenser des conseils pour la sélection des entreprises, assister le ménage pour l'analyse des
                    devis, expliquer les signes de qualité d'un produit. </p>

            </td>
        </tr>

        <tr>

            <td class=" border-bottom text-center">
                <p> Pendant les travaux</p>
            </td>
        </tr>

        <tr>

            <td class=" border-bottom">
                <p>Aider au suivi de travaux: </p>
                <p>
                <ul>
                    <li>
                        Dispenser des informations aux différentes phases d'un chantier de rénovation

                    </li>
                    <li>
                        Dispenser des conseils sur le suivi d'un chantier (fréquence et organisation des réunions de
                        chantier, points de vigilance sur la qualité de mise en œuvre et conformité aux devis,
                        appropriation des notions de coordination de chantier).

                    </li>
                    <li>
                        Dispenser des conseils sur la réception des travaux et les garanties.
                    </li>

                </ul>
                </p>
            </td>
        </tr>
        {{-- <tr>

            <td class=" border-bottom text-center">
                <p>
                    <b> L'Accompagnateur Rénov' n'effectuera aucune visite du chantier dans le cadre du présent contrat.
                        Il ne peut pas intervenir directement auprès des entreprises chargées de la réalisation des
                        travaux et n'est pas responsable de leur bonne réalisation. </b>
                </p>
            </td>
        </tr> --}}

        <tr>

            <td class=" border-bottom text-center">
                <p>Après les travaux</p>
            </td>
        </tr>

        <tr>

            <td class=" border-bottom">
                <p>Lors de la 2eme visite obligatoire </p>
                <p>
                <ul>
                    <li>
                        Conseiller sur la prise en main du logement post-travaux:


                    </li>
                    <li>
                        Dispenser des informations sur la bonne utilisation du logement (qualité de l'air intérieur,
                        maintenance équipements de chauffage et ventilation, confort d'été, écogestes).

                    </li>
                    <li>
                        Aider à l'analyse des consommations post-travaux .
                    </li>
                    <li>
                        Aider à créer ou mettre à jour le Carnet d'information du logement (CIL).
                    </li>
                </ul>
                </p>
            </td>
        </tr>
    </table>


    <table>
        <tr>
            <td class="background_td border-bottom text-center">
                <b style="font-size:13px;">Accompagnement administratif </b>
            </td>

        </tr>


        <tr>

            <td class=" border-bottom">
                <ul>
                    <li>
                        Procéder à un accompagnement dans les démarches en ligne pour percevoir les aides de l'Anah.
                    </li>
                    <li>
                        Conseiller le ménage dans le montage de dossiers d'aides et de financement du reste à
                        charge.

                    </li>
                    <li>
                        Renseigner le client sur les obligations de procédures d'urbanisme à réaliser le cas échéant
                        (obligation d'un permis de construire, déclaration de travaux ... )
                    </li>
                    <li>

                        Expliquer le rôle des différents types de mandataires administratifs et/ou financiers
                    </li>
                </ul>
            </td>
        </tr>

    </table>


    <table>
        <tr>
            <td class="background_td border-bottom text-center">
                <b style="font-size:13px;">Accompagnement financier </b>
            </td>

        </tr>


        <tr>

            <td class=" border-bottom">
                <ul>
                    <li>
                        Définir le reste à charge
                    </li>
                    <li>
                        Aider au montage du plan de financement

                    </li>
                    <li>
                        Conseiller sur les différentes aides financières mobilisables
                    </li>

                </ul>
            </td>
        </tr>

    </table>

    <div class="my-4">
        <h3>Article 4 : Obligations des parties </h3>
        <h4>Obligations de Mon Accompagnateur Rénov' </h4>
        <p>Mon Accompagnateur Rénov' s'engage à </p>
        <ul>
            <li>
                Exécuter les missions prévues à l'article 3 ci-dessus et acceptées par le bénéficiaire;
            </li>
            <li>
                Veiller aux intérêts du bénéficiaire, maître d'ouvrage, dans le cadre du projet retenu;
            </li>
            <li>
                Respecter un critère de performance permettant au moins le franchissement d'un seuil énergétique
            </li>
            <li>

                Aider le ménage à remplir le carnet d'information du logement;
            </li>
            <li>

                Remplir les conditions d'indépendance par rapport aux activités d'exécution d'ouvrage Donner des
                conseils en matière de lutte contre la précarité énergétique aux ménages modestes et très modestes.
            </li>
        </ul>
        <h4>Obligations du bénéficiaire </h4>
        <ul>
            <li>
                Le bénéficiaire doit mettre à disposition de Mon Accompagnateur Rénov' toutes informations et tous
                documents nécessaires à la bonne réalisation de sa mission.
            </li>
            <li>
                La signature du devis des travaux par le bénéficiaire et l'acceptation du plan de financement ne peut se
                faire sans l'accord écrit de Mon Accompagnateur Rénov'. Sans cet accord écrit, l'Accompagnateur Rénov'
                décline toute responsabilités quant aux conséquences de ces engagements. On peut citer parmi ces
                conséquences (liste non exhaustives) : montant des devis trop élevé, choix techniques inadaptés au
                logement, plan de financement inadapté à la situation financière du ménage.
            </li>
            <li>
                Si le bénéficiaire n'est pas satisfait pendant l'exécution des travaux, ou à la fin des travaux, il doit
                en avertir immédiatement Mon Accompagnateur Rénov' par lettre recommandée avec accusé de réception.
            </li>
            <li>

                Le bénéficiaire doit informer Mon Accompagnateur Rénov' s'il contracte un prêt pour ses travaux de
                rénovation énergétique.
            </li>
            <li>
                Le bénéficiaire doit accepter les différentes sollicitations de Mon Accompagnateur Ré nov'. En
                particulier, il doit d'accepter que Mon Accompagnateur Rénov' réalise la seconde visite après la fin des
                travaux. Cette seconde visite est rendue obligatoire conformément à l'arrêté du 21 décembre 2022, faute
                de quoi les financements de l'Anah seront remis en cause.
            </li>
        </ul>

    </div>

    <div class="my-4">
        <h3>Article 5 : Responsabilités </h3>
        <h4>Responsabilités de MAR </h4>
        <p>

            Il est entendu, conformément à l'Arrêté du 21 décembre 2022 cité ci-dessus, que
        </p>
        <ul>
            <li>
                « Mon Accompagnateur Rénov' » est responsable du conseil et de l'assistance au ménage pendant la durée
                de la prestation
            </li>
            <li>
                L'accompagnateur n'est pas responsable de l'exécution des travaux et de leur bonne réalisation, ni du
                choix du projet de travaux effectué par le ménage.
            </li>
            <li>
                L'accompagnateur n'est pas responsable de la qualité finale des travaux, dans la mesure où ceux-ci
                relèvent du choix du ménage. En revanche, il est tenu d'informer le ménage des défauts de qualité et des
                recours possibles en cas de persistance de malfaçons, ainsi que des assurances dommages-ouvrage
                accessibles.
            </li>

        </ul>
        <h4>Responsabilité du Bénéficiaire</h4>
        <p>Le bénéficiaire est responsable de:</p>
        <ul>
            <li>
                La sincérité et de l'exactitude des informations et documents fournis durant toute la prestation.
            </li>
            <li>
                La décision du choix de projet de travaux effectués, de leur exécution y compris de leur bonne
                réalisation.
            </li>

        </ul>

    </div>


    <div class="my-4">
        <h3> Article 6 : Mandat administratif et/ou financier </h3>
        <p>
            Le bénéficiaire peut désigner un mandataire administratif pour être accompagné dans ses démarches en ligne,
            pour déposer une demande de subvention et / ou une demande de paiement.
        </p>
        <p>
            La désignation d'un mandataire administratif et/ou financier devra se faire au moyen du formulaire» Mandat
            pour la constitution d'une demande d'aide en ligne» dûment rempli à destination de l'Anah.
        </p>
        <p>
            Le bénéficiaire est informé que le mandataire administratif peut accéder à son dossier et réaliser
            l'ensemble des démarches.
        </p>
        <p>
            Le bénéficiaire peut également désigner un mandataire financier qui avancera le coût des travaux couverts
            par les aides de l'Anah et une partie de la prestation de Mon Accompagnateur Rénov' en fonction de son
            plafond de ressources. Le mandataire reçoit de plein droit le montant des aides de l'Anah. Le bénéficiaire
            est informé que le mandataire financier n'a aucun accès à son dossier.
        </p>
        <p>
            Dans le cas où le bénéficiaire donne mandat à un mandataire financier pour que ce dernier soit en charge du
            paiement de la prestation de Mon Accompagnateur Rénov' à {{ $dossier->client->client_title }} en lieu et
            place du bénéficiaire, ce dernier ne peut dans ce cas précis révoquer le mandataire.
        </p>
        <p>
            Si, toutefois, le bénéficiaire décide de révoquer le mandataire financier, il devra régler la totalité du
            montant de la prestation déjà effectuée dans les 15 jours à {{ $dossier->client->client_title }}.
        </p>
    </div>



    <div class="my-4">
        <h3> Article 7 : Rétractation </h3>
        <p>
            Le Bénéficiaire peut se rétracter du présent contrat sans donner de motif dans un délai de quatorze jours.
            Le délai de rétractation expire quatorze jours à compter de la date de signature du contrat (Article L221-18
            du Code de la Consommation).
        </p>
        <p>
            Pour exercer son droit de rétractation, le Bénéficiaire doit notifier à
            {{ $dossier->client->client_title }} , sa décision au moyen d'un courrier recommandé avec accusé de
            réception à l'adresse suivante: .
        </p>
        <p>
            Toutefois, le Bénéficiaire s'il le souhaite peut utiliser le modèle de formulaire de rétractation figurant
            ci-dessous
        </p>
    </div>
    <div class="my-4">
        <p style="max-width:60%;font-style:italic;line-height:22px">
            « A l'attention de {{ $dossier->client->client_title }} <br />
            {{ $dossier->client->adresse }}- {{ $dossier->client->cp }} {{ $dossier->client->ville }} <br />
            [Numéro de téléphone de la structure, le cas échéant] <br />
            [Adresse électronique de la structure, le cas échéant] <br />
            Je vous notifie par la présente ma rétractation du contrat portant sur la prestation de services ci-dessous
            <br />
            Mission d'accompagnateur Rénov <br />
            Commandé le : (date de signature du contrat d'accompagnement) <br />
            Nom du bénéficiaire : <br />
            Adresse du bénéficiaire : <br />
            Signature du bénéficiaire (uniquement en cas de notification du présent formulaire sur papier): <br />
            Date: " <br /><br />
        </p>
    </div>

    <div class="my-4">
        <h3>Article 8: résiliation du contrat </h3>

        <p>
            Il est rappelé conformément à l'article 2, que le contrat entre en vigueur à la date de sa signature et
            prend fin à la date de la signature du rapport d'accompagnement. Si le bénéficiaire n'est pas satisfait de
            l'ensemble des prestations fournies, il est de sa responsabilité d'informer par lettre recommandée "Mon
            Accompagnateur Rénov'" des prestations qu'il juge incomplètes ou non exécutées avant la signature du rapport
            d'accompagnement.
        </p>
        <h4>Résiliation du contrat par le bénéficiaire</h4>
        <p>Le contrat peut être résilié par le bénéficiaire à tout moment par lettre recommandée avec accusé de
            réception.</p>
        <p>Toutefois, il est convenu entre les parties que les prestations déjà effectuées dans le cadre du contrat
            restent dues par le bénéficiaire. Tout remboursement sera calculé sur la base de la somme restante après
            déduction des prestations déjà réalisées.</p>
        <h4>Résiliation de plein droit</h4>
        <p> En cas d'inexécution de l'une des obligations des parties, l'autre partie devra adresser une mise en demeure
            par lettre recommandée avec accusé de réception précisant les manquements reprochés. Si la mise en demeure
            est restée sans effet dans un délai de quatorze jours à compter de sa réception, le contrat sera, si bon
            semble au créancier de l'obligation inexécutée, résilié de plein droit.</p>
    </div>
    <div class="my-4">
        <h3>Article 9: Prix et règlement </h3>

        <p>Le présent contrat est conclu à titre onéreux.
        </p>
        <p>
            Le tarif des prestations, faisant l'objet du présent contrat, est déterminé en accord avec le devis dûment
            signé par le bénéficiaire. Les parties conviennent que le devis, annexé aux présentes, fait partie
            intégrante du présent contrat.
        </p>
        <p>
            Le montant fourni dans le devis concerne la réalisation de la mission de Mon Accompagnateur Rénov' incluant
            l'audit énergétique réglementaire. La mission de Mon Accompagnateur Rénov' est facturée 1666,67 € HT, soit
            2000 € TTC, montant auquel vient se déduire l'aide de l'Anah versée au mandataire financier.
        </p>
    </div>
    <div class="my-4">

        <p>
            Les montants fournis dans le devis sont détaillés comme suit:
        </p>
        <p>
            <b>Réalisation de la mission de Mon Accompagnateur Rénov' et de l'Audit Énergétique Réglementaire
                comprenant</b>
        <ul>
            <li>
                1 800 € TTC pour la réalisation de la 1 è r e visite obligatoire et la réalisation de l'audit
                énergétique
                réglementaire
            </li>
            <li>
                200 € TTC pour la réalisation de la 2 è m e visite obligatoire
            </li>
        </ul>
        <b>Aide prévisionnelle de l'Anah en fonction du plafond de ressources</b><br />
        En fonction des plafonds de ressources, le montant de l'aide de l'ANAH est de:
        <ul>

            <li>2 000 € TTC pour un bénéficiaire en catégorie BLEU (Très modestes)</li>
            <li>1 600 € TTC pour un bénéficiaire en catégorie JAUNE (Modestes)</li>
            <li>800 € TTC pour un bénéficiaire en catégorie VIOLET (Intermédiaires)</li>
            <li> 400 € TTC pour un bénéficiaire en catégorie ROSE (Supérieurs)</li>

        </ul>
        </p>
        <p>
            Le paiement des aides pour la prestation MON Accompagnateur Ré nov' (MAR) par l' ANAH aux bénéficiaires est
            fait en une seule fois à la fin de la mission de Mon Accompagnateur Rénov'. En cas de mandataire financier
            désigné par le bénéficiaire, c'est ce dernier qui recevra la prime de l'Anah pour la prestation du MAR.
        </p>
        <p>
            Un paiement du reste à charge (2 000 € TTTC moins le montant de l'aide de l'Anah) pour l'ensemble de la
            prestation est demandé à la signature du devis et ne peut être réglé que 8 jours après la date de signature
            du devis. Ce paiement du reste à charge, en fonction des plafonds de ressources du bénéficiaire définis par
            l'ANAH, est le suivant:
        </p>
        <table>

            <tr>

                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    <p> Plafond de ressources</p>
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    <p> Prestation MAR TTC</p>
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    <p> Aide ANAH TTC</p>
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    <p> Reste à charge TTC</p>
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    Très modestes {bleu)
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    2000 €
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    2000 €
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    0 €
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    Modestes {jaune)
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    2000 €
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    1600 €
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    400 €
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    Intermédiaires {violet)
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    2000 €
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    800 €
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    1200 €
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    Supérieurs (rose)
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    2000 €
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    400 €
                </td>
                <td style="max-width:22%;width:25%;" class=" border-bottom text-center">
                    1600 €
                </td>
            </tr>
        </table>
        <p>

            La rémunération est due quelles que soient les contestations liées aux litiges entre le bénéficiaire et
            le(s) entreprise(s) de travaux.
        </p>
    </div>



    <div class="my-4">
        <h3>Article 10 : Règlement des litiges </h3>


        <p>
            En cas de litige ou de différend découlant du présent contrat, les parties conviennent de rechercher une
            solution amiable avant d'engager toute action en justice.
        </p>
        <p>
            En cas de recours au médiateur, les parties sélectionneront conjointement un médiateur neutre et compétent.
            Si les parties ne parviennent pas à se mettre d'accord sur un médiateur dans un délai de 30 jours suivant la
            notification écrite d'une partie à l'autre de l'existence d'un litige, le médiateur sera choisi conformément
            aux règles de médiation en vigueur.
        </p>
        <p>
            Les coûts de la médiation seront partagés également entre les parties, sauf accord contraire entre elles.
            Chaque partie supportera ses propres frais juridiques et autres frais associés à la médiation.
        </p>
        <p>
            Les discussions et délibérations de la médiation seront confidentielles et ne pourront être utilisées dans
            une procédure judiciaire ultérieure, sauf accord contraire des parties.
        </p>
        <p>
            La médiation sera considérée comme terminée lorsque les parties auront conclu un accord écrit mettant fin au
            litige.
        </p>
        <p>
            Cette clause de médiation ne préjuge pas du droit des parties de recourir à une procédure judiciaire en cas
            d'échec de la médiation ou si une partie refuse de participer à la médiation après avoir été dûment
            informée.
        </p>
        <p>
            Pour toute médiation, {{ $dossier->client->client_title }} a désigné comme médiateur AMIDIF {Association de
            médiateurs indépendants d'ile de France dont le siège social est 1, place, de Fleurus, 77100, Meaux, Adresse
            mail: co_11tact@a_rni_dif.com)

        </p>

    </div>



    <div class="my-4">
        <h3> Article 11 Notification et élection de domicile </h3>
        <p>
            Toute correspondance ou notification à adresser à Mon Accompagnateur Rénov' ou au bénéficiaire se feront à
            leur adresse de siège social ou de domicile définis ci-dessus.
        </p>
    </div>


    <div class="my-4">
        <h3>Article 12 : Assurances </h3>
        <p> {{ $dossier->client->client_title }} est assuré en responsabilité civile d'exploitation et en
            responsabilité décennale auprès de MIC INSURANCE COMPANY, entreprise régie par le Code des assurances,
            société anonyme au capital de 50 000 000 € - Immatriculée au RCS de Paris sous le numéro 885 241 208 dont le
            siège social est situé rue de l'Amiral Hamelin- 75016 Paris. Numéro de police AXE230401 O.
        </p>
    </div>


    <div class="my-4">
        <h3>Article 13 Traitement des données personnelles </h3>


        <p>
            Les informations recueillies font l'objet d'un traitement informatique par Mon Accompagnateur Rénov'. Mon
            Accompagnateur Rénov' n'utilisera vos données que dans la mesure où cela est nécessaire pour le suivi de
            votre accompagnement, notamment le cas échéant pour le suivi de votre dossier auprès des organismes
            susceptibles d'accorder un financement.
        </p>
        <p>
            Conformément au règlement européen n° 2016/679/ UE du 27 avril 2016, le Bénéficiaire bénéficie d'un droit
            d'accès, un droit de limitation, d'un droit de rectification, d'un droit d'opposition, d'un droit à
            l'effacement, un droit à la portabilité aux informations le concernant, qu'il peut exercer en s'adressant
            par courrier à {{ $dossier->client->client_title }} dont l'adresse est le 45, avenue du Président JF
            Kennedy-{{ $dossier->client->cp }} {{ $dossier->client->ville }} ou par mail à l'adresse
            {{ $dossier->client->email }}.
        </p>
        <p>
            Le Bénéficiaire peut également, pour des motifs légitimes, s'opposer au traitement des données le concernant
            ainsi qu'à leur transmission aux organismes susceptibles d'accorder un financement.
        </p>

        <p>
            Vos données personnelles sont conservées par Mon Accompagnateur Rénov' pendant toute la durée de la demande
            d'accompagnement, puis archivées pendant 8 ans à compter de la fin de la réalisation de la prestation
            conformément aux exigences de l'Agence nationale de l'habitat (ANAH).
        </p>


        <p>
            Pendant cette période, nous mettons en place tous les moyens aptes à assurer la confidentialité et la
            sécurité de vos données personnelles, de manière à empêcher leur endommagement, effacement ou l'accès par
            tiers non autorisés. L'accès à vos données personnelles est strictement limité au Personnel administratif,
            et, le cas échéant, à nos sous- traitants et sur demande aux autorités administratives. Nos sous-traitants
            sont soumis à une obligation de confidentialité et ne peuvent utiliser qu'en conformité avec nos
            dispositions contractuelles. En dehors des cas énoncés ci-dessus, nous nous engageons à ne pas vendre,
            louer, céder ni donner accès à des tiers vos données sans votre consentement préalable.
        </p>

    </div>


    <div class="my-4">
        <h3> Article 14: Signature électronique </h3>

        <p>
            Chaque signataire s'engage par la présente à prendre toutes les mesures appropriées pour que la signature
            électronique des présentes soit effectuée par son représentant dûment autorisé.
        </p>
        <p>
            Chaque signataire reconnaît et accepte par la présente que sa signature de l'acte via le processus
            électronique susmentionné est effectuée en pleine connaissance de la technologie mise en œuvre, de ses
            conditions d'utilisation et des lois sur la signature électronique, et, en conséquence, renonce
            irrévocablement et inconditionnellement à tout droit que la partie peut avoir à engager toute réclamation
            et/ou action en justice, résultant directement ou indirectement de ou concernant la fiabilité dudit
            processus de signature électronique et/ou la preuve de son intention de prendre part à l'acte à cet égard.
        </p>
        <p>
            En outre, conformément aux dispositions de l'article 1375 du Code civil, l'obligation de remise d'un
            exemplaire original papier à chacune des Parties n'est pas nécessaire comme preuve des engagements et
            obligations de chaque Partie à cet accord. La remise d'une copie électronique de l'acte directement par un
            prestataire certifié de signature électronique à chacune des Parties constitue une preuve suffisante et
            irréfutable des engagements et obligations de chaque Partie à l'acte.
        </p>

    </div>

    <div class="my-4">

        <p><b>Contrat pour la réalisation d'une mission de Mon Accompagnateur Rénov'</b></p>
        <p> Fait, en deux exemplaires originaux le             
            @if(isset($all_data['dossiers_data']['date_1ere_visite']))
            {{ date('d/m/Y', strtotime(str_replace('/','-',$all_data['dossiers_data']['date_1ere_visite']))) }}
            @else 
            {{ date('d/m/Y', strtotime('now')) }}
            @endif, à {{ $dossier->beneficiaire->ville }}</p>
    </div>
    <div>

        <table style="width:100%">
            <tr>
                <td style="border:none;width:50%">
                    <p><b>Le bénéficiaire</b></p>
                    {{-- <p>Signature précédé de la mention manuscrite <br/><i>"Bon pour accord et exécution du devis» </i>
                    </p> --}}
                    @if(isset(($all_data['form_data'][13]['signature_beneficiaire'])) && file_exists(storage_path('app/public/' . json_decode($all_data['form_data'][13]['signature_beneficiaire'])[0])))

                    <img style="max-width:150px;margin-top:-10px" src="{{ asset('storage/' . json_decode($all_data['form_data'][13]['signature_beneficiaire'])[0]) }}" alt="Logo">
        
                    @endif
                </td>


                <td style="border:none;width:50%;padding-left:45px;">
                    <p><b>Mon Accompagnateur Rénov'</b> <br />{{ $dossier->client->client_title }}  </p>
                    <p>{{ $dossier->client->representant ?? '' }} </p>
                    @if(isset($dossier->client->signature) && file_exists(storage_path('app/public/' . $dossier->client->signature)))

                    <img style="max-width:150px;margin-top:-10px" src="{{ asset('storage/' . $dossier->client->signature) }}" alt="Logo">
        
                    @endif


                </td>

            </tr>
        </table>

    </div>
</page>

<page backtop="25mm" backleft="10mm" backright="10mm" backimg="" backbottom="10mm">
    @include('pdf.header')
    @include('pdf.footer')

    <div class="my-4">
        <h3>Annexe • Panorama des aides de l' ANAH </h3>
        <h4>Plafond de ressources Ma Prime Rénov' </h4>

        <table>

            <tr>

                <td colspan="5" class=" border-bottom text-center">
                    <p style="text-align:center"> <b>PLAFOND DE RESSOURCES en ÎLE de FRANCE au 1er janvier 2024</b> </p>
                </td>
            
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    Nombre de personnes composant le ménage 
                </td>
                <td style="max-width:22%;width:20%;background:cyan" class=" border-bottom text-center">
                    Ménages aux revenus très modestes
                </td>
                <td style="max-width:22%;width:20%;background:yellow" class=" border-bottom text-center">
                    Ménages aux revenus modestes
                </td>
                <td style="max-width:22%;width:20%;background:pink" class=" border-bottom text-center">
                    Ménages aux revenus intermédiaires
                </td>
                <td style="max-width:22%;width:20%; background:rgb(255, 220, 226) " class=" border-bottom text-center">
                    Ménages aux revenus supérieurs
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    1
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                   23541 €
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    28657 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    40018 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 40018 € 
                </td>
            </tr> 

            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    2
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    34551 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    42058 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    58827 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 58827 € 
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    3
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    41493 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    50513 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    70382 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 70382 € 
                </td>
            </tr>

            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    4
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    48447 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    58981 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    82839 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 82839 € 
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    5
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    55427 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    67473 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    94844 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 94844 € 
                </td>
            </tr>

            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    Par personne supplémentaire
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    +6970€
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    +8486€
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    +12006€
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    +12006€
                </td>
            </tr>

        </table>


        <table>

            <tr>

                <td colspan="5" class=" border-bottom text-center">
                    <p style="text-align:center"> <b>PLAFOND DE RESSOURCES pour les AUTRES COLLECTIVITES au 1er janvier 2024 </b> </p>
                </td>
            
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    Nombre de personnes composant le ménage 
                </td>
                <td style="max-width:22%;width:20%;background:cyan" class=" border-bottom text-center">
                    Ménages aux revenus très modestes
                </td>
                <td style="max-width:22%;width:20%;background:yellow" class=" border-bottom text-center">
                    Ménages aux revenus modestes
                </td>
                <td style="max-width:22%;width:20%;background:pink" class=" border-bottom text-center">
                    Ménages aux revenus intermédiaires
                </td>
                <td style="max-width:22%;width:20%; background:rgb(255, 220, 226) " class=" border-bottom text-center">
                    Ménages aux revenus supérieurs
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    1
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    17009 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    21805 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                   30549 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 30549 € 
                </td>
            </tr> 

            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    2
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    24875 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    31889 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    44907 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 44907 € 
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    3
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    29917 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    38349 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    54071 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 54071 € 
                </td>
            </tr>

            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    4
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    34948 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    44802 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    63235 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 63235 € 
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    5
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    40002 €  
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    51281 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    72400 € 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    > 72400 € 
                </td>
            </tr>

            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    Par personne supplémentaire
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    +5045€
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    +6462€
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                   +9165€
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                   +9165€
                </td>
            </tr>

        </table>

<h3>Financement de Mon Accompagnateur Rénov' en fonction des plafonds de ressources</h3>
        <table>

    
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    Plafond des dépenses éligibles
                </td>
                <td style="max-width:22%;width:20%;background:cyan" class=" border-bottom text-center">
                    Ménages aux revenus très modestes
                </td>
                <td style="max-width:22%;width:20%;background:yellow" class=" border-bottom text-center">
                    Ménages aux revenus modestes
                </td>
                <td style="max-width:22%;width:20%;background:pink" class=" border-bottom text-center">
                    Ménages aux revenus intermédiaires
                </td>
                <td style="max-width:22%;width:20%; background:rgb(255, 220, 226) " class=" border-bottom text-center">
                    Ménages aux revenus supérieurs
                </td>
            </tr>
            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    2000 € TTC 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                   100%
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    80% 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    40%
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    20% 
                </td>
            </tr> 

            <tr>

                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    Déduction 
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    -2 000 €TTC
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    -1 600 €TTC
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    -800 €TTC
                </td>
                <td style="max-width:22%;width:20%;" class=" border-bottom text-center">
                    -400 €TTC
                </td>
            </tr> 

        </table>

    </div>

</page>
