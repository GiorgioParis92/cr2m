@extends('layouts.app')

@section('content')
    <div class="container">
        <h4>Dossiers</h4>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table id="dossiersTable" class="table table-bordered responsive-table table-responsive">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Menage MPR</th>
                    <th>Chauffage</th>
                    <th>Occupation</th>
                    <th></th>
                </tr>
                <tr>
                    <th><input type="text" data-column="0" placeholder="Filter Client"></th>
                    <th><input type="text" data-column="1" placeholder="Filter Adresse"></th>
                    <th><input type="text" data-column="2" placeholder="Filter Téléphone"></th>
                    <th><input type="text" data-column="3" placeholder="Filter Email"></th>
                    <th><input type="text" data-column="4" placeholder="Filter Menage MPR"></th>
                    <th><input type="text" data-column="5" placeholder="Filter Chauffage"></th>
                    <th><input type="text" data-column="6" placeholder="Filter Occupation"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dossiers as $dossier)
                    <tr>
                        <td>{{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }}</td>
                        <td>
                            {{ $dossier->beneficiaire->adresse }}<br />
                            {{ $dossier->beneficiaire->cp }} {{ $dossier->beneficiaire->ville }}
                        </td>
                        <td>{{ $dossier->beneficiaire->telephone }}<br />{{ $dossier->beneficiaire->telephone_2 }}</td>
                        <td>{{ $dossier->beneficiaire->email }}</td>
                        <td>{{ $dossier->beneficiaire->menage_mpr }}</td>
                        <td>{{ $dossier->beneficiaire->chauffage }}</td>
                        <td>{{ $dossier->beneficiaire->occupation }}</td>
                        <td>
                            <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-primary">
                                {!! $dossier->fiche->fiche_name.'<br/>' ?? 'N/A' !!}
                            </a>
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
            var table = $('#dossiersTable').DataTable();

            // Generalized search function
            function bindSearchInputs() {
                $('input[type="text"][data-column]').on('keyup change', function() {
                    var column = $(this).data('column');
                    table.column(column).search(this.value).draw();
                });
            }

            // Bind search inputs
            bindSearchInputs();
        });
    </script>
@endsection
