@extends('layouts.app')

@section('content')
    <div class="container">
        <h4>Beneficiaires</h4>
        <a href="{{ route('beneficiaires.create') }}" class="btn btn-primary mb-3">{{ __('forms.add_beneficiaire') }}</a>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-bordered responsive-table table-responsive">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>First Name</th>
                    <th>Address</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Menage MPR</th>
                    <th>Heating</th>
                    <th>Occupation</th>
                    <th>Dossiers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($beneficiaires as $beneficiaire)
                    <tr>
                        <td data-label="Name">{{ $beneficiaire->nom }}</td>
                        <td data-label="First Name">{{ $beneficiaire->prenom }}</td>
                        <td data-label="Address">
                            {{ $beneficiaire->adresse }}<br />
                            {{ $beneficiaire->cp }} {{ $beneficiaire->ville }}

                        <td data-label="Phone">{{ $beneficiaire->telephone }}<br />{{ $beneficiaire->telephone_2 }}</td>
                        <td data-label="Email">{{ $beneficiaire->email }}</td>
                        <td data-label="Menage MPR">{{ $beneficiaire->menage_mpr }}</td>
                        <td data-label="Heating">{{ $beneficiaire->chauffage }}</td>
                        <td data-label="Occupation">{{ $beneficiaire->occupation }}</td>
                        <td data-label="Dossiers">
                            @foreach ($beneficiaire->dossiers as $dossier)
                            <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-primary">
                                {!! $dossier->fiche->fiche_name.'<br/>' ?? 'N/A' !!}
                            </a>
                            
                            @endforeach
                        </td>
                        <td data-label="Actions">
                            <a href="{{ route('beneficiaires.edit', $beneficiaire->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('beneficiaires.destroy', $beneficiaire->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
