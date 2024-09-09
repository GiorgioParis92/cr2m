@extends('layouts.app')
@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container mt-5">
    <h2>Create User</h2>
    <form action="/user/store" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name:</label>
            <input value="{{ old('name') }}" type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input value="{{ old('email') }}" type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="client_id">Type d'utilisateur</label>
            <select required class="form-control" id="type_id" name="type_id" >
            <option value="">Choisir un type d'utilisateur</option>
            @foreach($types as $type)
            <option @if(old('type_id')==$type->id) selected @endif value="{{$type->id}}">{{$type->type_desc}}</option>
            @endforeach
            </select>
        </div>
        @if(auth()->user()->client_id==0)
        <div class="form-group">
            <label for="client_id">Client associé</label>
            <select  class="form-control" id="client_id" name="client_id" >
            <option value="">Choisir un client</option>
            @foreach($clients as $client)
            <option @if(old('client_id')==$client->id) selected @endif  value="{{$client->id}}">{{$client->client_title}}</option>
            @endforeach
            </select>
        </div>  
        @else
        <input type="hidden"  id="client_id" name="client_id" value="{{ $user->client_id }}" required>

        @endif
       
        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>
@endsection
