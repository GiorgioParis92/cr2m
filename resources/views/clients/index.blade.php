@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4>Partenaires</h4>
                <a href="{{ route('clients.create') }}" class="btn btn-primary mb-3">Nouveau partenaire</a>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <table class="table table-bordered responsive-table">
                    <thead>
                        <tr>
                            <th>Raison sociale</th>
                            <th>Type</th>
                            <th>Adresse</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td data-label="{{ __('forms.client_title') }}">
                                    @if (Storage::disk('public')->exists($client->main_logo))
                                        <img class="logo_table" src="{{ asset('storage/' . $client->main_logo) }}">
                                    @endif
                                    <b>{{ $client->client_title }}</b>
                                </td>
                                <td data-label="Type">{{ $client->type->type_desc }}</td>
                           
                                <td data-label="{{ __('forms.address') }}">
                                    {{ $client->adresse }}<br />
                                    {{ $client->cp }} {{ $client->ville }}
                                </td>
                                <td data-label="Email">{{ $client->email }}</td>
                                <td data-label="Telephone">{{ $client->telephone }}</td>
                                <td data-label="Actions">
                                    <a href="{{ route('clients.edit', $client->id) }}"
                                        class="btn btn-success"><i class="fa fa-save"></i></a>
                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
