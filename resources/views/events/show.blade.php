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
                <button class="btn-brand open-edit-event-modal-btn" data-event='@json($event)' style="background: var(--muted); color: var(--foreground); border: none; cursor: pointer;">Modifier</button>
                <a href="{{ route('dashboard.moderator.index', $event->id) }}" class="btn-brand">Ouvrir la Console</a>
            </div>
        </div>
    </div>

    {{-- Modal de Modification d'Événement --}}
    <div id="edit-event-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:1.5rem; backdrop-filter: blur(8px); overflow-y: auto;">
        <div class="card" style="width:100%; max-width:40rem; padding: 2rem; border-radius: 1.5rem; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative; text-align: left;">
            <button onclick="closeEditEventModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: var(--muted); border: none; width: 2rem; height: 2rem; border-radius: 50%; font-size: 1.25rem; cursor: pointer; color: var(--muted-foreground);">&times;</button>
            
            <h2 style="font-size: 1.5rem; font-weight: 900; color: var(--foreground); margin-bottom: 0.5rem;">Modifier l'événement</h2>
            <p style="color: var(--muted-foreground); margin-bottom: 2rem; font-size: 0.875rem;">Mettez à jour les détails de votre session.</p>
            
            <form id="edit-event-form" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label class="form-label">Bannière actuelle</label>
                    <div id="edit-current-banner" style="margin-bottom: 1rem;">
                        <!-- Image injectée en JS -->
                    </div>
                    <label class="form-label" for="edit_image">Changer de bannière</label>
                    <input type="file" id="edit_image" name="image" class="form-input" style="padding: 0.5rem; font-size: 0.75rem;" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_name">Nom de l'événement</label>
                    <input type="text" id="edit_name" name="name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_description">Description (pour l'IA)</label>
                    <textarea id="edit_description" name="description" class="form-input" rows="3"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="edit_date">Date début</label>
                        <input type="date" id="edit_date" name="date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_end_date">Date fin</label>
                        <input type="date" id="edit_end_date" name="end_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_scheduled_at">Heure début</label>
                        <input type="datetime-local" id="edit_scheduled_at" name="scheduled_at" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_status">Statut</label>
                    <select id="edit_status" name="status" class="form-input" style="appearance: auto;">
                        <option value="active">Actif</option>
                        <option value="archived">Archivé</option>
                    </select>
                </div>

                <div style="height: 1px; background: var(--border); margin: 1.5rem 0;"></div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.875rem; font-weight: 600;">Modération</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="moderation_enabled" id="edit_moderation_enabled" value="1" class="toggle-input">
                            <span class="toggle-track"></span>
                            <span class="toggle-thumb"></span>
                        </label>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.875rem; font-weight: 600;">Anonyme</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="anonymous_allowed" id="edit_anonymous_allowed" value="1" class="toggle-input">
                            <span class="toggle-track"></span>
                            <span class="toggle-thumb"></span>
                        </label>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.875rem; font-weight: 600;">Marketplace</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_on_marketplace" id="edit_is_on_marketplace" value="1" class="toggle-input">
                            <span class="toggle-track"></span>
                            <span class="toggle-thumb"></span>
                        </label>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.875rem; font-weight: 600;">Présence</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="collect_presence" id="edit_collect_presence" value="1" class="toggle-input">
                            <span class="toggle-track"></span>
                            <span class="toggle-thumb"></span>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <button type="button" onclick="closeEditEventModal()" class="btn-brand" style="background: var(--muted); color: var(--foreground); flex: 1; border: none;">Annuler</button>
                    <button type="submit" class="btn-brand" style="flex: 2; border: none;">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Questions</h3>
            <p style="font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">{{ $event->questions_count }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Date</h3>
            <p style="font-size: 1.25rem; font-weight: 600; margin-top: 0.5rem;">
                {{ $event->date->format('d/m/Y') }}
                @if($event->end_date)
                    <span style="font-size: 0.875rem; color: var(--muted-foreground); font-weight: 400;">au</span>
                    {{ $event->end_date->format('d/m/Y') }}
                @endif
            </p>
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

    @if($event->collect_presence)
    <div class="card" style="margin-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="section-title" style="margin-bottom: 0;">Liste de présence</h2>
            <a href="{{ route('dashboard.events.export-presence', $event->id) }}" class="btn-brand" style="width: auto; display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.125rem; height: 1.125rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Télécharger (PDF)
            </a>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border); text-align: left;">
                        <th style="padding: 0.75rem; color: var(--muted-foreground); font-weight: 600;">Pseudo / Nom</th>
                        <th style="padding: 0.75rem; color: var(--muted-foreground); font-weight: 600;">Email</th>
                        <th style="padding: 0.75rem; color: var(--muted-foreground); font-weight: 600;">Téléphone</th>
                        <th style="padding: 0.75rem; color: var(--muted-foreground); font-weight: 600;">Secteur</th>
                        <th style="padding: 0.75rem; color: var(--muted-foreground); font-weight: 600;">Entreprise</th>
                        <th style="padding: 0.75rem; color: var(--muted-foreground); font-weight: 600;">Dernière activité</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($event->participants as $participant)
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 0.75rem; font-weight: 500;">{{ $participant->pseudo }}</td>
                            <td style="padding: 0.75rem;">{{ $participant->email ?? '-' }}</td>
                            <td style="padding: 0.75rem;">{{ $participant->phone ?? '-' }}</td>
                            <td style="padding: 0.75rem;">{{ $participant->sector ?? '-' }}</td>
                            <td style="padding: 0.75rem;">{{ $participant->company ?? '-' }}</td>
                            <td style="padding: 0.75rem; color: var(--muted-foreground);">{{ $participant->last_seen_at->format('H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 2rem; text-align: center; color: var(--muted-foreground);">Aucun participant n'est encore inscrit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
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

    function openEditEventModal(event) {
        const form = document.getElementById('edit-event-form');
        form.action = `/dashboard/events/${event.id}`;
        
        document.getElementById('edit_name').value = event.name;
        document.getElementById('edit_description').value = event.description || '';
        
        // Parsing robuste des dates
        const parseDate = (d) => d ? d.replace(' ', 'T').split('T')[0] : '';
        const parseDateTime = (d) => d ? d.replace(' ', 'T').substring(0, 16) : '';

        document.getElementById('edit_date').value = parseDate(event.date);
        document.getElementById('edit_end_date').value = parseDate(event.end_date);
        document.getElementById('edit_scheduled_at').value = parseDateTime(event.scheduled_at);
        document.getElementById('edit_status').value = event.status;
        
        document.getElementById('edit_moderation_enabled').checked = event.moderation_enabled;
        document.getElementById('edit_anonymous_allowed').checked = event.anonymous_allowed;
        document.getElementById('edit_is_on_marketplace').checked = event.is_on_marketplace;
        document.getElementById('edit_collect_presence').checked = event.collect_presence;

        // Mise à jour visuelle des toggles
        updateToggleVisuals();

        const bannerContainer = document.getElementById('edit-current-banner');
        if (event.image_path) {
            bannerContainer.innerHTML = `<img src="/storage/${event.image_path}" style="width: 100%; height: 8rem; object-fit: cover; border-radius: 0.75rem;">`;
        } else {
            bannerContainer.innerHTML = `<div style="background: var(--muted); height: 8rem; border-radius: 0.75rem; display: grid; place-items: center; color: var(--muted-foreground);">Aucune image</div>`;
        }

        document.getElementById('edit-event-modal').style.display = 'flex';
    }

    // Gestionnaire global pour ouvrir le modal d'édition sans erreur de syntaxe JSON
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-edit-event-modal-btn');
        if (btn) {
            const eventData = JSON.parse(btn.getAttribute('data-event'));
            openEditEventModal(eventData);
        }
    });

    function closeEditEventModal() {
        document.getElementById('edit-event-modal').style.display = 'none';
    }

    function updateToggleVisuals() {
        document.querySelectorAll('#edit-event-modal .toggle-input').forEach(input => {
            const track = input.nextElementSibling;
            const thumb = track.nextElementSibling;
            if (input.checked) {
                track.style.background = 'var(--brand)';
                thumb.style.transform = 'translateX(1.125rem)';
            } else {
                track.style.background = 'var(--muted)';
                thumb.style.transform = 'translateX(0)';
            }
        });
    }

    // Ajouter les écouteurs pour les toggles dans le modal de modification
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#edit-event-modal .toggle-input').forEach(input => {
            input.addEventListener('change', updateToggleVisuals);
        });
    });

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

<style>
/* Styles pour les toggles */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 2.5rem;
    height: 1.375rem;
}
.toggle-input {
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle-track {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: var(--muted);
    transition: .4s;
    border-radius: 34px;
}
.toggle-thumb {
    position: absolute;
    content: "";
    height: 1.125rem;
    width: 1.125rem;
    left: 0.125rem;
    bottom: 0.125rem;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
</style>

@endsection
