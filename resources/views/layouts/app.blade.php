<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --bg: #0b1220;               /* bleu marine pas clair */
            --bg2: #0e1a2f;              /* surface */
            --panel: rgba(255,255,255,.04);
            --border: rgba(255,255,255,.09);
            --text: rgba(255,255,255,.92);
            --muted: rgba(255,255,255,.64);

            --green: #14b8a6;            /* vert pas clair */
            --green2: #0d9488;           /* hover */
            --danger: #ef4444;
            --card: rgba(255,255,255,.03);

            --shadow: 0 14px 40px rgba(0,0,0,.28);
            --radius: 16px;
            --radius2: 12px;
            --font: system-ui, -apple-system, Segoe UI, Roboto, Arial;
        }

        *{ box-sizing:border-box; }

        body {
            margin:0;
            font-family: var(--font);
            color: var(--text);
            background:
                radial-gradient(1200px 520px at 18% 0%, rgba(20,184,166,.16), transparent 60%),
                radial-gradient(900px 520px at 85% 5%, rgba(59,130,246,.10), transparent 60%),
                var(--bg);
        }

        .app-shell { display:flex; min-height:100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 270px;
            color: var(--text);
            padding: 18px 14px;
            position: sticky;
            top: 0;
            height: 100vh;
            border-right: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
        }

        .brand {
            display:flex;
            align-items:center;
            gap:10px;
            padding: 12px 12px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: rgba(255,255,255,.03);
            margin-bottom: 14px;
        }

       .brand .logo{
    width: 38px;
    height: 38px;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.06);
    overflow: hidden;
    display:flex;
    align-items:center;
    justify-content:center;
}

.brand .logo .logo-img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    display:block;
}


        .brand .title {
            font-weight: 950;
            letter-spacing: .2px;
            line-height: 1.1;
        }
        .brand .sub {
            margin-top: 2px;
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
        }

        .nav {
            display:flex;
            flex-direction:column;
            gap:8px;
            margin-top: 10px;
        }

        .nav .section-title {
            font-size: 11px;
            opacity: .8;
            margin: 14px 10px 6px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: rgba(255,255,255,.72);
        }

        .nav a {
            display:flex;
            align-items:center;
            gap:10px;
            padding: 10px 12px;
            border-radius: 14px;
            text-decoration:none;
            color: var(--text);
            font-weight: 800;
            font-size: 14px;
            transition: .15s ease;
            border: 1px solid transparent;
            background: rgba(255,255,255,.03);
        }

        .nav a:hover {
            background: rgba(20,184,166,.08);
            border-color: rgba(20,184,166,.35);
        }

        .nav a.active {
            background: linear-gradient(90deg, rgba(20,184,166,.22), rgba(20,184,166,.06));
            border: 1px solid rgba(20,184,166,.45);
        }

        /* CONTENT */
        .content {
            flex:1;
            display:flex;
            flex-direction:column;
            min-width:0;
        }

        .topbar {
            height: 70px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding: 0 18px;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,.03);
            backdrop-filter: blur(10px);
        }

        .topbar .left-title{
            font-weight: 950;
            letter-spacing: .2px;
            color: var(--text);
        }

        .topbar .right {
            display:flex;
            align-items:center;
            gap:12px;
        }

        .user-pill {
            padding: 9px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            font-size: 13px;
            font-weight: 900;
            color: var(--text);
        }

        .logout-btn {
            border: 1px solid rgba(20,184,166,.45);
            background: rgba(20,184,166,.10);
            color: var(--text);
            padding: 9px 12px;
            border-radius: 12px;
            font-weight: 900;
            cursor:pointer;
        }

        .logout-btn:hover {
            background: rgba(20,184,166,.16);
            border-color: rgba(20,184,166,.60);
        }

        .page {
            padding: 18px;
            min-width:0;
        }

        /* Utilisés dans tes pages */
        .page-title {
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
            margin-bottom: 12px;
        }

        .page-title h1 { margin:0; font-size: 24px; font-weight: 950; letter-spacing:.2px; }

        .muted { margin:6px 0 0; color: var(--muted); font-size: 13px; }

        .section {
            background: rgba(255,255,255,.03);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px;
            margin: 12px 0;
            box-shadow: var(--shadow);
        }

        .section-head {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
        }

        table {
            width:100%;
            border-collapse: collapse;
            margin-top: 10px;
            overflow:hidden;
            border-radius: 14px;
        }

        th, td {
            border-bottom: 1px solid rgba(255,255,255,.06);
            text-align:left;
            padding: 12px;
            vertical-align: top;
            font-size: 13px;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255,255,255,.70);
            background: rgba(255,255,255,.03);
        }

        tbody tr:hover{
            background: rgba(20,184,166,.06);
        }

        .btn {
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.04);
            padding: 9px 12px;
            border-radius: 12px;
            font-weight: 900;
            cursor:pointer;
            font-size: 13px;
            color: var(--text);
        }

        .btn:hover {
            background: rgba(20,184,166,.10);
            border-color: rgba(20,184,166,.45);
        }

        .badge {
            display:inline-flex;
            align-items:center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 950;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.04);
            color: var(--text);
        }

        .flash {
            background: rgba(20,184,166,.10);
            border: 1px solid rgba(20,184,166,.32);
            color: rgba(255,255,255,.90);
            padding: 10px 12px;
            border-radius: 14px;
            margin: 12px 0;
            font-weight: 850;
            font-size: 13px;
        }

        .pager { margin-top: 12px; }
        pre { margin:0; white-space: pre-wrap; word-break: break-word; color: rgba(255,255,255,.85); }

        /* Inputs */
        input, select, textarea{
            background: rgba(0,0,0,.15);
            border: 1px solid rgba(255,255,255,.12);
            color: var(--text);
            border-radius: 12px;
        }
        input:focus, select:focus, textarea:focus{
            outline: none;
            border-color: rgba(20,184,166,.55);
            box-shadow: 0 0 0 4px rgba(20,184,166,.12);
        }

        @media (max-width: 900px) {
            .sidebar { width: 230px; }
        }
        @media (max-width: 720px) {
            .app-shell { flex-direction:column; }
            .sidebar { width: auto; height:auto; position: relative; }
        }
    </style>
</head>

<body>
<div class="app-shell">

    <aside class="sidebar">
        <div class="brand">
            <div class="logo">    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
</div>
            <div>
                <div class="title">S7 PROJECT</div>
                <div class="sub">Admin • Marketing Automation</div>
            </div>
        </div>

        <div class="nav">
            <div class="section-title">Navigation</div>

            <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                Clients 
            </a>

            <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                Commandes 
            </a>

            <a href="{{ route('predictions.index') }}" class="{{ request()->routeIs('predictions.*') ? 'active' : '' }}">
                Predictions
            </a>

            <div class="section-title">Automation</div>

            <a href="{{ route('automation.rules') }}" class="{{ request()->routeIs('automation.rules*') ? 'active' : '' }}">
                Rules 
            </a>

            <a href="{{ route('automation.actions') }}" class="{{ request()->routeIs('automation.actions*') ? 'active' : '' }}">
                Actions
            </a>
        </div>
    </aside>

    <main class="content">
        <div class="topbar">
            <div class="left-title">
                {{ $title ?? 'Dashboard' }}
            </div>

            <div class="right">
                @auth
                    <div class="user-pill">
                        {{ auth()->user()->name }}
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-btn" type="submit">Déconnexion</button>
                    </form>
                @endauth
            </div>
        </div>

        <div class="page">
            @yield('content')
        </div>
    </main>

</div>
</body>
</html>
