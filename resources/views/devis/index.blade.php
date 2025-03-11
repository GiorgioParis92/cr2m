@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Devis</h1>
    <a href="{{ route('devis.create') }}" class="btn btn-primary">Create Devis</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Client</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devis as $devi)
            <tr>
                <td>{{ $devi->devis_id }}</td>
                <td>{{ $devi->devis_name }}</td>
                <td>{{ $devi->client->client_title }}</td>
                <td>{{ $devi->amount }}</td>
                <td>
                    <a href="{{ route('devis.edit', $devi->devis_id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('devis.destroy', $devi->devis_id) }}" method="POST" style="display:inline-block;">
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
