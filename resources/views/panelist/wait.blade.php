<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès Paneliste : {{ $event->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap">
    <style>
        :root {
            --brand: {{ $event->user->brand_color ?? '#10b981' }};
            --brand-soft: {{ ($event->user->brand_color ?? '#10b981') . '15' }};
            --bg: #0f172a;
            --text: #f8fafc;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            text-align: center;
        }
        .container {
            width: 90%;
            max-width: 600px;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2.5rem;
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .event-badge {
            display: inline-block;
            background: var(--brand-soft);
            color: var(--brand);
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            border: 1px solid var(--brand);
        }
        h1 { font-size: 2rem; font-weight: 800; margin-bottom: 1rem; letter-spacing: -0.02em; }
        p { color: #94a3b8; line-height: 1.6; margin-bottom: 3rem; }
        
        .countdown {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .unit {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--brand);
            line-height: 1;
        }
        .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            margin-top: 0.5rem;
        }

        .pulse-loader {
            width: 80px;
            height: 80px;
            background: var(--brand);
            border-radius: 50%;
            margin: 0 auto 2rem;
            position: relative;
            animation: pulse 2s infinite ease-out;
        }
        @keyframes pulse {
            0% { transform: scale(0.9); box-shadow: 0 0 0 0 var(--brand); }
            70% { transform: scale(1); box-shadow: 0 0 0 40px rgba(0,0,0,0); }
            100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(0,0,0,0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="pulse-loader"></div>
        <div class="event-badge">ACCÈS EXPERT PROGRAMMÉ</div>
        <h1>Préparez-vous, {{ Auth::user()->name }}</h1>
        <p>L'événement {{ $event->name }} démarrera sous peu. Votre console de gestion de documents et de réponses sera déverrouillée à l'heure prévue.</p>

        <div class="countdown" id="timer">
            <div class="unit">
                <span class="number" id="days">00</span>
                <span class="label">Jours</span>
            </div>
            <div class="unit">
                <span class="number" id="hours">00</span>
                <span class="label">Heures</span>
            </div>
            <div class="unit">
                <span class="number" id="minutes">00</span>
                <span class="label">Min</span>
            </div>
            <div class="unit">
                <span class="number" id="seconds">00</span>
                <span class="label">Sec</span>
            </div>
        </div>

        <div style="font-size: 0.75rem; color: #475569; margin-top: 2rem;">Vérifiez vos documents pendant cette attente.</div>
    </div>

    <script>
        const targetDate = new Date("{{ $event->scheduled_at ? $event->scheduled_at->toIso8601String() : $event->created_at->toIso8601String() }}").getTime();

        function updateTimer() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                window.location.reload();
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerText = String(days).padStart(2, '0');
            document.getElementById("hours").innerText = String(hours).padStart(2, '0');
            document.getElementById("minutes").innerText = String(minutes).padStart(2, '0');
            document.getElementById("seconds").innerText = String(seconds).padStart(2, '0');
        }

        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>
</html>
