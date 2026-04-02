@extends('layouts.auth')

@section('title', 'Email envoyé – Event Q&A')

@section('content')

<main class="auth-page">
    <div class="auth-card" style="max-width:26rem;">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">Q&A</div>
            <span style="font-weight:600;font-size:0.875rem;">Event Q&A</span>
        </div>

        {{-- Spinner / Statut --}}
        <div id="sending-state" style="text-align:center;">
            <div style="width:4rem;height:4rem;border-radius:50%;background:color-mix(in srgb,var(--brand) 12%,transparent);display:inline-grid;place-items:center;margin-bottom:1.25rem;">
                <div class="emailjs-spinner"></div>
            </div>
            <h1 style="font-size:1.25rem;">Envoi en cours…</h1>
            <p>Nous envoyons le lien de réinitialisation à<br><strong>{{ $userEmail }}</strong></p>
        </div>

        {{-- État succès (masqué par défaut) --}}
        <div id="success-state" style="display:none;text-align:center;">
            <div style="width:4rem;height:4rem;border-radius:50%;background:#ecfdf5;display:inline-grid;place-items:center;margin-bottom:1.25rem;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h1 style="font-size:1.25rem;">Email envoyé !</h1>
            <p style="margin-bottom:1.5rem;">Un lien de réinitialisation a été envoyé à <strong>{{ $userEmail }}</strong>.<br>Vérifiez votre boîte de réception (et vos spams).</p>
            <div style="background:color-mix(in srgb,var(--brand) 8%,transparent);border:1px solid color-mix(in srgb,var(--brand) 20%,transparent);border-radius:0.75rem;padding:1rem;font-size:0.8125rem;color:var(--muted-fg);margin-bottom:1.5rem;">
                ⏱ Ce lien est valable <strong>60 minutes</strong>.
            </div>
            <a href="{{ route('login') }}" class="btn-brand" style="display:inline-flex;align-items:center;gap:0.5rem;text-decoration:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Retour à la connexion
            </a>
        </div>

        {{-- État erreur (masqué par défaut) --}}
        <div id="error-state" style="display:none;text-align:center;">
            <div style="width:4rem;height:4rem;border-radius:50%;background:#fef2f2;display:inline-grid;place-items:center;margin-bottom:1.25rem;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <h1 style="font-size:1.25rem;">Échec de l'envoi</h1>
            <p style="margin-bottom:1.5rem;">Une erreur s'est produite. Veuillez réessayer.</p>
            <a href="{{ route('password.request') }}" class="btn-brand" style="display:inline-flex;align-items:center;gap:0.5rem;text-decoration:none;background:var(--destructive);">
                Réessayer
            </a>
        </div>

    </div>
</main>

<style>
    .emailjs-spinner {
        width: 2rem;
        height: 2rem;
        border: 3px solid color-mix(in srgb, var(--brand) 20%, transparent);
        border-top-color: var(--brand);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

{{-- EmailJS SDK --}}
<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
<script>
(function() {
    // ─── Configuration EmailJS ───────────────────────────────
    // Remplacez ces valeurs par celles de votre compte EmailJS
    const EMAILJS_PUBLIC_KEY  = '{{ env("EMAILJS_PUBLIC_KEY", "") }}';
    const EMAILJS_SERVICE_ID  = '{{ env("EMAILJS_SERVICE_ID", "") }}';
    const EMAILJS_TEMPLATE_ID = '{{ env("EMAILJS_RESET_TEMPLATE_ID", "") }}';
    // ─────────────────────────────────────────────────────────

    emailjs.init({ publicKey: EMAILJS_PUBLIC_KEY });

    const params = {
        to_name:   '{{ addslashes($userName) }}',
        to_email:  '{{ $userEmail }}',
        reset_url: '{{ $resetUrl }}',
        app_name:  'Event Q&A',
    };

    emailjs.send(EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID, params)
        .then(function() {
            document.getElementById('sending-state').style.display = 'none';
            document.getElementById('success-state').style.display = 'block';
        }, function(error) {
            console.error('EmailJS error:', error);
            document.getElementById('sending-state').style.display = 'none';
            document.getElementById('error-state').style.display = 'block';
        });
})();
</script>

@endsection
