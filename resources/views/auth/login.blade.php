{{-- <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>

    <style>
        :root{
            --bg:#0b1220;
            --card:#0f172a;
            --border: rgba(255,255,255,0.10);
            --text:#e5e7eb;
            --muted:#9ca3af;
            --primary:#22c55e;
            --primaryHover:#16a34a;
            --danger:#ef4444;
        }

        * { box-sizing: border-box; }

        body{
            margin:0;
            font-family: Arial, sans-serif;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
            background:
                radial-gradient(800px 400px at 20% 10%, rgba(34,197,94,0.18), transparent 60%),
                radial-gradient(800px 400px at 80% 90%, rgba(59,130,246,0.14), transparent 60%),
                var(--bg);
            color: var(--text);
        }

        .wrap{
            width: 440px;
            max-width: 100%;
        }

        .brand{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:14px;
        }

        .logo{
            width:38px;
            height:38px;
            border-radius:12px;
            background: rgba(34,197,94,0.18);
            border: 1px solid rgba(34,197,94,0.35);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:800;
            color:#86efac;
        }

        .brand h1{
            margin:0;
            font-size:18px;
        }

        .brand p{
            margin:3px 0 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .card{
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 18px 60px rgba(0,0,0,0.35);
            backdrop-filter: blur(8px);
        }

        .title{
            margin:0;
            font-size: 18px;
        }

        .subtitle{
            margin:6px 0 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        label{
            display:block;
            margin-top: 14px;
            font-size: 13px;
            font-weight: 700;
            color: #d1d5db;
        }

        input{
            width: 100%;
            margin-top: 6px;
            padding: 11px 12px;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            outline: none;
            font-size: 14px;
            background: rgba(255,255,255,0.04);
            color: var(--text);
        }

        input::placeholder{ color: rgba(229,231,235,0.45); }

        input:focus{
            border-color: rgba(34,197,94,0.55);
            box-shadow: 0 0 0 3px rgba(34,197,94,0.18);
        }

        .row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-top: 12px;
            gap: 10px;
        }

        .checkbox{
            display:flex;
            align-items:center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
            user-select:none;
        }

        .checkbox input{
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--primary);
        }

        .btn{
            width: 100%;
            margin-top: 14px;
            padding: 11px 12px;
            border: 0;
            border-radius: 12px;
            background: var(--primary);
            color: #052e12;
            cursor: pointer;
            font-size: 14px;
            font-weight: 800;
        }

        .btn:hover{
            background: var(--primaryHover);
        }

        .errors{
            margin-top: 12px;
            padding: 10px;
            border: 1px solid rgba(239,68,68,0.35);
            background: rgba(239,68,68,0.12);
            border-radius: 12px;
            color: #fecaca;
            font-size: 13px;
        }

        .status{
            margin-top: 12px;
            padding: 10px;
            border: 1px solid rgba(34,197,94,0.35);
            background: rgba(34,197,94,0.12);
            border-radius: 12px;
            color: #bbf7d0;
            font-size: 13px;
        }

        .hint{
            margin-top: 12px;
            font-size: 12px;
            color: rgba(156,163,175,0.9);
            text-align:center;
        }

        .hint code{
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            padding: 2px 6px;
            border-radius: 8px;
            color: #e5e7eb;
        }
    </style>
</head>
<body>

    <div class="wrap">

        <div class="brand">
            <div class="logo">MA</div>
            <div>
                <h1>Marketing Automation</h1>
                <p>Admin panel</p>
            </div>
        </div>

        <div class="card">
            <h2 class="title">Connexion</h2>
            <p class="subtitle">Entre tes identifiants admin pour accéder au dashboard.</p>

            @if (session('status'))
                <div class="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    <ul style="margin:0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@admin.com"
                >

                <label for="password">Mot de passe</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="admin12345"
                >

                <div class="row">
                    <label class="checkbox">
                        <input type="checkbox" name="remember">
                        Se souvenir de moi
                    </label>
                </div>

                <button class="btn" type="submit">Se connecter</button>
            </form>

            <div class="hint">
                Test : <code>admin@admin.com</code> / <code>admin12345</code>
            </div>
        </div>

    </div>

</body>
</html> --}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <link rel="stylesheet" href="{{ asset('css/app-theme.css') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
 <div style="display:flex; justify-content:center; margin-bottom:14px;">
    <div style="width:72px; height:72px; border-radius:18px; overflow:hidden; border:1px solid rgba(255,255,255,.14); background:rgba(255,255,255,.06);">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:100%; height:100%; object-fit:cover; display:block;">
    </div>
</div>
        <div class="auth-header">
            <h1>Connexion</h1>
            <p>Accède au dashboard d’administration</p>
        </div>


        @if ($errors->any())
            <div class="flash" style="border-color: rgba(239,68,68,.35); background: rgba(239,68,68,.12); margin-top:12px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li style="margin:4px 0;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" style="margin-top:14px;">
            @csrf

            <div style="display:flex; flex-direction:column; gap:12px;">
                <div>
                    <label style="font-weight:900; font-size:13px;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@email.com">
                </div>

                <div>
                    <label style="font-weight:900; font-size:13px;">Mot de passe</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <label style="display:flex; align-items:center; gap:8px; color: rgba(255,255,255,.75); font-weight:800;">
                        <input type="checkbox" name="remember" style="width:auto;">
                        Se souvenir de moi
                    </label>
                </div>

                <button class="btn btn-primary" type="submit" style="width:100%;">
                    Se connecter
                </button>
            </div>
        </form>

        <div class="muted" style="font-size:12px; margin-top:14px; text-align:center;">
            © {{ date('Y') }} Marketing Automation
        </div>
    </div>
</div>
</body>
</html>
