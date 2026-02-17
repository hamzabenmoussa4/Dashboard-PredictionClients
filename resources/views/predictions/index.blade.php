@extends('layouts.app', ['title' => 'Predictions'])

@section('content')
    <div class="page-title">
        <div>
            <h1>Predictions</h1>
            <p class="muted">Générées automatiquement (stats réelles). Filtre par type + recherche.</p>
        </div>
    </div>

    <div class="section" style="margin-top:0;">
        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a class="btn" href="{{ route('predictions.index', array_filter(['q' => $q])) }}"
                   style="text-decoration:none; {{ !$type ? 'border-color:#6366f1;' : '' }}">
                    Tous
                </a>

                <a class="btn" href="{{ route('predictions.index', array_filter(['type' => 'churn', 'q' => $q])) }}"
                   style="text-decoration:none; {{ $type === 'churn' ? 'border-color:#6366f1;' : '' }}">
                    churn
                </a>

                <a class="btn" href="{{ route('predictions.index', array_filter(['type' => 'sales', 'q' => $q])) }}"
                   style="text-decoration:none; {{ $type === 'sales' ? 'border-color:#6366f1;' : '' }}">
                    sales
                </a>

                <a class="btn" href="{{ route('predictions.index', array_filter(['type' => 'engagement', 'q' => $q])) }}"
                   style="text-decoration:none; {{ $type === 'engagement' ? 'border-color:#6366f1;' : '' }}">
                    engagement
                </a>
            </div>

            <form method="GET" action="{{ route('predictions.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                @if ($type)
                    <input type="hidden" name="type" value="{{ $type }}">
                @endif

                <input name="q" value="{{ $q }}"
                       placeholder="Rechercher client, email, score, label..."
                       style="width:320px; max-width:60vw; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">

                <button class="btn" type="submit">Rechercher</button>

                <a class="btn" href="{{ route('predictions.index', array_filter(['type' => $type])) }}" style="text-decoration:none;">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <div class="section">
        <div class="section-head">
            <h2>Liste des prédictions</h2>
            <span class="badge">{{ $predictions->total() }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Score</th>
                    <th>Label</th>
                    <th>Predicted at</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($predictions as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>
                            @if ($p->customer)
                                {{ $p->customer->first_name }} {{ $p->customer->last_name }}
                                <div class="muted" style="margin:2px 0 0;">{{ $p->customer->email }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td><span class="badge">{{ $p->type }}</span></td>
                        <td>{{ number_format((float) $p->score, 4) }}</td>
                        <td>{{ $p->label ?? '-' }}</td>
                        <td>{{ $p->predicted_at ? $p->predicted_at->format('Y-m-d H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Aucun résultat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pager">
            {{ $predictions->links() }}
        </div>
    </div>
@endsection
