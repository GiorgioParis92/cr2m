@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Permission</h1>

    @if ($errors->any())
        <!-- Error handling -->
    @endif

    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf

        <!-- Permission Name -->
        <div class="form-group">
            <label>Permission Name:</label>
            <input type="text" name="permission_name" class="form-control" placeholder="Permission Name" required>
        </div>

        <!-- Type ID (User) Dropdown -->
        <div class="form-group">
            <label>User Type:</label>
            <select name="type_id" class="form-control" required>
                <option value="">Select User Type</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Type Client Dropdown -->
        <div class="form-group">
            <label>Client Type:</label>
            <select name="type_client" class="form-control">
                <option value="">Select Client Type</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Is Active -->
        <div class="form-group">
            <label>Is Active:</label>
            <select name="is_active" class="form-control" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Permission</button>
    </form>
</div>
@endsection
