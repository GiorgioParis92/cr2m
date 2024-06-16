@extends('emails.base')

@section('title', 'Reset Your Password')

@section('content')
<h1>Bonjour {{ $user->name }},</h1>
<p>You are receiving this email because we received a password reset request for your account.</p>
<p>You are receiving this email because we received a password reset request for your account.</p>
<p>If you did not request a password reset, no further action is required.</p>
<a href="{{ $url }}">Reset Password</a>
<p>This password reset link will expire in 60 minutes.</p>
<p>If you have any questions, feel free to contact our support team.</p>
@endsection
