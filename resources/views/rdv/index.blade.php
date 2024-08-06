@extends('layouts.app')

@section('content')
    <div class="container">
        <h4>Liste des RDV <span id="count_message">(<span id="count"></span> résultats)</span></h4>

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
                <label class="mr-sm-2">Statut du dossier</label>
                <select class="form-control select-filter" name="dossier_status" id="dossier_status">
                    <option value="">Filtrer</option>
                    @foreach ($dossier_status as $statut)
                        <option value="{{ $statut->id }}">{{ $statut->status_desc }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row form-group">
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Date de début</label>
                <input type="text" class="form-control filter datepicker" name="start" id="start" placeholder="">
            </div>

            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Date de fin</label>
                <input type="text" class="form-control filter datepicker" name="end" id="end" placeholder="">
            </div>

        </div>
        <div class="row form-group">
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

    <div id="loader" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>

<style>
   div#loader {
    width: 100%;
    text-align: center;
    background: #6666661c;
    min-height: 20vh;
    line-height: 33vh;
} 
.lds-ring,
.lds-ring div {
  box-sizing: border-box;
}
.lds-ring {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 64px;
  height: 64px;
  margin: 8px;
  border: 8px solid currentColor;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: currentColor transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
  animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
  animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
  animation-delay: -0.15s;
}
@keyframes lds-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}


</style>
    @endsection

@section('scripts')
    
    <script>
        $(document).ready(function() {

          

            var table = $('#dossiersTable').DataTable({
                buttons: [
            {
                extend: 'copy',
                className: 'btn btn-primary',

                filename: 'Extraction RDV'
            },
            {
                extend: 'csv',
                className: 'btn btn-primary',

                filename: 'Extraction RDV'
            },
            {
                extend: 'excel',
                className: 'btn btn-primary',

                filename: 'Extraction RDV Excel',
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row c[r^="A"] t', sheet).each(function() {
                        var text = $(this).text();
                        if (text.indexOf('<br/>') > -1) {
                            $(this).text(text.replace(/<br\s*\/?>/gi, '\n'));
                        }
                    });
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape', // Change to 'portrait' if needed
                pageSize: 'A4', // Change to other sizes if needed
                filename: 'Extraction RDV',
                title: 'Extraction RDV',
                customize: function (doc) {
                    doc.styles.title = {
                        color: 'red',
                        fontSize: '20',
                        alignment: 'center'
                    }
                }
            },
            {
                extend: 'print',
                title: 'Extraction',
                className: 'btn btn-primary',

                customize: function (win) {
                    $(win.document.body).css('font-size', '10pt');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                    
                    // Adding landscape orientation style
                    var css = '@page { size: landscape; }',
                        head = win.document.head || win.document.getElementsByTagName('head')[0],
                        style = win.document.createElement('style');
                    
                    style.type = 'text/css';
                    style.media = 'print';

                    if (style.styleSheet) {
                      style.styleSheet.cssText = css;
                    } else {
                      style.appendChild(win.document.createTextNode(css));
                    }

                    head.appendChild(style);
                }
            }
        ],
                dom: '<"top"Bfrtip><"bottom">',

                language: {
                    url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/fr-FR.json',
                    emptyTable: "Appliquez au moins un filtre"
                },
                pageLength: -1, // Set the default page length here

                columns: [{
                        title: "Bénéficiaire"
                    },
                    {
                        title: "RDV"
                    },
                    {
                        title: "Statut du dossier"
                    },
                    {
                        title: "Installateur"
                    },
                    {
                        title: "MAR"
                    },
                    {
                        title: "Mandataire Financier"
                    }
                ]
            });

            function fetchDossiers() {
                var apiToken = '{{ $apiToken }}';
                var data = {};
                $('#loader').show();
                $('#dossiersTable').hide()
                var filter = false;
                data['filter'] = false;

                $('.filter, .select-filter').each(function() {
                    data[$(this).attr('name')] = $(this).val();
                    if ($(this).val() != '') {
                        filter = true;
                        data['filter'] = true;

                    }
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
                        if (data == 'no_filter') {
                            table.clear().draw();
                            $('#loader').hide();
                            $('#count_message').hide();
                            $('#dossiersTable').show()
                            return;
                        }
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
                var count = 0;
                $.each(dossiers, function(index, dossier) {
                    var row = [];

                    if (dossier.beneficiaire) {
                        var beneficiaireInfo = '<a href="dossier/show/' + dossier.folder + '"><b>' + dossier
                            .beneficiaire.nom + ' ' + dossier.beneficiaire.prenom + '</b></a><br/>' +
                            (dossier.beneficiaire.numero_voie ?? '') + ' ' + dossier.beneficiaire.adresse +
                            '<br/>' +
                            dossier.beneficiaire.cp + ' ' + dossier.beneficiaire.ville + '<br/>' + dossier
                            .beneficiaire.telephone;
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

                            rdvInfo += '<a href="dossier/show/' + dossier.folder +
                                '"><div class="show_rdv btn btn-' + (rdv.status ? rdv.status
                                    .rdv_style : '') + '">RDV MAR' + rdv.type_rdv + ' du ' +
                                formattedDateTime + ' Statut : ' + (rdv.status ? rdv.status
                                    .rdv_desc : '') + '</div></a><br/>';
                        });
                        row.push(rdvInfo);
                    } else {
                        row.push('');
                    }

                    if (dossier.status) {
                        row.push(dossier.status.status_desc);
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

                    if (dossier.mandataire_financier) {
                        if(dossier.mandataire_financier.id>0) {
                            row.push(dossier.mandataire_financier.client_title );
                        } else {
                            row.push('');
                        }
                        

                    } else {
                        row.push('');
                    }

                    tableData.push(row);
                    count = count + 1;
                });
                console.log(count)
                $('#count').html(count)
                if(count>0) {
                    $('#count_message').show();

                }
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
