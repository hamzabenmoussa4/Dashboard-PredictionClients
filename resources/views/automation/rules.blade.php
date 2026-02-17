@extends('layouts.app', ['title' => 'Rules (Badges)'])

@section('content')
    <div class="page-title">
        <div>
            <h1>Rules (Badges)</h1>
            <p class="muted">Nom + condition prediction + résultat badge. Recherche + pagination.</p>
        </div>

        <form method="POST" action="{{ route('automation.run') }}">
            @csrf
            <button class="btn" type="submit">Run Automation (Badges)</button>
        </form>
    </div>

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash" style="border-color:#f5c2c7; background:#f8d7da; color:#842029;">
            <ul style="margin:0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="section" style="margin-top:0;">
        <form method="GET" action="{{ route('automation.rules') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input name="q" value="{{ $q ?? '' }}"
                   placeholder="Rechercher : nom, type, operator..."
                   style="width:360px; max-width:70vw; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
            <button class="btn" type="submit">Rechercher</button>
            <a class="btn" href="{{ route('automation.rules') }}" style="text-decoration:none;">Reset</a>
        </form>
    </div>

    {{-- AJOUT --}}
    <div class="section">
        <div class="section-head">
            <h2>Ajouter une règle</h2>
        </div>

        <form method="POST" action="{{ route('automation.rules.store') }}">
            @csrf

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                <div style="grid-column: span 2;">
                    <label style="font-size:13px; font-weight:700;">Nom *</label>
                    <input name="name" value="{{ old('name') }}" required
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Prediction type *</label>
                    <select name="prediction_type" required
                            style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                        <option value="churn">churn</option>
                        <option value="sales">sales</option>
                        <option value="engagement">engagement</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Operator *</label>
                    <select name="operator" required
                            style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                        <option value=">">&gt;</option>
                        <option value=">=">&gt;=</option>
                        <option value="<">&lt;</option>
                        <option value="<=">&lt;=</option>
                        <option value="=">=</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Threshold *</label>
                    <input name="threshold" value="{{ old('threshold') }}" required
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;"
                           placeholder="ex: 0.70">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Résultat (Badge) *</label>
                    <select name="result_badge" required
                            style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                        <option value="NORMAL">NORMAL</option>
                        <option value="VIP">VIP</option>
                        <option value="RISK">RISK</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:12px;">
                <button class="btn" type="submit">Créer</button>
            </div>
        </form>
    </div>

    {{-- EDIT --}}
    @if ($editRule)
        <div class="section">
            <div class="section-head">
                <h2>Modifier la règle #{{ $editRule->id }}</h2>
                <a class="link" href="{{ route('automation.rules', array_filter(['q' => $q ?? null])) }}">Annuler</a>
            </div>

            <form method="POST" action="{{ route('automation.rules.update', $editRule) }}">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                    <div style="grid-column: span 2;">
                        <label style="font-size:13px; font-weight:700;">Nom *</label>
                        <input name="name" required value="{{ old('name', $editRule->name) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Prediction type *</label>
                        <select name="prediction_type" required
                                style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                            <option value="churn" {{ old('prediction_type', $editRule->prediction_type) === 'churn' ? 'selected' : '' }}>churn</option>
                            <option value="sales" {{ old('prediction_type', $editRule->prediction_type) === 'sales' ? 'selected' : '' }}>sales</option>
                            <option value="engagement" {{ old('prediction_type', $editRule->prediction_type) === 'engagement' ? 'selected' : '' }}>engagement</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Operator *</label>
                        <select name="operator" required
                                style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                            <option value=">"  {{ old('operator', $editRule->operator) === '>'  ? 'selected' : '' }}>&gt;</option>
                            <option value=">=" {{ old('operator', $editRule->operator) === '>=' ? 'selected' : '' }}>&gt;=</option>
                            <option value="<"  {{ old('operator', $editRule->operator) === '<'  ? 'selected' : '' }}>&lt;</option>
                            <option value="<=" {{ old('operator', $editRule->operator) === '<=' ? 'selected' : '' }}>&lt;=</option>
                            <option value="="  {{ old('operator', $editRule->operator) === '='  ? 'selected' : '' }}>=</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Threshold *</label>
                        <input name="threshold" required value="{{ old('threshold', $editRule->threshold) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Résultat (Badge) *</label>
                        <select name="result_badge" required
                                style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                            <option value="NORMAL" {{ old('result_badge', $editRule->action_payload['badge'] ?? 'NORMAL') === 'NORMAL' ? 'selected' : '' }}>NORMAL</option>
                            <option value="VIP" {{ old('result_badge', $editRule->action_payload['badge'] ?? '') === 'VIP' ? 'selected' : '' }}>VIP</option>
                            <option value="RISK" {{ old('result_badge', $editRule->action_payload['badge'] ?? '') === 'RISK' ? 'selected' : '' }}>RISK</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top:12px; display:flex; gap:10px;">
                    <button class="btn" type="submit">Enregistrer</button>
                    <a class="btn" href="{{ route('automation.rules', array_filter(['q' => $q ?? null])) }}" style="text-decoration:none;">Annuler</a>
                </div>
            </form>
        </div>
    @endif

    {{-- LISTE --}}
    <div class="section">
        <div class="section-head">
            <h2>Liste</h2>
            <span class="badge">{{ $rules->total() }}</span>
        </div>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Active</th>
                <th>Condition</th>
                <th>Résultat (Badge)</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($rules as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->name }}</td>
                    <td><span class="badge">{{ $r->is_active ? 'ON' : 'OFF' }}</span></td>
                    <td>{{ $r->prediction_type }} {{ $r->operator }} {{ $r->threshold }}</td>
                    <td><span class="badge">{{ $r->action_payload['badge'] ?? 'NORMAL' }}</span></td>
                    <td style="display:flex; gap:8px; align-items:center;">
                        <a class="btn" href="{{ route('automation.rules', array_filter(['edit' => $r->id, 'q' => $q ?? null])) }}" style="text-decoration:none;">Modifier</a>

                        <form method="POST" action="{{ route('automation.rules.toggle', $r) }}">
                            @csrf
                            <button class="btn" type="submit">{{ $r->is_active ? 'Désactiver' : 'Activer' }}</button>
                        </form>

                        <form method="POST" action="{{ route('automation.rules.destroy', $r) }}" onsubmit="return confirm('Supprimer cette règle ?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn" type="submit" style="border-color:#ef4444;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun résultat.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="pager">{{ $rules->links() }}</div>
    </div>
@endsection
