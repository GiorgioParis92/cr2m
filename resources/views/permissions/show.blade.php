@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Permission Details</h1>

    <div class="form-group">
        <strong>ID:</strong>
        {{ $permission->id }}
    </div>

    <div class="form-group">
        <strong>Permission Name:</strong>
        {{ $permission->permission_name }}
    </div>

    <div class="form-group">
        <strong>User Type:</strong>
        {{ $permission->userType ? $permission->userType->name : 'N/A' }}
    </div>

    <div class="form-group">
        <strong>Client Type:</strong>
        {{ $permission->clientType ? $permission->clientType->name : 'N/A' }}
    </div>

    <div class="form-group">
        <strong>Is Active:</strong>
        {{ $permission->is_active ? 'Yes' : 'No' }}
    </div>

    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
