@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4>Utilisateurs</h4>
                <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Nouvel utilisateur</a>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <table class="table datatable table-bordered responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Type d'utilisateur</th>
                            <th>Email</th>

                            <th>Téléphone</th>
                            <th>Client</th>
                            @if(auth()->user()->client_id==0)
                            <th>Activation</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td data-label="">
                      
                                    <a href="{{ route('users.edit', $user->id) }}">{{ $user->id }}</a>
                                 </td>
                                <td data-label="">
                      
                                   <a href="{{ route('users.edit', $user->id) }}">{{ $user->name }}</a>
                                </td>
                                <td data-label="Type">{{ $user->type->type_desc ?? '' }}</td>
                                <td data-label="Type">{{ $user->email }}</td>
                           
                                <td data-label="">
                                    {{ $user->phone}}
                                </td>
                                <td data-label="">
                                    {{ isset($user->client->client_title) ? ($user->client->client_title.' ('.$user->client->type->type_desc.')') : ''}}
                                </td>
                                @if(auth()->user()->client_id==0)
                                <td data-label="">
                                    @if($user->activation==1)
                                    <div class="btn btn-success">Activé</div>
                                    @else
                                    <div class="btn btn-danger">Désactivé</div>

                                    @endif

                                </td>
                                @endif
                                <td data-label="Actions">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="btn btn-success"><i class="fa fa-save"></i></a>

                                        <a href="{{ route('users.edit-password', $user->id) }}"
                                            class="btn btn-warning"><i class="fa fa-key"></i></a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
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
