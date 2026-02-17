<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Automation - App</title>

    <style>
        body { margin: 0; font-family: Arial, sans-serif; }

        /* Layout global */
        .layout {
            display: flex;
            height: 100vh;
        }

        /* Sidebar à gauche */
        .sidebar {
            width: 240px;
            background: #111827;
            color: #fff;
            padding: 16px;
            box-sizing: border-box;
        }

        .brand {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 16px;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 12px;
        }

        /* Boutons (liens) */
        .nav a {
            display: block;
            padding: 10px 12px;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            background: rgba(255,255,255,0.08);
        }

        .nav a:hover {
            background: rgba(255,255,255,0.16);
        }

        /* Contenu au centre */
        .content {
            flex: 1;
            background: #f3f4f6;
            padding: 0;
        }

        /* Iframe plein écran dans la zone content */
        .content iframe {
            width: 100%;
            height: 100%;
            border: 0;
            background: #fff;
        }

        /* Petit footer sidebar */
        .sidebar-footer {
            position: absolute;
            bottom: 16px;
            left: 16px;
            right: 16px;
            font-size: 12px;
            opacity: 0.7;
        }

        .sidebar-wrap {
            position: relative;
            height: 100%;
        }
    </style>
</head>
<body>

    <div class="layout">

        <aside class="sidebar">
            <div class="sidebar-wrap">
                <div class="brand">Marketing Automation</div>

                <div class="nav">
                    <!-- target="appFrame" => ouvre dans l'iframe, pas dans la page -->
                    <a href="{{ route('dashboard.index') }}" target="appFrame">Dashboard</a>

                    <a href="{{ route('predictions.index', ['type' => 'churn']) }}" target="appFrame">Predictions (Churn)</a>
                    <a href="{{ route('predictions.index', ['type' => 'sales']) }}" target="appFrame">Predictions (Sales)</a>
                    <a href="{{ route('predictions.index', ['type' => 'engagement']) }}" target="appFrame">Predictions (Engagement)</a>

                    <a href="{{ route('automation.rules') }}" target="appFrame">Automation - Rules</a>
                    <a href="{{ route('automation.actions', ['status' => 'pending']) }}" target="appFrame">Automation - Actions</a>

                    <a href="{{ route('customers.atRisk') }}" target="appFrame">Customers - At Risk</a>
                </div>

                <div class="sidebar-footer">
                    MVC Laravel + MySQL
                </div>
            </div>
        </aside>

        <main class="content">
            <!-- iframe : on démarre avec le dashboard par défaut -->
            <iframe
                name="appFrame"
                src="{{ route('dashboard.index') }}"
                title="App Content"
            ></iframe>
        </main>

    </div>

</body>
</html>
