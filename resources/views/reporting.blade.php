@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">📊 Reporting</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th></th>
                    <th>Total</th>
                    <th>Année en cours</th>
                    <th>Mois dernier</th>
                    <th>Mois en cours<br><small>Évolution</small></th>
                    <th>Semaine dernière</th>
                    <th>Semaine en cours<br><small>Évolution</small></th>
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
                                ({{ number_format($monthChange, 1) }}%)
                            </div>
                        </td>
                        <td>{{ $stat['past_week'] }}</td>
                        <td>
                            {{ $stat['current_week'] }}
                            <div class="{{ $weekClass }}">
                                ({{ number_format($weekChange, 1) }}%)
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
@endsection
