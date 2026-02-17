<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automation - Actions</title>

    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        a { text-decoration: none; }
        .muted { color: #666; }
        .topbar { display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 8px 12px; border: 1px solid #333; border-radius: 8px; background: #fff; cursor: pointer; }
        .tabs { margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; }
        .tab { padding: 6px 10px; border: 1px solid #ddd; border-radius: 999px; }
        .tab.active { background: #f5f5f5; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; }

        .badge { padding: 3px 8px; border-radius: 999px; font-size: 12px; }
        .pending { background: #fff3cd; border: 1px solid #ffe69c; }
        .done { background: #d1e7dd; border: 1px solid #badbcc; }
        .failed { background: #f8d7da; border: 1px solid #f5c2c7; }

        .flash { margin-top: 12px; padding: 10px; background: #e7ffe7; border: 1px solid #b7e7b7; border-radius: 8px; }
        pre { margin: 0; white-space: pre-wrap; }
        .pager { margin-top: 15px; }
    </style>
</head>
<body>

    <div class="topbar">
        <div>
            <h1>Automation - Actions</h1>
            <p class="muted">Actions générées par le moteur d’automatisation.</p>
        </div>

        <form method="POST" action="{{ route('automation.run') }}">
            @csrf
            <button class="btn" type="submit">Relancer automation</button>
        </form>
    </div>

    @if (session('success'))
        <div class="flash">
            {{ session('success') }}
        </div>
    @endif

    <div class="tabs">
        <a class="tab {{ $status === 'pending' ? 'active' : '' }}"
           href="{{ route('automation.actions', ['status' => 'pending']) }}">
            Pending
        </a>

        <a class="tab {{ $status === 'done' ? 'active' : '' }}"
           href="{{ route('automation.actions', ['status' => 'done']) }}">
            Done
        </a>

        <a class="tab {{ $status === 'failed' ? 'active' : '' }}"
           href="{{ route('automation.actions', ['status' => 'failed']) }}">
            Failed
        </a>

        <a class="tab" href="{{ route('automation.rules') }}">Règles</a>
        <a class="tab" href="{{ route('customers.atRisk') }}">Clients At Risk</a>
        <a class="tab" href="{{ route('dashboard.index') }}">Dashboard</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Règle</th>
                <th>Exécution prévue</th>
                <th>Payload</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($actions as $action)
                <tr>
                    <td>{{ $action->created_at }}</td>
                    <td>{{ $action->customer?->full_name ?? 'Client #' . $action->customer_id }}</td>
                    <td>{{ $action->type }}</td>
                    <td>
                        @if ($action->status === 'pending')
                            <span class="badge pending">pending</span>
                        @elseif ($action->status === 'done')
                            <span class="badge done">done</span>
                        @elseif ($action->status === 'failed')
                            <span class="badge failed">failed</span>
                        @else
                            {{ $action->status }}
                        @endif
                    </td>
                    <td>{{ $action->rule?->name ?? '-' }}</td>
                    <td>{{ $action->scheduled_for ?? '-' }}</td>
                    <td>
                        <pre>{{ json_encode($action->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucune action trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pager">
        {{ $actions->links() }}
    </div>

</body>
</html>
