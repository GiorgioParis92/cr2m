@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Edit User</h2>
    <form action="/user/edit/{{ $user->id }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>
        
        @if(auth()->user()->client_id==0)
        <div class="form-group">
            <label for="client_id">Client associé</label>
            <select class="form-control" id="client_id" name="client_id" >
            <option value="">Choisir un client</option>
            @foreach($clients as $client)
            <option @if($client->id==$user->client_id) selected @endif value="{{$client->id}}">{{$client->client_title}}</option>
            @endforeach
            </select>
        </div>  
        @else
        <input type="hidden"  id="client_id" name="client_id" value="{{ $user->client_id }}" required>

        @endif
      
        
        <div class="form-group">
            <label for="type_id">Type d'utilisateur</label>
            <select class="form-control" id="type_id" name="type_id" >
            <option value="">Choisir un type d'utilisateur</option>
            @foreach($types as $type)
            @dump($type)
            <option @if($type->id==$user->type_id) selected @endif value="{{$type->id}}">{{$type->type_desc}}</option>
            @endforeach
            </select>
        </div> 

        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>
@endsection
