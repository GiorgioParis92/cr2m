@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Modifier la tâche</h1>
        <form action="{{ route('taches.update', $tache) }}" method="POST">
            @csrf
            @method('PUT')
            @foreach (['nom', 'prenom', 'numero_dossier', 'objet', 'type_demande'] as $field)
                <div class="mb-3">
                    <label for="{{ $field }}" class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                    <input type="text" name="{{ $field }}" class="form-control" required
                        value="{{ old($field, $tache->{$field}) }}">

                </div>
            @endforeach

            <div class="mb-3">
                <label class="form-label">Date de réception</label>
                <input type="date" name="date_reception" class="form-control" required
                    value="{{ old('date_reception', $tache->date_reception) }}"     onfocus="this.showPicker()"
                    >
            </div>

            <div class="mb-3">
                <label class="form-label">Date d’affectation</label>
                <input type="date" name="date_affectation" class="form-control"
                    value="{{ old('date_affectation', $tache->date_affectation) }}"     onfocus="this.showPicker()"
                    >
            </div>

            <div class="mb-3">
                <label class="form-label">Agent</label>
                <select name="agent_id" class="form-control" required>
                    @foreach ($agents as $agent)
                        <option value="{{ $agent->id }}" {{ old('agent_id', $tache->agent_id) == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            <div class="mb-3">
                <label class="form-label">Commentaires</label>
                <textarea name="commentaires"
                    class="form-control">{{ old('commentaires', $tache->commentaires) }}</textarea>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="valide" class="form-check-input" value="1" {{ old('valide', $tache->valide) ? 'checked' : '' }}>
                <label class="form-check-label">Valide</label>
            </div>

            <button type="submit" class="btn btn-success">Enregistrer</button>
        </form>
    </div>
@endsection