@extends('layouts.dashboard')

@section('title', $event->name)

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="{{ route('dashboard.events.index') }}" style="font-size: 0.75rem; color: var(--muted-foreground); text-decoration: none;">← Retour à la liste</a>
                <h1 style="margin-top: 0.5rem;">{{ $event->name }}</h1>
                <p>Code de participation : <strong>{{ $event->code }}</strong></p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <form action="{{ route('dashboard.events.toggle-status', $event->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-brand" style="background: {{ $event->status == 'active' ? '#f3f4f6' : '#ecfdf5' }}; color: {{ $event->status == 'active' ? '#374151' : '#059669' }};">
                        {{ $event->status == 'active' ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
                <a href="{{ route('dashboard.events.edit', $event->id) }}" class="btn-brand" style="background: var(--muted); color: var(--foreground);">Modifier</a>
                <a href="{{ route('dashboard.moderator.index', $event->id) }}" class="btn-brand">Ouvrir la Console</a>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Questions</h3>
            <p style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $event->questions_count }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Date</h3>
            <p style="font-size: 1.25rem; font-weight: 600; margin-top: 0.5rem;">{{ $event->date->format('d/m/Y') }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Statut</h3>
            <p style="font-size: 1.25rem; font-weight: 600; margin-top: 0.5rem;">
                <span class="badge" style="background: {{ $event->status == 'active' ? '#ecfdf5' : '#f3f4f6' }}; color: {{ $event->status == 'active' ? '#059669' : '#6b7280' }};">
                    {{ ucfirst($event->status) }}
                </span>
            </p>
        </div>
    </div>

    <div class="card">
        <h2 class="section-title">Accès Participant</h2>
        <p style="margin-bottom: 1.5rem;">Partagez ce lien ou ce code avec votre public pour qu'ils puissent poser des questions.</p>
        
        <div style="display: flex; gap: 2rem; align-items: center;">
            <div style="flex: 1;">
                <div style="display: flex; gap: 1rem; align-items: center; background: var(--muted); padding: 1rem; border-radius: 0.75rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <p style="font-size: 0.75rem; color: var(--muted-foreground);">Lien direct</p>
                        <p style="font-weight: 500; font-size: 0.875rem;">{{ route('participant.join', ['code' => $event->code]) }}</p>
                    </div>
                    <button class="btn-brand" style="width: auto; padding: 0.5rem 1rem;" onclick="navigator.clipboard.writeText('{{ route('participant.join', ['code' => $event->code]) }}')">Copier</button>
                </div>
                <div style="background: var(--brand-light); color: var(--brand); padding: 1rem; border-radius: 0.75rem; text-align: center;">
                    <p style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Code de l'événement</p>
                    <p style="font-size: 2rem; font-weight: 800;">{{ $event->code }}</p>
                </div>
            </div>
            
            <div style="background: #fff; padding: 1rem; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center;">
                <img id="qrcode-img" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('participant.join', ['code' => $event->code])) }}" alt="QR Code" style="width: 150px; height: 150px;">
                <p style="font-size: 0.625rem; color: var(--muted-foreground); margin-top: 0.5rem; margin-bottom: 0.5rem;">Scannez pour rejoindre</p>
                <button onclick="downloadQRCode()" style="background: none; border: none; color: var(--brand); font-size: 0.75rem; font-weight: 600; cursor: pointer; text-decoration: underline;">Télécharger</button>
            </div>
        </div>
    </div>
</div>

<script>
    async function downloadQRCode() {
        const img = document.getElementById('qrcode-img');
        const response = await fetch(img.src);
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'qrcode-{{ $event->code }}.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }
</script>

@endsection
