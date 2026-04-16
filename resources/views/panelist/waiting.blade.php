@extends('layouts.dashboard')

@section('title', 'Attente d\'ouverture — ' . $event->name)

@section('content')
<div class="space-y-6">
    <div class="page-header" style="text-align: center; margin-bottom: 3rem;">
        <div style="display: inline-flex; align-items: center; background: var(--brand-light); color: var(--brand); padding: 0.5rem 1rem; border-radius: 999px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem;">
            <span class="pulse-dot" style="width: 10px; height: 10px; background: var(--brand); border-radius: 50%; display: inline-block; margin-right: 8px; animation: pulse 2s infinite;"></span>
            Événement programmé
        </div>
        <h1>{{ $event->name }}</h1>
        <p>L'accès à la console panéliste sera disponible dès l'ouverture officielle.</p>
    </div>

    <div style="display: flex; gap: 1.5rem; justify-content: center; margin: 3rem 0;">
        <div class="card" style="min-width: 6rem; text-align: center; padding: 1.5rem;">
            <span id="days" style="font-size: 2rem; font-weight: 800; color: var(--brand); display: block;">00</span>
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--muted-foreground);">Jours</span>
        </div>
        <div class="card" style="min-width: 6rem; text-align: center; padding: 1.5rem;">
            <span id="hours" style="font-size: 2rem; font-weight: 800; color: var(--brand); display: block;">00</span>
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--muted-foreground);">Heures</span>
        </div>
        <div class="card" style="min-width: 6rem; text-align: center; padding: 1.5rem;">
            <span id="minutes" style="font-size: 2rem; font-weight: 800; color: var(--brand); display: block;">00</span>
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--muted-foreground);">Min</span>
        </div>
        <div class="card" style="min-width: 6rem; text-align: center; padding: 1.5rem;">
            <span id="seconds" style="font-size: 2rem; font-weight: 800; color: var(--brand); display: block;">00</span>
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--muted-foreground);">Sec</span>
        </div>
    </div>

    <div class="card" style="max-width: 500px; margin: 0 auto; text-align: center;">
        <h3 style="font-weight: 700; margin-bottom: 1rem;">Note pour le Panéliste</h3>
        <p style="font-size: 0.875rem; color: var(--muted-foreground); line-height: 1.5;">
            Vous êtes bien enregistré pour cet événement. Une fois le décompte terminé, cette page se rafraîchira automatiquement pour vous donner accès aux outils de présentation.
        </p>
    </div>
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(124, 58, 237, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(124, 58, 237, 0); }
    }
</style>
@endsection

@push('scripts')
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

    function checkStatus() {
        fetch(`/panelist/event-info/${eventCode}`)
            .then(res => res.json())
            .then(data => {
                if (data.is_open) {
                    window.location.href = `/panelist/e/${eventCode}`;
                }
            })
            .catch(err => console.error("Erreur de polling:", err));
    }

    setInterval(updateCountdown, 1000);
    setInterval(checkStatus, 5000);
    updateCountdown();
</script>
@endpush
