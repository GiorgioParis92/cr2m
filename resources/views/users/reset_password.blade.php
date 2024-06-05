@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Reset Password</h2>
    <form action="/password/reset" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
    </form>
</div>
@endsection
