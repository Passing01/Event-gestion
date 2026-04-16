@extends('layouts.public')

@section('title', 'Salle d\'attente — ' . $event->name)

@section('extra_css')
<style>
    .waiting-room {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem;
        background: linear-gradient(135deg, var(--brand-light) 0%, #ffffff 100%);
    }

    .countdown-container {
        display: flex;
        gap: 1.5rem;
        margin: 3rem 0;
    }

    .countdown-item {
        background: #fff;
        padding: 1.5rem;
        border-radius: 1.5rem;
        min-width: 6rem;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border);
    }

    .countdown-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--brand);
        display: block;
        line-height: 1;
    }

    .countdown-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--muted-foreground);
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .event-info-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        padding: 2.5rem;
        border-radius: 2rem;
        border: 1px solid var(--border);
        max-width: 600px;
        width: 100%;
    }

    .pulse-dot {
        width: 12px;
        height: 12px;
        background: var(--brand);
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(124, 58, 237, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(124, 58, 237, 0); }
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        background: var(--brand-light);
        color: var(--brand);
        padding: 0.5rem 1rem;
        border-radius: 999px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 2rem;
    }

    @media (max-width: 600px) {
        .countdown-container { gap: 0.75rem; }
        .countdown-item { min-width: 4.5rem; padding: 1rem; }
        .countdown-value { font-size: 1.75rem; }
    }
</style>
@endsection

@section('content')
<div class="waiting-room">
    <div class="container container-tight" style="max-width: 800px;">
        
        <div class="status-badge">
            <span class="pulse-dot"></span>
            En attente de l'ouverture
        </div>

        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem;">{{ $event->name }}</h1>
        <p style="color: var(--muted-foreground); font-size: 1.125rem;">Préparez-vous ! L'événement commencera dans quelques instants.</p>

        <div class="countdown-container" id="countdown">
            <div class="countdown-item">
                <span class="countdown-value" id="days">00</span>
                <span class="countdown-label">Jours</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-value" id="hours">00</span>
                <span class="countdown-label">Heures</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-value" id="minutes">00</span>
                <span class="countdown-label">Min</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-value" id="seconds">00</span>
                <span class="countdown-label">Sec</span>
            </div>
        </div>

        <div class="event-info-card">
            <p style="font-weight: 600; margin-bottom: 0.5rem; color: var(--foreground);">À propos de cet événement :</p>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); line-height: 1.6;">
                {{ $event->description ?: "Aucune description fournie pour cet événement." }}
            </p>
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--muted-foreground);">
                ID de session : <span style="font-family: monospace; font-weight: 700;">{{ $event->code }}</span>
            </div>
        </div>

    </div>
</div>
@endsection

@section('extra_js')
<script>
    const scheduledAt = new Date("{{ $event->scheduled_at->toIso8601String() }}").getTime();
    const eventCode = "{{ $event->code }}";
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = scheduledAt - now;

        if (distance < 0) {
            document.getElementById('days').innerText = "00";
            document.getElementById('hours').innerText = "00";
            document.getElementById('minutes').innerText = "00";
            document.getElementById('seconds').innerText = "00";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('days').innerText = String(days).padStart(2, '0');
        document.getElementById('hours').innerText = String(hours).padStart(2, '0');
        document.getElementById('minutes').innerText = String(minutes).padStart(2, '0');
        document.getElementById('seconds').innerText = String(seconds).padStart(2, '0');
    }

    // Polling pour vérifier si l'événement a été activé manuellement
    function checkStatus() {
        fetch(`/participant/event-info/${eventCode}`)
            .then(res => res.json())
            .then(data => {
                if (data.is_open) {
                    window.location.href = `/e/${eventCode}`;
                }
            })
            .catch(err => console.error("Erreur de polling:", err));
    }

    setInterval(updateCountdown, 1000);
    setInterval(checkStatus, 5000); // Vérifier toutes les 5 secondes
    updateCountdown();
</script>
@endsection
