@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Liste des tâches</h1>
        <a href="{{ route('taches.create') }}" class="btn btn-primary mb-3">Nouvelle tâche</a>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="agent_id" class="form-select" onchange="this.form.submit()">
                @php
    $selectedAgentId = request()->has('agent_id')
        ? request('agent_id')
        : auth()->user()->id;
@endphp

<option value="">Tous les agents</option>
@foreach ($agents as $agent)
    <option
        value="{{ $agent->id }}"
        {{ (string) $selectedAgentId === (string) $agent->id ? 'selected' : '' }}
    >
        {{ $agent->name }}
    </option>
@endforeach
                </select>
            </div>

            <div class="col-md-4">
                <input type="text" name="type_demande" class="form-control" placeholder="Recherche"
                    value="{{ request('type_demande') }}" onblur="this.form.submit()">
            </div>

            <div class="col-md-4">
                <button class="btn btn-secondary" type="submit">Filtrer</button>
                <a href="{{ route('taches.index') }}" class="btn btn-light">Réinitialiser</a>
            </div>


            <div class="col-md-4">
                <select name="valide" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes les tâches</option>
                    <option value="1" {{ request('valide') === '1' ? 'selected' : '' }}>✅ Validées</option>
                    <option value="0" {{ request('valide') === '0' ? 'selected' : '' }}>❌ Non validées</option>
                </select>
            </div>


        </form>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>N° Dossier</th>
                    <th>Objet</th>
                    <th>Date réception</th>
                    <th>Date affectation</th>
                    <th>Type</th>
                    <th>Agent</th>
                    <th>Commentaires</th>
                    <th>Valide</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($taches as $tache)
                    <tr>
                        <td>{{ $tache->nom }}</td>
                        <td>{{ $tache->prenom }}</td>
                        <td>{{ $tache->numero_dossier }}</td>
                        <td>{{ $tache->objet }}</td>
                        <td>{{ date("d/m/Y",strtotime($tache->date_reception)) }}</td>
                        <td>{{ date("d/m/Y",strtotime($tache->date_affectation)) }}</td>
                        <td>{{ $tache->type_demande }}</td>
                        <td>{{ $tache->agent->name }}</td>
                        <td>{{ $tache->commentaires }}</td>
                        <td>
                            @if ($tache->valide)
                                <span class="badge bg-success">✔ Valide</span>
                            @else
                                <span class="badge bg-danger">✖ Non valide</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('taches.edit', $tache) }}" class="btn btn-sm btn-warning">✏️</a>

                            <form action="{{ route('taches.destroy', $tache) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Supprimer cette tâche ?')">🗑️</button>
                            </form>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection