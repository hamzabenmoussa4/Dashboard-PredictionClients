@extends('layouts.app', ['title' => 'CRUD Commandes'])

@section('content')
    <div class="page-title">
        <div>
            <h1>CRUD Commandes</h1>
            <p class="muted">Créer, modifier et supprimer des commandes (page séparée).</p>
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
        <form method="GET" action="{{ route('orders.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input name="q" value="{{ $q ?? '' }}"
                   placeholder="Rechercher : order_number, client, status, currency..."
                   style="width:420px; max-width:70vw; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
            <button class="btn" type="submit">Rechercher</button>
            <a class="btn" href="{{ route('orders.index') }}" style="text-decoration:none;">Reset</a>
        </form>
    </div>

    {{-- AJOUT COMMANDE --}}
    <div class="section">
        <div class="section-head">
            <h2>Ajouter une commande</h2>
        </div>

        <form method="POST" action="{{ route('orders.store') }}">
            @csrf

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                <div>
                    <label style="font-size:13px; font-weight:700;">Client *</label>
                    <select name="customer_id" required
                            style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                        <option value="">-- Choisir un client --</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                #{{ $c->id }} - {{ $c->first_name }} {{ $c->last_name }} ({{ $c->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Numéro commande *</label>
                    <input name="order_number" value="{{ old('order_number') }}" required
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;"
                           placeholder="ORD-0001">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Status *</label>
                    <select name="status" required
                            style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>pending</option>
                        <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>paid</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>cancelled</option>
                        <option value="refunded" {{ old('status') === 'refunded' ? 'selected' : '' }}>refunded</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Montant total *</label>
                    <input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount') }}" required
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Devise *</label>
                    <input name="currency" value="{{ old('currency', 'MAD') }}" required maxlength="3"
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;"
                           placeholder="MAD">
                </div>

                <div>
                    <label style="font-size:13px; font-weight:700;">Date commande *</label>
                    <input type="date" name="ordered_at" value="{{ old('ordered_at') }}" required
                           style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                </div>
            </div>

            <div style="margin-top:12px;">
                <button class="btn" type="submit">Créer</button>
            </div>
        </form>
    </div>

    {{-- MODIFIER COMMANDE --}}
    @if ($editOrder)
        <div class="section">
            <div class="section-head">
                <h2>Modifier la commande #{{ $editOrder->id }}</h2>
                <a class="link" href="{{ route('orders.index', array_filter(['q' => $q ?? null])) }}">Annuler</a>
            </div>

            <form method="POST" action="{{ route('orders.update', $editOrder) }}">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:10px;">
                    <div>
                        <label style="font-size:13px; font-weight:700;">Client *</label>
                        <select name="customer_id" required
                                style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $editOrder->customer_id) == $c->id ? 'selected' : '' }}>
                                    #{{ $c->id }} - {{ $c->first_name }} {{ $c->last_name }} ({{ $c->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Numéro commande *</label>
                        <input name="order_number" required value="{{ old('order_number', $editOrder->order_number) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Status *</label>
                        <select name="status" required
                                style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                            <option value="pending" {{ old('status', $editOrder->status) === 'pending' ? 'selected' : '' }}>pending</option>
                            <option value="paid" {{ old('status', $editOrder->status) === 'paid' ? 'selected' : '' }}>paid</option>
                            <option value="cancelled" {{ old('status', $editOrder->status) === 'cancelled' ? 'selected' : '' }}>cancelled</option>
                            <option value="refunded" {{ old('status', $editOrder->status) === 'refunded' ? 'selected' : '' }}>refunded</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Montant total *</label>
                        <input type="number" step="0.01" min="0" name="total_amount" required
                               value="{{ old('total_amount', $editOrder->total_amount) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Devise *</label>
                        <input name="currency" required maxlength="3"
                               value="{{ old('currency', $editOrder->currency) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:700;">Date commande *</label>
                        <input type="date" name="ordered_at" required
                               value="{{ old('ordered_at', optional($editOrder->ordered_at)->format('Y-m-d')) }}"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
                    </div>
                </div>

                <div style="margin-top:12px; display:flex; gap:10px;">
                    <button class="btn" type="submit">Enregistrer</button>
                    <a class="btn" href="{{ route('orders.index', array_filter(['q' => $q ?? null])) }}" style="text-decoration:none;">Annuler</a>
                </div>
            </form>
        </div>
    @endif

    {{-- LISTE --}}
    <div class="section">
        <div class="section-head">
            <h2>Liste des commandes</h2>
            <span class="badge">{{ $orders->total() }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Numéro</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Status</th>
                    <th>Devise</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr>
                        <td>{{ $o->id }}</td>
                        <td>{{ $o->order_number }}</td>
                        <td>
                            @if ($o->customer)
                                #{{ $o->customer->id }} - {{ $o->customer->first_name }} {{ $o->customer->last_name }}
                                <div class="muted" style="margin:2px 0 0;">{{ $o->customer->email }}</div>
                            @else
                                Client #{{ $o->customer_id }}
                            @endif
                        </td>
                        <td>{{ $o->ordered_at }}</td>
                        <td>{{ number_format((float)$o->total_amount, 2) }}</td>
                        <td><span class="badge">{{ $o->status }}</span></td>
                        <td>{{ $o->currency }}</td>
                        <td style="display:flex; gap:8px; align-items:center;">
                            <a class="btn" href="{{ route('orders.index', array_filter(['edit' => $o->id, 'q' => $q ?? null])) }}" style="text-decoration:none;">Modifier</a>

                            <form method="POST" action="{{ route('orders.destroy', $o) }}" onsubmit="return confirm('Supprimer cette commande ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn" type="submit" style="border-color:#ef4444;">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">Aucun résultat.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pager">{{ $orders->links() }}</div>
    </div>
@endsection
