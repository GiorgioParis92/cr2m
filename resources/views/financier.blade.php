@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Liste des dossiers financiers</h1>

        <table id="financier-table" class="table table-bordered table-striped">
            <thead>
                <!-- Original Header Row -->
                <tr>
                    <th>Bénéficiaire</th>
                    <th>Étape</th>
                    <th>Étape</th>
                    <th>Installateur</th>
                    <th>Date Dépôt</th>
                    <th>Date Octroi</th>
                    <th>Subvention</th>
                    <th>Date Paiement Anah</th>

                </tr>

                <!-- Filter Row -->
                <tr>
                    <!-- Bénéficiaire filter: Text input -->
                    <th>
                        <input type="text" class="form-control form-control-sm" placeholder="Rechercher Bénéficiaire" />
                    </th>

                    <!-- Étape filter: Dropdown -->
                    <th>
                        <select class="form-select form-select-sm" id="select-step">
                            <option value="">Toutes les étapes</option>
                            @foreach ($etapeNames as $etape)
                                <option value="{{ $etape->etape_icon . ' - ' . $etape->etape_desc }}">
                                    {{ $etape->etape_icon . ' - ' . $etape->etape_desc }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th></th>
                    <!-- No filters for the remaining columns -->
                    <th>
                        <select class="form-select form-select-sm" id="select-client">
                            <option value="">Tous les clients</option>
                            @foreach ($liste_clients as $client)
                                <option value="{{ $client->client_title }}">
                                    {{ $client->client_title }}</option>
                            @endforeach
                        </select>

                    </th>
              
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
   
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($financier as $row)
                    <tr>
                        <td>
                            <a target="_blank" href="{{ url('dossier/show/' . $row->folder) }}">
                                {{ $row->beneficiaire_nom . ' ' . $row->beneficiaire_prenom }}
                            </a>
                        </td>
                        <td>{{ $row->etape_icon . ' - ' . $row->etape_desc }}</td>
                        <td>{{ $row->order_column }}</td>
                        <td>{{ $row->client_title }}</td>
                        <td>{{ $row->date_depot }}</td>
                        <td>{{ $row->date_octroi }}</td>
                        <td>{{ $row->subvention ? number_format((float) $row->subvention, 2) : '' }}</td>
                        <td>{{ $row->date_paiement_anah }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with Buttons
        var table = $('#financier-table').DataTable({
            dom: 'Bfrtip', // Include buttons in the DOM
            buttons: [
                {
                    extend: 'excel',
                    text: 'Exporter Excel',
                    exportOptions: {
                        columns: ':visible:not(.no-export)' // Exclude columns with class 'no-export'
                    }
                },
                {
                    extend: 'csv',
                    text: 'Exporter CSV',
                    exportOptions: {
                        columns: ':visible:not(.no-export)' // Exclude columns with class 'no-export'
                    }
                },
                {
                    extend: 'pdf',
                    text: 'Exporter PDF',
                    exportOptions: {
                        columns: ':visible:not(.no-export)' // Exclude columns with class 'no-export'
                    }
                }
            ],
            orderCellsTop: true,
            fixedHeader: true,
            responsive: true,
            "pageLength": 50,
            order: [
                [2, 'desc']
            ],
            columnDefs: [
                {
                    targets: 2,
                    visible: false,
                    searchable: false
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
            }
        });

        // Append filters and update export buttons dynamically
        $('#select-step').on('change', function() {
            let val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(1).search(val ? '^' + val + '$' : '', true, false).draw();
        });

        $('#select-client').on('change', function() {
            let val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(3).search(val ? '^' + val + '$' : '', true, false).draw();
        });

        $('#financier-table thead tr:eq(1) th').each(function(i) {
            let headerCell = $(this);
            let input = headerCell.find('input');

            if (input.length > 0) {
                input.on('keyup change', function() {
                    table.column(i).search(this.value).draw();
                });
            }
        });
    });
</script>
@endsection
