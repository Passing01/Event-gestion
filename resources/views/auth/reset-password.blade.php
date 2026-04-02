@extends('layouts.auth')

@section('title', 'Nouveau mot de passe – Event Q&A')

@section('content')

<main class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.75rem;">Q&A</div>
            <span style="font-weight:600;font-size:0.875rem;">Event Q&A</span>
        </div>

        {{-- Icône --}}
        <div style="width:3.5rem;height:3.5rem;border-radius:1rem;background:color-mix(in srgb,var(--brand) 12%,transparent);display:grid;place-items:center;margin-bottom:1.25rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>

        <h1>Nouveau mot de passe</h1>
        <p style="margin-bottom:1.5rem;">Choisissez un mot de passe fort d'au moins 8 caractères.</p>

        {{-- Erreur --}}
        @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;display:flex;gap:0.5rem;align-items:flex-start;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" id="reset-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label class="form-label" for="password">Nouveau mot de passe</label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="••••••••" required autocomplete="new-password"
                           style="padding-right:2.75rem;">
                    <button type="button" id="toggle-pwd" onclick="togglePassword('password', 'eye-pwd')"
                            style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted-fg);padding:0;">
                        <svg id="eye-pwd" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror

                {{-- Indicateur de force --}}
                <div style="margin-top:0.5rem;">
                    <div style="height:4px;border-radius:2px;background:var(--border);overflow:hidden;">
                        <div id="strength-bar" style="height:100%;width:0%;border-radius:2px;transition:width 0.3s,background 0.3s;"></div>
                    </div>
                    <span id="strength-label" style="font-size:0.7rem;color:var(--muted-fg);margin-top:0.25rem;display:block;"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                <div style="position:relative;">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                           placeholder="••••••••" required autocomplete="new-password"
                           style="padding-right:2.75rem;">
                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                            style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted-fg);padding:0;">
                        <svg id="eye-confirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <span id="match-msg" style="font-size:0.75rem;display:none;margin-top:0.25rem;"></span>
            </div>

            <button type="submit" class="btn-brand" id="submit-btn" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Réinitialiser le mot de passe
            </button>
        </form>

    </div>
</main>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(iconId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    eye.innerHTML = isHidden
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}

// Indicateur de force du mot de passe
const pwdInput = document.getElementById('password');
const confirmInput = document.getElementById('password_confirmation');
const strengthBar = document.getElementById('strength-bar');
const strengthLabel = document.getElementById('strength-label');
const matchMsg = document.getElementById('match-msg');

pwdInput.addEventListener('input', function() {
    const val = this.value;
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '25%', color: '#ef4444', label: 'Très faible' },
        { pct: '50%', color: '#f97316', label: 'Faible' },
        { pct: '75%', color: '#eab308', label: 'Moyen' },
        { pct: '100%', color: '#22c55e', label: 'Fort' },
    ];
    const lvl = levels[Math.max(0, score - 1)];
    if (val.length === 0) {
        strengthBar.style.width = '0%';
        strengthLabel.textContent = '';
    } else {
        strengthBar.style.width  = lvl.pct;
        strengthBar.style.background = lvl.color;
        strengthLabel.textContent = lvl.label;
        strengthLabel.style.color = lvl.color;
    }
    checkMatch();
});

confirmInput.addEventListener('input', checkMatch);

function checkMatch() {
    if (!confirmInput.value) {
        matchMsg.style.display = 'none';
        return;
    }
    matchMsg.style.display = 'block';
    if (pwdInput.value === confirmInput.value) {
        matchMsg.textContent = '✓ Les mots de passe correspondent';
        matchMsg.style.color = '#22c55e';
    } else {
        matchMsg.textContent = '✗ Les mots de passe ne correspondent pas';
        matchMsg.style.color = '#ef4444';
    }
}
</script>

@endsection
