@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="page-title">
        <div>
            <h1>Dashboard</h1>
            <p class="muted">KPI + badges + derniers événements + recherche.</p>
        </div>
    </div>

    {{-- KPI --}}
    <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:12px;">
        <div class="card">
            <div class="muted" style="font-weight:900;">Total clients</div>
            <div class="value" style="font-size:28px; font-weight:900;">{{ $totalCustomers }}</div>
        </div>

        <div class="card">
            <div class="muted" style="font-weight:900;">Total commandes</div>
            <div class="value" style="font-size:28px; font-weight:900;">{{ $totalOrders }}</div>
        </div>

        <div class="card">
            <div class="muted" style="font-weight:900;">Revenu total</div>
            <div class="value" style="font-size:28px; font-weight:900;">{{ number_format($totalRevenue, 2) }}</div>
        </div>

        <div class="card">
            <div class="muted" style="font-weight:900;">Panier moyen</div>
            <div class="value" style="font-size:28px; font-weight:900;">{{ number_format($avgOrderValue, 2) }}</div>
        </div>
    </div>

    {{-- Badges cards + recherche --}}
    <div class="section" style="margin-top:14px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a class="btn" href="{{ route('dashboard.index', ['badge' => 'RISK', 'q' => $q]) }}"
                   style="text-decoration:none; padding:14px 16px; {{ $badge === 'RISK' ? 'border-color:#6366f1;' : '' }}">
                    <div style="font-weight:900;">RISK</div>
                    <div class="muted" style="margin-top:4px;">{{ $countRisk }} clients</div>
                </a>

                <a class="btn" href="{{ route('dashboard.index', ['badge' => 'NORMAL', 'q' => $q]) }}"
                   style="text-decoration:none; padding:14px 16px; {{ $badge === 'NORMAL' ? 'border-color:#6366f1;' : '' }}">
                    <div style="font-weight:900;">NORMAL</div>
                    <div class="muted" style="margin-top:4px;">{{ $countNormal }} clients</div>
                </a>

                <a class="btn" href="{{ route('dashboard.index', ['badge' => 'VIP', 'q' => $q]) }}"
                   style="text-decoration:none; padding:14px 16px; {{ $badge === 'VIP' ? 'border-color:#6366f1;' : '' }}">
                    <div style="font-weight:900;">VIP</div>
                    <div class="muted" style="margin-top:4px;">{{ $countVip }} clients</div>
                </a>

                <div class="btn" style="padding:14px 16px; cursor:default;">
                    <div style="font-weight:900;">Actions en attente</div>
                    <div class="muted" style="margin-top:4px;">{{ $pendingActionsCount }}</div>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="badge" value="{{ $badge }}">

                <input name="q" value="{{ $q }}"
                       placeholder="Rechercher client..."
                       style="width:320px; max-width:60vw; padding:10px; border:1px solid #e5e7eb; border-radius:10px;">

                <button class="btn" type="submit">Rechercher</button>

                <a class="btn" href="{{ route('dashboard.index', ['badge' => $badge]) }}" style="text-decoration:none;">
                    Reset
                </a>
            </form>
        </div>
    </div>

    @php
        $totalBadge = max(1, $countRisk + $countNormal + $countVip);
        $riskPct = round(($countRisk / $totalBadge) * 100);
        $normalPct = round(($countNormal / $totalBadge) * 100);
        $vipPct = round(($countVip / $totalBadge) * 100);
    @endphp

    <div style="display:grid; grid-template-columns: 0.55fr 1.45fr; gap:12px; margin-top:12px;">
        {{-- Diagramme badges --}}
        <div class="section" style="margin-top:0;">
            <div class="section-head">
                <h2>Répartition clients</h2>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px; margin-top:10px;">
                <div>
                    <div style="display:flex; justify-content:space-between; font-weight:900;">
                        <span>RISK</span>
                        <span>{{ $countRisk }} ({{ $riskPct }}%)</span>
                    </div>
                    <div style="height:12px; border-radius:999px; background:#eef2ff; overflow:hidden; margin-top:6px;">
                        <div style="height:12px; width:{{ $riskPct }}%; background:#ef4444;"></div>
                    </div>
                </div>

                <div>
                    <div style="display:flex; justify-content:space-between; font-weight:900;">
                        <span>NORMAL</span>
                        <span>{{ $countNormal }} ({{ $normalPct }}%)</span>
                    </div>
                    <div style="height:12px; border-radius:999px; background:#eef2ff; overflow:hidden; margin-top:6px;">
                        <div style="height:12px; width:{{ $normalPct }}%; background:#6366f1;"></div>
                    </div>
                </div>

                <div>
                    <div style="display:flex; justify-content:space-between; font-weight:900;">
                        <span>VIP</span>
                        <span>{{ $countVip }} ({{ $vipPct }}%)</span>
                    </div>
                    <div style="height:12px; border-radius:999px; background:#eef2ff; overflow:hidden; margin-top:6px;">
                        <div style="height:12px; width:{{ $vipPct }}%; background:#10b981;"></div>
                    </div>
                </div>

                <div style="margin-top:6px;" class="muted">
                    Badge = override si présent sinon computed.
                </div>
            </div>
        </div>

        {{-- Tables --}}
        <div class="section" style="margin-top:0;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                {{-- Clients filtrés --}}
                <div>
                    <div class="section-head">
                        <h2>Clients : {{ $badge }}</h2>
                        <span class="badge">{{ $filteredCustomers->total() }}</span>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Badge</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($filteredCustomers as $c)
                                <tr>
                                    <td>{{ $c->id }}</td>
                                    <td>{{ $c->first_name }} {{ $c->last_name }}</td>
                                    <td>{{ $c->email }}</td>
                                    <td><span class="badge">{{ $c->badge }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4">Aucun résultat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="pager">
                        {{ $filteredCustomers->links() }}
                    </div>
                </div>

                {{-- Dernières commandes + actions --}}
                <div>
                    <div class="section-head">
                        <h2>Dernières commandes</h2>
                        <a class="link" href="{{ route('orders.index') }}">Voir →</a>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $o)
                                <tr>
                                    <td>{{ $o->order_number }}</td>
                                    <td>
                                        @if ($o->customer)
                                            {{ $o->customer->first_name }} {{ $o->customer->last_name }}
                                        @else
                                            Client #{{ $o->customer_id }}
                                        @endif
                                    </td>
                                    <td>{{ number_format((float)$o->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">Aucune commande.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div style="height:14px;"></div>

                    <div class="section-head">
                        <h2>Dernières actions</h2>
                        <a class="link" href="{{ route('automation.actions') }}">Voir →</a>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Client</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentActions as $a)
                                @php
                                    $t = $a->type ?? $a->action_type;
                                @endphp
                                <tr>
                                    <td>{{ $a->executed_at ? $a->executed_at->format('m-d H:i') : '-' }}</td>
                                    <td><span class="badge">{{ $t }}</span></td>
                                    <td>
                                        @if ($a->customer)
                                            {{ $a->customer->first_name }}
                                        @else
                                            supprimé
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3">Aucune action.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
