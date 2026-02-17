@extends('layouts.app', ['title' => 'Automation Actions'])

@section('content')
    <div class="page-title">
        <div>
            <h1>Automation Actions</h1>
            <p class="muted">
                Choisis une catégorie (badge), recherche, sélectionne des clients, exécute une action, puis consulte l’historique.
            </p>
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

    {{-- Tabs + Search --}}
    <div class="section" style="margin-top:0;">
        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a class="btn" href="{{ route('automation.actions', array_filter(['badge' => 'RISK', 'q' => $q ?? null])) }}"
                   style="text-decoration:none; {{ $badge === 'RISK' ? 'border-color:#6366f1;' : '' }}">
                    RISK
                </a>
                <a class="btn" href="{{ route('automation.actions', array_filter(['badge' => 'NORMAL', 'q' => $q ?? null])) }}"
                   style="text-decoration:none; {{ $badge === 'NORMAL' ? 'border-color:#6366f1;' : '' }}">
                    NORMAL
                </a>
                <a class="btn" href="{{ route('automation.actions', array_filter(['badge' => 'VIP', 'q' => $q ?? null])) }}"
                   style="text-decoration:none; {{ $badge === 'VIP' ? 'border-color:#6366f1;' : '' }}">
                    VIP
                </a>
            </div>

            <form method="GET" action="{{ route('automation.actions') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="badge" value="{{ $badge }}">

                <input name="q" value="{{ $q ?? '' }}"
                       placeholder="Rechercher (nom, email, phone...)"
                       style="width:320px; max-width:60vw; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">

                <button class="btn" type="submit">Rechercher</button>

                <a class="btn" href="{{ route('automation.actions', ['badge' => $badge]) }}" style="text-decoration:none;">
                    Reset
                </a>
            </form>
        </div>
    </div>

    {{-- Sélection clients + action --}}
    <div class="section">
        <div class="section-head">
            <h2>Clients : {{ $badge }}</h2>
            <span class="badge">{{ count($customers) }}</span>
        </div>

        <form method="POST" action="{{ route('automation.actions.run') }}">
            @csrf

            <input type="hidden" name="badge" value="{{ $badge }}">

            <div style="display:grid; grid-template-columns: 1fr; gap:12px; margin-top:10px;">

                <div>
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                        <div style="font-weight:900;">Sélection des clients</div>

                        <div style="display:flex; gap:8px;">
                            <button class="btn" type="button" onclick="selectAll(true)">Tout cocher</button>
                            <button class="btn" type="button" onclick="selectAll(false)">Tout décocher</button>
                        </div>
                    </div>

                    <div style="margin-top:10px; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:60px;">Choix</th>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Email</th>
                                    <th>Badge</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $c)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="customer-checkbox">
                                        </td>
                                        <td>{{ $c->id }}</td>
                                        <td>{{ $c->first_name }} {{ $c->last_name }}</td>
                                        <td>{{ $c->email }}</td>
                                        <td><span class="badge">{{ $c->badge }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Aucun client.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 220px 1fr 200px; gap:12px;">
                    <div>
                        <label style="font-size:13px; font-weight:800;">Action *</label>
                        <select name="action_type"
                                style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;"
                                onchange="toggleFields()"
                                id="actionType">
                            <option value="email">email</option>
                            <option value="notify">notify</option>
                            <option value="discount">discount</option>
                        </select>
                    </div>

                    <div id="messageBox">
                        <label style="font-size:13px; font-weight:800;">Message (email/notify) *</label>
                        <input name="message"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;"
                               placeholder="Ex: Bonjour, ...">
                    </div>

                    <div id="discountBox" style="display:none;">
                        <label style="font-size:13px; font-weight:800;">Discount % *</label>
                        <input name="discount_percent"
                               style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;"
                               placeholder="Ex: 10">
                    </div>
                </div>

                <div>
                    <button class="btn" type="submit">Exécuter / Enregistrer</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Historique --}}
    <div class="section">
        <div class="section-head">
            <h2>Historique : {{ $badge }}</h2>
            <span class="badge">{{ count($history) }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Action</th>
                    <th>Infos</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($history as $h)
                    <tr>
                        <td>{{ $h->executed_at ? $h->executed_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            @if ($h->customer)
                                {{ $h->customer->first_name }} {{ $h->customer->last_name }} (ID: {{ $h->customer->id }})
                            @else
                                Client supprimé
                            @endif
                        </td>
                        <td><span class="badge">{{ $h->type ?? $h->action_type }}</span></td>
                        <td>
                            @php
                                $t = $h->type ?? $h->action_type;
                            @endphp

                            @if ($t === 'discount')
                                discount = {{ $h->discount_percent ?? '-' }}%
                            @else
                                {{ $h->message ?? ($h->payload['message'] ?? '-') }}
                            @endif
                        </td>
                        <td><span class="badge">{{ $h->status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Aucune action.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function selectAll(state) {
            document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = state);
        }

        function toggleFields() {
            const actionType = document.getElementById('actionType').value;
            const messageBox = document.getElementById('messageBox');
            const discountBox = document.getElementById('discountBox');

            if (actionType === 'discount') {
                messageBox.style.display = 'none';
                discountBox.style.display = 'block';
            } else {
                messageBox.style.display = 'block';
                discountBox.style.display = 'none';
            }
        }

        toggleFields();
    </script>
@endsection
