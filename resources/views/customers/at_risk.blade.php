@extends('layouts.app', ['title' => 'Customers At Risk'])

@section('content')
    <div class="page-title">
        <div>
            <h1>Clients "At Risk"</h1>
            <p class="muted">Liste complète des clients dans le segment "At Risk" (paginée).</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Email</th>
                <th>Récence (jours)</th>
                <th>Fréquence 90j</th>
                <th>Total dépensé</th>
                <th>Dernier churn score</th>
                <th>Label</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $c)
                @php
                    $lastChurn = $c->predictions->first();
                @endphp

                <tr>
                    <td>{{ $c->full_name ?: ('Client #' . $c->id) }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->features?->recency_days ?? '-' }}</td>
                    <td>{{ $c->features?->frequency_90d ?? '-' }}</td>
                    <td>{{ $c->features?->total_spent !== null ? number_format((float)$c->features->total_spent, 2) : '-' }}</td>
                    <td>{{ $lastChurn?->score !== null ? number_format((float)$lastChurn->score, 4) : '-' }}</td>
                    <td>{{ $lastChurn?->label ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucun client "At Risk" trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pager">
        {{ $customers->links() }}
    </div>
@endsection
