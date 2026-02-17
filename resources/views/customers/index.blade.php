@extends('layouts.app', ['title' => 'CRUD Clients'])

@section('content')
    <div class="page-title">
        <div>
            <h1>CRUD Clients</h1>
            <p class="muted">Créer, modifier et supprimer des clients. Badge = override si présent sinon computed.</p>
        </div>
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
        <form method="GET" action="{{ route('customers.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input name="q" value="{{ $q ?? '' }}"
                   placeholder="Rechercher : nom, email, phone, badge..."
                   style="width:360px; max-width:70vw; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
            <button class="btn" type="submit">Rechercher</button>
            <a class="btn" href="{{ route('customers.index') }}" style="text-decoration:none;">Reset</a>
        </form>
    </div>

    {{-- AJOUT --}}
    <div class="section">
        <div class="section-head">
            <h2>Ajouter un client</h2>
        </div>

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                <div>
                    <label style="font-size:13px; font-weight:700;">Prénom *</label>
                    <input name="first_name" value="{{ old('first_name') }}" required
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Nom *</label>
                    <input name="last_name" value="{{ old('last_name') }}" required
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Email *</label>
                    <input name="email" value="{{ old('email') }}" required
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Téléphone</label>
                    <input name="phone" value="{{ old('phone') }}"
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Status *</label>
                    <select name="status" required
                            style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>inactive</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:12px;">
                <button class="btn" type="submit">Créer</button>
            </div>
        </form>
    </div>

    {{-- EDIT --}}
    @if ($editCustomer)
        <div class="section">
            <div class="section-head">
                <h2>Modifier le client #{{ $editCustomer->id }}</h2>
                <a class="link" href="{{ route('customers.index', array_filter(['q' => $q ?? null])) }}">Annuler</a>
            </div>

            <form method="POST" action="{{ route('customers.update', $editCustomer) }}">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                    <div>
                        <label style="font-size:13px; font-weight:700;">Prénom *</label>
                        <input name="first_name" required value="{{ old('first_name', $editCustomer->first_name) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Nom *</label>
                        <input name="last_name" required value="{{ old('last_name', $editCustomer->last_name) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Email *</label>
                        <input name="email" required value="{{ old('email', $editCustomer->email) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Téléphone</label>
                        <input name="phone" value="{{ old('phone', $editCustomer->phone) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Status *</label>
                        <select name="status" required
                                style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                            <option value="active" {{ old('status', $editCustomer->status) === 'active' ? 'selected' : '' }}>active</option>
                            <option value="inactive" {{ old('status', $editCustomer->status) === 'inactive' ? 'selected' : '' }}>inactive</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Badge (override)</label>
                        <select name="badge_override"
                                style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                            <option value="" {{ old('badge_override', $editCustomer->badge_override) === null ? 'selected' : '' }}>
                                AUTO (computed)
                            </option>
                            <option value="NORMAL" {{ old('badge_override', $editCustomer->badge_override) === 'NORMAL' ? 'selected' : '' }}>NORMAL</option>
                            <option value="VIP" {{ old('badge_override', $editCustomer->badge_override) === 'VIP' ? 'selected' : '' }}>VIP</option>
                            <option value="RISK" {{ old('badge_override', $editCustomer->badge_override) === 'RISK' ? 'selected' : '' }}>RISK</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top:12px; display:flex; gap:10px;">
                    <button class="btn" type="submit">Enregistrer</button>
                    <a class="btn" href="{{ route('customers.index', array_filter(['q' => $q ?? null])) }}" style="text-decoration:none;">Annuler</a>
                </div>
            </form>
        </div>
    @endif

    {{-- LISTE --}}
    <div class="section">
        <div class="section-head">
            <h2>Liste des clients</h2>
            <span class="badge">{{ $customers->total() }}</span>
        </div>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Status</th>
                <th>Badge</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($customers as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->first_name }}</td>
                    <td>{{ $c->last_name }}</td>
                    <td>{{ $c->email }}</td>
                    <td><span class="badge">{{ $c->status }}</span></td>
                    <td><span class="badge">{{ $c->badge }}</span></td>
                    <td style="display:flex; gap:8px; align-items:center;">
                        <a class="btn" href="{{ route('customers.index', array_filter(['edit' => $c->id, 'q' => $q ?? null])) }}" style="text-decoration:none;">Modifier</a>

                        <form method="POST" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Supprimer ce client ?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn" type="submit" style="border-color:#ef4444;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Aucun résultat.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="pager">
            {{ $customers->links() }}
        </div>
    </div>
@endsection
