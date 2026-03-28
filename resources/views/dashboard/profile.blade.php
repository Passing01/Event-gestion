@extends('layouts.dashboard')

@section('title', 'Profil – Smart Home')
@section('meta_description', 'Gérez votre profil, préférences et compte.')

@section('content')

<section class="card card-lg">
    <div class="page-header">
        <h1>Profil</h1>
        <p>Gérez votre profil, vos préférences et votre compte.</p>
    </div>

    <div class="profile-grid">

        {{-- Infos utilisateur --}}
        <div class="profile-card">
            <div class="profile-avatar-wrap">
                <div class="profile-avatar">JR</div>
                <div>
                    <p style="font-weight:500;">Jennifer Rhodes</p>
                    <p style="font-size:0.875rem;color:var(--muted-foreground);">jennifer@example.com</p>
                </div>
            </div>
            <div style="margin-top:1rem;">
                <button style="width:100%;background:var(--brand);color:#fff;border:none;border-radius:0.5rem;padding:0.5rem;font-size:0.875rem;cursor:pointer;">
                    Modifier le profil
                </button>
            </div>
        </div>

        {{-- Préférences --}}
        <div class="profile-card">
            <h3 style="font-weight:500;margin-bottom:0.75rem;">Préférences</h3>

            <div class="pref-row">
                <span class="pref-label">Notifications</span>
                <label class="toggle-switch">
                    <input type="checkbox" class="toggle-input" checked
                           onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                    <span class="toggle-track" style="background:var(--brand);"></span>
                    <span class="toggle-thumb" style="transform:translateX(1.125rem);"></span>
                </label>
            </div>

            <div class="pref-row">
                <span class="pref-label">Mode économie d'énergie</span>
                <label class="toggle-switch">
                    <input type="checkbox" class="toggle-input"
                           onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                    <span class="toggle-track"></span>
                    <span class="toggle-thumb"></span>
                </label>
            </div>

            <div class="pref-row">
                <span class="pref-label">Mises à jour automatiques</span>
                <label class="toggle-switch">
                    <input type="checkbox" class="toggle-input" checked
                           onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                    <span class="toggle-track" style="background:var(--brand);"></span>
                    <span class="toggle-thumb" style="transform:translateX(1.125rem);"></span>
                </label>
            </div>
        </div>

        {{-- Raccourcis --}}
        <div class="profile-card">
            <h3 style="font-weight:500;margin-bottom:0.75rem;">Raccourcis</h3>
            <div class="shortcut-tags">
                <div class="shortcut-tag">🌅 Bon matin</div>
                <div class="shortcut-tag">🌙 Bonne nuit</div>
                <div class="shortcut-tag">🏠 Absent</div>
                <div class="shortcut-tag">🎬 Cinéma</div>
            </div>
        </div>

    </div>

    {{-- Section sécurité compte --}}
    <div class="card" style="margin-top:1.5rem;">
        <h3 style="font-weight:500;margin-bottom:0.75rem;">Sécurité du compte</h3>
        <div style="display:grid;gap:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="font-weight:500;font-size:0.875rem;">Mot de passe</p>
                    <p style="font-size:0.75rem;color:var(--muted-foreground);">Dernière modification il y a 30 jours</p>
                </div>
                <button style="background:var(--muted);border:none;border-radius:0.5rem;padding:0.375rem 0.75rem;font-size:0.875rem;cursor:pointer;color:var(--foreground);">
                    Modifier
                </button>
            </div>
            <div style="height:1px;background:var(--border);"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="font-weight:500;font-size:0.875rem;">Authentification 2 facteurs</p>
                    <p style="font-size:0.75rem;color:var(--muted-foreground);">Non activée</p>
                </div>
                <button style="background:var(--brand);color:#fff;border:none;border-radius:0.5rem;padding:0.375rem 0.75rem;font-size:0.875rem;cursor:pointer;">
                    Activer
                </button>
            </div>
        </div>
    </div>

</section>

@endsection
