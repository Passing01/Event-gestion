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

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
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

        <div class="card">
            <h2 class="section-title">Panélistes</h2>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1rem;">Ajoutez des panélistes pour cet événement.</p>
            
            <form action="{{ route('dashboard.events.panelists.store', $event->id) }}" method="POST" style="margin-bottom: 1.5rem;">
                @csrf
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <input type="text" name="name" class="form-input" placeholder="Nom complet" required style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                </div>
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <input type="email" name="email" class="form-input" placeholder="Email" required style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                </div>
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <input type="text" name="sector" class="form-input" placeholder="Secteur d'activité" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                </div>
                <button type="submit" class="btn-brand" style="width: 100%; font-size: 0.875rem; padding: 0.5rem;">Ajouter Panéliste</button>
            </form>

            <div style="max-height: 300px; overflow-y: auto;">
                @forelse($event->panelists as $panelist)
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-bottom: 1px solid var(--border);">
                        <div style="width: 2.5rem; height: 2.5rem; background: var(--brand); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">
                            {{ substr($panelist->user->name, 0, 1) }}
                        </div>
                        <div style="flex: 1;">
                            <p style="font-size: 0.875rem; font-weight: 600;">{{ $panelist->user->name }}</p>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $panelist->sector ?? 'Aucun secteur' }}</p>
                            <p style="font-size: 0.625rem; color: var(--muted-foreground);">{{ $panelist->user->email }}</p>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <button onclick="openEditPanelistModal('{{ $panelist->id }}', '{{ addslashes($panelist->user->name) }}', '{{ addslashes($panelist->sector) }}')" style="background: none; border: none; color: var(--brand); cursor: pointer; padding: 0.25rem;" title="Modifier">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            <form action="{{ route('dashboard.events.panelists.destroy', $panelist->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer ce panéliste ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--destructive); cursor: pointer; padding: 0.25rem;" title="Supprimer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p style="font-size: 0.75rem; color: var(--muted-foreground); text-align: center; padding: 1rem;">Aucun panéliste ajouté.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Édition Panéliste -->
<div id="edit-panelist-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div class="card" style="max-width: 25rem; width: 90%;">
        <h2 class="section-title">Modifier Panéliste</h2>
        <form id="edit-panelist-form" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 0.75rem;">
                <label class="form-label">Nom complet</label>
                <input type="text" name="name" id="edit-panelist-name" class="form-input" required>
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Secteur d'activité</label>
                <input type="text" name="sector" id="edit-panelist-sector" class="form-input">
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn-brand" style="background: var(--muted); color: var(--foreground);" onclick="document.getElementById('edit-panelist-modal').style.display='none'">Annuler</button>
                <button type="submit" class="btn-brand">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditPanelistModal(id, name, sector) {
        const modal = document.getElementById('edit-panelist-modal');
        const form = document.getElementById('edit-panelist-form');
        const nameInput = document.getElementById('edit-panelist-name');
        const sectorInput = document.getElementById('edit-panelist-sector');
        
        form.action = `/dashboard/events/panelists/${id}`;
        nameInput.value = name;
        sectorInput.value = sector;
        modal.style.display = 'flex';
    }

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
