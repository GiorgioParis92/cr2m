@extends('layouts.app')

@section('content')
<div class="container">
<h1>Modifier la tâche</h1>
<form action="{{ route('taches.update', $tache) }}" method="POST">
    @method('PUT')
        @csrf
        @foreach (['nom', 'prenom', 'numero_dossier', 'objet', 'type_demande'] as $field)
            <div class="mb-3">
                <label for="{{ $field }}" class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                <input type="text" name="{{ $field }}" class="form-control" required {{ old($field, $tache->{$field}) }}>
            </div>
        @endforeach

        <div class="mb-3">
            <label class="form-label">Date de réception</label>
            <input type="date" name="date_reception" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Date d’affectation</label>
            <input type="date" name="date_affectation" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Agent</label>
            <select name="agent_id" class="form-control" required>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Commentaires</label>
            <textarea name="commentaires" class="form-control"></textarea>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="valide" class="form-check-input" value="1">
            <label class="form-check-label">Valide</label>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
    </form>
</div>
@endsection
