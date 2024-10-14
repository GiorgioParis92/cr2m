@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Permission</h1>

    @if ($errors->any())
        <!-- Error handling -->
    @endif

    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Permission Name -->
        <div class="form-group">
            <label>Permission Name:</label>
            <input type="text" name="permission_name" class="form-control" value="{{ $permission->permission_name }}" required>
        </div>

        <!-- Type ID (User) Dropdown -->
        <div class="form-group">
            <label>User Type:</label>
            <select name="type_id" class="form-control" >
                <option value="">Select User Type</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $permission->type_id == $user->id ? 'selected' : '' }}>
                        {{ $user->type_desc }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Type Client Dropdown -->
        <div class="form-group">
            <label>Client Type:</label>
            <select name="type_client" class="form-control">
                <option value="">Select Client Type</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $permission->type_client == $client->id ? 'selected' : '' }}>
                        {{ $client->type_desc }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Is Active -->
        <div class="form-group">
            <label>Is Active:</label>
            <select name="is_active" class="form-control" >
                <option value="1" {{ $permission->is_active ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !$permission->is_active ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <!-- Update Button -->
        <button type="submit" class="btn btn-primary">Update Permission</button>
    </form>
</div>
@endsection
