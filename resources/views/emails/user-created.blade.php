@extends('emails.base')

@section('title', 'Your Account has been Created')

@section('content')
<h1>Bonjour {{ $user->name }},</h1>
<p>Votre compte a été créé sur le CRM. Voici vos détails de connexion</p>
<p><strong>Email:</strong> {{ $user->email }}</p>
<p><strong>Mot de passe temporaire :</strong> {{ $temporaryPassword }}</p>
<p>Merci de changer votre mot de passe en vous connectant au lien suivant :</p>
<a class="btn btn-primary" href="{{ $url }}">Réinitialisation du mot de passe</a>
<p>Merci!</p>
@endsection
