@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Permissions</h1>

    <form action="{{ route('permissions.index') }}" method="GET" class="form-inline mb-3">
        <input type="text" name="permission_name" class="form-control mr-2" placeholder="Permission Name" value="{{ request('permission_name') }}">
        <input type="number" name="type_id" class="form-control mr-2" placeholder="Type ID" value="{{ request('type_id') }}">
        <input type="number" name="type_client" class="form-control mr-2" placeholder="Type Client" value="{{ request('type_client') }}">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <a href="{{ route('permissions.create') }}" class="btn btn-success mb-3">Create Permission</a>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Permission Name</th>
            <th>Type ID</th>
            <th>Type Client</th>
            <th>Is Active</th>
            <th>Actions</th>
        </tr>
        @foreach ($permissions as $permission)
        <tr>
            <td>{{ $permission->id }}</td>
            <td>{{ $permission->permission_name }}</td>
            <td>{{ $permission->userType->type_desc ?? '' }}</td>
            <td>{{ $permission->clientType->type_desc }}</td>
            <td>{{ $permission->is_active ? 'Yes' : 'No' }}</td>
            <td>
                <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-info btn-sm">Show</a>
                <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary btn-sm">Edit</a>
                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this permission?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
