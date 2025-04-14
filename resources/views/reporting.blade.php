@extends('layouts.app')

@section('content')

<div class="container">
    <h2 class="mb-4">📊 Reporting</h2>
    <form method="GET" action="{{ route('reporting') }}" class="row g-3 mb-4">

        <div class="col-md-3">
            <label for="mandataire_financier" class="form-label text-capitalize">Mandataire</label>
            <select class="form-select select2" name="mandataire_financier" id="mandataire_financier" onchange=""this.form.submit()>
                <option value="">Choisir un mandataire</option>
                @foreach ($mandataires as $val => $label)
                    <option value="{{ $label->id }}" {{ request('mandataire_financier') == $label->id ? 'selected' : '' }}>
                        {{ $label->client_title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="mar" class="form-label text-capitalize">MAR</label>
            <select class="form-select select2" name="mar" id="mar" onchange=""this.form.submit()>
                <option value="">Choisir un mar</option>
                @foreach ($mar as $val => $label)
                    <option value="{{ $label->id }}" {{ request('mar') == $label->id ? 'selected' : '' }}>
                        {{ $label->client_title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="installateur" class="form-label text-capitalize">Apporteurs</label>
            <select class="form-select select2" name="installateur" id="installateur" onchange=""this.form.submit()>
                <option value="">Choisir un apporteur</option>
                @foreach ($installateurs as $val => $label)
                    <option value="{{ $label->id }}" {{ request('installateur') == $label->id ? 'selected' : '' }}>
                        {{ $label->client_title }}
                    </option>
                @endforeach
            </select>
        </div>

    <div class="col-md-3 d-flex align-items-end">
        <!-- <button type="submit" class="btn btn-primary w-100">Filtrer</button> -->
    </div>
</form>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th></th>
                    <th>Total</th>
                    <th>Année en cours</th>
                    <th>Mois dernier</th>
                    <th>Mois en cours</th>
                    <th>Semaine dernière</th>
                    <th>Semaine en cours</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @forelse ($stats as $stat)
                    @php
                        // Safe division to avoid division by 0
                        $monthChange = $stat['past_month'] > 0 
                            ? (($stat['current_month'] - $stat['past_month']) / $stat['past_month']) * 100 
                            : ($stat['current_month'] > 0 ? 100 : 0);

                        $weekChange = $stat['past_week'] > 0 
                            ? (($stat['current_week'] - $stat['past_week']) / $stat['past_week']) * 100 
                            : ($stat['current_week'] > 0 ? 100 : 0);

                        $monthClass = $monthChange > 0 ? 'text-success' : ($monthChange < 0 ? 'text-danger' : '');
                        $weekClass = $weekChange > 0 ? 'text-success' : ($weekChange < 0 ? 'text-danger' : '');
                    @endphp
                    <tr>
                        <td>{{ $stat['meta_key'] }}</td>
                        <td>{{ $stat['all_time'] }}</td>
                        <td>{{ $stat['current_year'] }}</td>
                        <td>{{ $stat['past_month'] }}</td>
                        <td>
                            {{ $stat['current_month'] }}
                            <div class="{{ $monthClass }}">
                                <!-- ({{ number_format($monthChange, 1) }}%) -->
                            </div>
                        </td>
                        <td>{{ $stat['past_week'] }}</td>
                        <td>
                            {{ $stat['current_week'] }}
                            <div class="{{ $weekClass }}">
                                <!-- ({{ number_format($weekChange, 1) }}%) -->
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Aucune donnée trouvée pour les meta_keys sélectionnées.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdowns = document.querySelectorAll('.select2');

        // For each dropdown, initialize Select2 and set up change event
        dropdowns.forEach(function(dropdown) {
            // Initialize Select2
            $(dropdown).select2();

            // Listen for changes
            $(dropdown).on('change', function () {
                // Submit the form that this dropdown is inside of
                dropdown.form.submit();
            });
        });
    });
</script>
@endsection
