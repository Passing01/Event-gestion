@extends('layouts.dashboard')

@section('title', 'Sécurité – Smart Home')
@section('meta_description', 'Gestion des caméras, capteurs et alertes de sécurité.')

@section('content')

<section class="card card-lg">
    <div class="page-header">
        <h1>Sécurité</h1>
        <p>Gérez vos caméras, capteurs et alertes de sécurité.</p>
    </div>

    {{-- Contrôles de sécurité --}}
    <div class="security-grid">

        {{-- Serrure porte --}}
        <div class="security-item">
            <div class="security-item-header">
                <div class="security-item-label">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Serrure porte
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" class="toggle-input" checked
                           onchange="updateSecStatus('door-status', this.checked, 'Porte d\'entrée verrouillée.', 'Porte d\'entrée déverrouillée.')">
                    <span class="toggle-track" style="background:var(--brand);"></span>
                    <span class="toggle-thumb" style="transform:translateX(1.125rem);"></span>
                </label>
            </div>
            <p class="security-item-desc" id="door-status">Porte d'entrée verrouillée.</p>
        </div>

        {{-- Caméras --}}
        <div class="security-item">
            <div class="security-item-header">
                <div class="security-item-label">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Caméras
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" class="toggle-input" checked
                           onchange="updateSecStatus('cam-status', this.checked, 'Toutes les caméras enregistrent.', 'Enregistrement en pause.')">
                    <span class="toggle-track" style="background:var(--brand);"></span>
                    <span class="toggle-thumb" style="transform:translateX(1.125rem);"></span>
                </label>
            </div>
            <p class="security-item-desc" id="cam-status">Toutes les caméras enregistrent.</p>
        </div>

        {{-- Alarme --}}
        <div class="security-item">
            <div class="security-item-header">
                <div class="security-item-label">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Alarme
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" class="toggle-input"
                           onchange="updateSecStatus('alarm-status', this.checked, 'Système armé.', 'Système désarmé.')">
                    <span class="toggle-track"></span>
                    <span class="toggle-thumb"></span>
                </label>
            </div>
            <p class="security-item-desc" id="alarm-status">Système désarmé.</p>
        </div>

    </div>

    {{-- Activité récente --}}
    <div class="card" style="margin-top:1.5rem;">
        <h3 style="font-weight:500;margin-bottom:0.75rem;">Activité récente</h3>
        @php
        $events = [
            ['id'=>1, 'time'=>'08:32',    'text'=>'Porte d\'entrée verrouillée'],
            ['id'=>2, 'time'=>'07:15',    'text'=>'Caméra jardin : mouvement détecté'],
            ['id'=>3, 'time'=>'Hier',     'text'=>'Alarme désarmée par Jennifer'],
        ];
        @endphp
        <ul class="activity-list">
            @foreach($events as $e)
            <li class="activity-item">
                <span>{{ $e['text'] }}</span>
                <span class="text-muted">{{ $e['time'] }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</section>

@push('scripts')
<script>
    function updateSecStatus(id, checked, onMsg, offMsg) {
        const el = document.getElementById(id);
        if (el) el.textContent = checked ? onMsg : offMsg;
        // Sync toggle track
        const track = event.target.nextElementSibling;
        const thumb  = event.target.parentElement.querySelector('.toggle-thumb');
        if (track) track.style.background = checked ? 'var(--brand)' : 'var(--muted)';
        if (thumb)  thumb.style.transform  = checked ? 'translateX(1.125rem)' : 'translateX(0)';
    }
</script>
@endpush

@endsection
