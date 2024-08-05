@extends('layouts.app')

@section('content')
    <div class="container">
        <h4>Liste des RDV (<span id="count"></span> résultats)</h4>

        <div class="row form-group">
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Client</label>
                <input type="text" class="form-control filter" name="search" id="search"
                    placeholder="Filtrer par nom de client, adresse, ou téléphone (3 caracteres minimum)">
            </div>
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Statut du rdv</label>
                <select class="form-control select-filter" name="status" id="status">
                    <option value="">Filtrer</option>
                    <option value="-1">Dossiers sans RDV</option>
                    @foreach ($status as $statut)
                        <option value="{{ $statut->id }}">{{ $statut->rdv_desc }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Installateur</label>
                <select class="form-control select-filter" name="installateur" id="installateur">
                    <option value="">Filtrer par installateur</option>
                    <option value="-1">Dossiers sans installateur</option>

                    @foreach ($installateurs as $installateur)
                        <option value="{{ $installateur->id }}">{{ $installateur->client_title }}</option>
                    @endforeach

                </select>
            </div>

            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Accompagnateur</label>
                <select class="form-control select-filter" name="mar" id="mar">
                    <option value="">Filtrer par Accompagnateur</option>
                    <option value="-1">Dossiers sans Accompagnateur</option>

                    @foreach ($mars as $mar)
                        <option value="{{ $mar->id }}">{{ $mar->client_title }}</option>
                    @endforeach

                </select>
            </div>

            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Mandataire financier</label>
                <select class="form-control select-filter" name="mandataire_financier" id="mandataire_financier">
                    <option value="">Filtrer par mandataire</option>
                    <option value="-1">Dossiers sans mandataire</option>

                    @foreach ($financiers as $mandataire_financier)
                        <option value="{{ $mandataire_financier->id }}">{{ $mandataire_financier->client_title }}</option>
                    @endforeach

                </select>
            </div>

        </div>

        <table id="dossiersTable" class="table table-bordered responsive-table table-responsive">
            <thead>
                <tr>
                    <th>Dossier</th>
                    <th>RDV</th>
                    <th>Installateur</th>
                    <th>Accompagnateur</th>
                    <th>Mandataire financier</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div id="loader" style="display:none;">Loading...</div>

@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/daygrid/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/timegrid/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/interaction/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/fr.js"></script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzcaFvxwi1XLyRHmPRnlKO4zcJXPOT5gM&libraries=marker&callback=initMap">
    </script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script>
 $(document).ready(function() {
    var table = $('#dossiersTable').DataTable({
        
        dom: '<"top"><"bottom">',
                language: {
                    url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/fr-FR.json',
                },
                pageLength: -1,  // Set the default page length here

        columns: [
            { title: "Bénéficiaire" },
            { title: "RDV" },
            { title: "Installateur" },
            { title: "MAR" },
            { title: "Mandataire Financier" }
        ]
    });

    function fetchDossiers() {
        var apiToken = '{{ $apiToken }}';
        var data = {};
        $('#loader').show();
        $('#dossiersTable').hide()
        $('.filter, .select-filter').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        console.log(data);
        $.ajax({
            url: 'api/dossiers', // Make sure to update the route as per your configuration
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + apiToken
            },
            data: data,
            success: function(data) {
                console.log(data);
                populateTable(data);

                // Redraw the DataTable
                table.clear().rows.add(populateTable(data)).draw();
                $('#loader').hide();
                $('#dossiersTable').show()

            },
            error: function(error) {
                console.log('Error:', error);
                $('#loader').hide();
                $('#dossiersTable').show()

            }
        });
    }

    function populateTable(dossiers) {
        var tableData = [];
        var count=0;
        $.each(dossiers, function(index, dossier) {
            var row = [];

            if (dossier.beneficiaire) {
                var beneficiaireInfo = dossier.beneficiaire.nom + ' ' + dossier.beneficiaire.prenom + '<br/>' +
                    (dossier.beneficiaire.numero_voie ?? '') + ' ' + dossier.beneficiaire.adresse + '<br/>' +
                    dossier.beneficiaire.cp + ' ' + dossier.beneficiaire.ville + '<br/>' + dossier.beneficiaire.telephone;
                row.push(beneficiaireInfo);
            } else {
                row.push('');
            }

            if (dossier.get_rdv) {
                var rdvInfo = '';
                dossier.get_rdv.forEach(rdv => {
                    const date = new Date(rdv.date_rdv);

                    const dateOptions = {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    };
                    const timeOptions = {
                        hour: '2-digit',
                        minute: '2-digit'
                    };

                    const formattedDate = date.toLocaleDateString('fr-FR', dateOptions);
                    const formattedTime = date.toLocaleTimeString('fr-FR', timeOptions);
                    const formattedDateTime = `${formattedDate} à ${formattedTime}`;

                    rdvInfo += '<div class="show_rdv btn btn-' + (rdv.status ? rdv.status.rdv_style : '') + '">RDV MAR' + rdv.type_rdv + ' du ' +
                        formattedDateTime + ' Statut : ' + (rdv.status ? rdv.status.rdv_desc : '') + '</div><br/>';
                });
                row.push(rdvInfo);
            } else {
                row.push('');
            }

            if (dossier.installateur) {
                row.push(dossier.installateur.client_title);
            } else {
                row.push('');
            }

            if (dossier.mar) {
                row.push(dossier.mar.client_title);
            } else {
                row.push('');
            }

            if (dossier.mandataire_financier>0) {
                row.push(dossier.mandataire_financier.client_title);
            } else {
                row.push('');
            }

            tableData.push(row);
            count=count+1;
        });
        console.log(count)
        $('#count').html(count)
        return tableData;
    }

    // Fetch dossiers on page load
    fetchDossiers();

    $('.select-filter').on('change', function() {
        fetchDossiers();
    });

    $('.filter').on('keydown keyup blur change', function() {
        fetchDossiers();
    });
});



    </script>
@endsection
