@extends('layouts.app')

@section('content')
    <div class="container">
        <h4>Dossiers</h4>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row form-group">


            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Client</label>
                <input type="text" class="form-control " data-column="2,3"
                    placeholder="Filtrer par nom de client,adresse, ou téléphone">
            </div>


            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Précarité</label>
                <select class="form-control" data-column="5">
                    <option value="">Filtrer par type de ménage</option>
                    <option value="bleu">Bleu</option>
                    <option value="jaune">Jaune</option>
                </select>
            </div>
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Etape</label>
                <select class="form-control" data-column="9">
                    <option value="">Filtrer par étape</option>

                    @foreach ($etapes as $etape)
                        <option value="{{ $etape->order_column}}">{{ $etape->etape_desc }}</option>
                    @endforeach

                </select>
            </div>
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Statut</label>
                <select class="form-control" data-column="11">
                    <option value="">Filtrer par statut</option>

                    @foreach ($status as $etape)
                        <option value="{{ $etape->status_desc }}">{{ $etape->status_desc }}</option>
                    @endforeach

                </select>
            </div>

            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Acoompagnateur</label>
                <select class="form-control" data-column="13">
                    <option value="">Filtrer par accompagnateur</option>

                    @foreach ($mars as $mar)
                        <option value="{{ $mar->id }}">{{ $mar->client_title }}</option>
                    @endforeach

                </select>
            </div>

            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Mandataire</label>
                <select class="form-control" data-column="15">
                    <option value="">Filtrer par mandataire</option>

                    @foreach ($financiers as $financier)
                        <option value="{{ $financier->id }}">{{ $financier->client_title }}</option>
                    @endforeach

                </select>
            </div>
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Installateur</label>
                <select class="form-control" data-column="17">
                    <option value="">Filtrer par installateur</option>

                    @foreach ($installateurs as $installateur)
                        <option value="{{ $installateur->id }}">{{ $installateur->client_title }}</option>
                    @endforeach

                </select>
            </div>
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Type de dossier</label>
                <select class="form-control" data-column="19">
                    <option value="">Filtrer par type de dossier</option>

                    @foreach ($fiches as $fiche)
                        <option value="{{ $fiche->id }}">{{ $fiche->fiche_name }}</option>
                    @endforeach

                </select>
            </div>


        </div>

        <table id="dossiersTable" class="table table-bordered responsive-table table-responsive">
            <thead>
                <tr>
                    <th style="max-width:10%">Date</th>
                    <th style="max-width:10%">Date str</th>
                    <th style="max-width:10%">Client</th>
                    <th style="max-width:10%">Coordonnées</th>

                    <th style="max-width:10%">Ménage</th>
                    <th style="max-width:10%">Détails</th>
                    <th style="max-width:10%">Détails</th>
                    <th style="max-width:10%">Détails</th>
                    <th style="max-width:10%">Etape</th>
                    <th style="max-width:10%">Détails</th>
                    <th style="max-width:10%">Statut</th>
                    <th style="max-width:10%">Statut</th>
                    <th style="max-width:10%">Accompagnateur</th>

                    <th style="max-width:10%">Mandataire</th>
                    <th style="max-width:10%">Mandataire</th>
                    <th style="max-width:10%">Type de dossier</th>
                    <th style="max-width:10%">Installateur</th>
                    <th style="max-width:10%">Détails</th>
                    <th style="max-width:10%">Détails</th>
                    <th style="max-width:10%">Détails</th>


                </tr>

            </thead>
            <tbody>
                @foreach ($dossiers as $dossier)
                    <tr>
                        <td>{{ format_date($dossier->created_at) }}</td>
                        <td>{{ strtotime_date($dossier->created_at) }}</td>
                        <td><b><a href="{{ route('dossiers.show', $dossier->folder) }}">{{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }}</a></b><br/>
                            <i>Ménage :{{ $dossier->beneficiaire->menage_mpr }}</i>

                        </td>
                        <td>
                            {{ $dossier->beneficiaire->adresse }}<br />
                            {{ $dossier->beneficiaire->cp }} {{ $dossier->beneficiaire->ville }}<br />
                            Tél :
                            {{ $dossier->beneficiaire->telephone ?? '' }}<br />{{ $dossier->beneficiaire->telephone_2 ?? '' }}<br />
                            <span class="font-italic">email : {{ $dossier->beneficiaire->email }}</span>
                        </td>

                        <td>
                            Type de ménage :{{ $dossier->beneficiaire->menage_mpr }}<br />
                            Type de chauffage : {{ $dossier->beneficiaire->chauffage }}<br />
                            Occupation : {{ $dossier->beneficiaire->occupation }}
                        </td>
                        <td>
                            {{ $dossier->beneficiaire->menage_mpr }}
                        </td>
                        <td>
                            {{ $dossier->beneficiaire->chauffage }}
                        </td>
                        <td>{{ $dossier->beneficiaire->occupation }}</td>
                        <td>
                            <a style="max-width:80px"  href="{{ route('dossiers.show', $dossier->folder) }}">
                                <span class="badge badge-primary badge_button">{{ ($dossier->etape->order_column)+1 }}</span>

                                <div style="    margin-top: 13px;
    max-width: 80px;
    text-wrap: wrap;
    font-size: 9px;
    padding: 8px !important;
    background-size: 0;
    /* margin: auto !important; */
    padding-top: 13px !important;    width: 100%;
    max-width: 100%;"
                                    class="btn btn-{{ $dossier->etape->etape_style ?? 'default' }}">

                                    {{ $dossier->etape->etape_desc ?? '' }}

                                </div>
                            </a>
                        </td>

                        <td>
                            {{ $dossier->etape->order_column ?? '' }}
                        </td>
                        <td>
                            <a  href="{{ route('dossiers.show', $dossier->folder) }}">
                                <div style="    margin-top: 12px; width: 100%;    max-width: 100%;"
                                    class="btn btn-{{ $dossier->status->status_style ?? 'default' }}">

                                    {{ $dossier->status->status_desc ?? '' }}

                                </div>
                            </a>
                        </td>

                        <td>
                            {{ $dossier->status->status_desc ?? '' }}
                        </td>

                        <td class="text-center">
                            @if(isset($dossier->mar))

                            @if (Storage::disk('public')->exists($dossier->mar->main_logo))
                                <img class="logo_table" src="{{ asset('storage/' . $dossier->mar->main_logo) }}">
                            @endif
                            {{ $dossier->mar->client_title }}
                            @endif
                        </td>
                        <td>
                            @if(isset($dossier->mar))

                            {{ $dossier->mar->id }}

                            @endif
                        </td>

                        <td class="text-center">
                            @if(isset($dossier->mandataire_financier))

                            @if (isset($dossier->mandataire_financier->main_logo) &&
                                    Storage::disk('public')->exists($dossier->mandataire_financier->main_logo))
                                <img class="logo_table"
                                    src="{{ asset('storage/' . $dossier->mandataire_financier->main_logo) }}">
                            @endif
                            {{ $dossier->mandataire_financier->client_title ?? 'Aucun' }}
                            @endif
                        </td>
                        <td>
                            @if(isset($dossier->mandataire_financier))

                            {{ $dossier->mandataire_financier->id ?? '' }}

                            @endif
                        </td>
                        <td>
                            @if(isset($dossier->installateur))
                            @if (Storage::disk('public')->exists($dossier->installateur->main_logo))
                                <img class="logo_table" src="{{ asset('storage/' . $dossier->installateur->main_logo) }}">
                            @endif
                            {{ $dossier->installateur->client_title ?? '' }}
                            @endif
                        </td>
                        <td>
                            @if(isset($dossier->installateur))

                            {{ $dossier->installateur->id ?? '' }}
                            @endif
                        </td>
                        <td>
                            <a  href="{{ route('dossiers.show', $dossier->folder) }}" class="btn btn-primary">
                                {!! $dossier->fiche->fiche_name . '<br/>' ?? 'N/A' !!}
                            </a>
                        </td>
                        <td>

                            {{ $dossier->fiche->id }}

                        </td>



                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#dossiersTable').DataTable({
                columnDefs: [{
                        targets: [1,4, 5, 6, 7, 9, 11, 13, 15, 17, 19],
                        visible: false
                    },


                ],
                dom: '<"top"l><"bottom"><"clear">',
                language: {
                    lengthMenu: ' _MENU_ lignes'
                }

            });

            $(document).ready(function() {
                var table = $('#dossiersTable').DataTable();

                // Enhanced search function
                function bindSearchInputs() {
                    $('input[type="text"][data-column], select[data-column]').on('keyup change',
                        function() {
                            var columns = $(this).data('column').toString().split(',');
                            var searchTerm = this.value.toLowerCase();

                            if (columns.length > 1) {
                                // Clear previous searches
                                columns.forEach(function(col) {
                                    table.column(col).search('');
                                });

                                // Apply new search term to each specified column using custom search function
                                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                                    for (var i = 0; i < columns.length; i++) {
                                        if (data[columns[i]].toLowerCase().includes(
                                                searchTerm)) {
                                            return true;
                                        }
                                    }
                                    return false;
                                });

                                table.draw();

                                // Remove custom search to avoid stacking of filters
                                $.fn.dataTable.ext.search.pop();
                            } else {
                                var column = columns[0];
                                table.column(column).search(this.value).draw();
                            }
                        });
                }

                // Bind search inputs
                bindSearchInputs();
            });


        });
    </script>
@endsection
