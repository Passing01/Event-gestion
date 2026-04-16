@extends('layouts.dashboard')

@section('title', 'Mes Événements')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Mes Événements</h1>
                <p>Gérez vos sessions Q&A passées et à venir.</p>
            </div>
            <button onclick="openCreateModal()" class="btn-brand">+ Créer un événement</button>
        </div>
    </div>

    {{-- Modal de Succès --}}
    @if(session('success'))
    <div id="success-modal" style="position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:2000; display:flex; align-items:center; justify-content:center; backdrop-filter: blur(4px); animation: fadeIn 0.3s ease;">
        <div class="card" style="width:100%; max-width:24rem; text-align:center; padding:2.5rem; border-radius:1.5rem; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="font-size:4rem; margin-bottom:1rem; animation: tada 1s ease;">🎉</div>
            <h2 style="font-size:1.5rem; font-weight:900; color:var(--foreground); margin-bottom:0.5rem;">Félicitations !</h2>
            <p style="color:var(--muted-foreground); margin-bottom:1.5rem;">{{ session('success') }}</p>
            <button onclick="document.getElementById('success-modal').remove()" class="btn-brand" style="width:100%;">Génial !</button>
        </div>
    </div>
    @endif

    {{-- Modal de Création d'Événement --}}
    <div id="create-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:1.5rem; backdrop-filter: blur(8px); overflow-y: auto;">
        <div class="card" style="width:100%; max-width:40rem; padding: 2.5rem; border-radius: 1.5rem; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative; text-align: left;">
            <button onclick="closeCreateModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: var(--muted); border: none; width: 2.25rem; height: 2.25rem; border-radius: 50%; font-size: 1.25rem; cursor: pointer; color: var(--muted-foreground); transition: all 0.2s;">&times;</button>
            
            <div style="margin-bottom: 2rem;">
                <h2 style="font-size: 1.75rem; font-weight: 900; color: var(--foreground); margin-bottom: 0.5rem; letter-spacing: -0.025em;">Nouvel événement</h2>
                <p style="color: var(--muted-foreground); font-size: 0.875rem;">Configurez votre session interactive premium en quelques secondes.</p>
            </div>
            
            <form method="POST" action="{{ route('dashboard.events.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem; display: block;">Image de couverture / Bannière</label>
                    <div style="border: 2px dashed var(--border); border-radius: 1rem; padding: 2rem; text-align: center; background: var(--brand-light); cursor: pointer; position: relative;" onclick="document.getElementById('image').click()">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🖼️</div>
                        <p style="font-size: 0.75rem; color: var(--brand); font-weight: 600;">Cliquez pour choisir une image</p>
                        <p style="font-size: 0.625rem; color: var(--muted-foreground); margin-top: 0.25rem;">PNG, JPG ou GIF jusqu'à 2Mo</p>
                        <input type="file" id="image" name="image" style="display: none;" accept="image/*">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="name" style="font-weight: 600;">Nom de l'événement</label>
                    <input type="text" id="name" name="name" class="form-input" required placeholder="Ex: Masterclass IA & Web3" style="border-radius: 0.75rem; padding: 0.75rem 1rem;">
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="description" style="font-weight: 600;">Description (Optionnel)</label>
                    <textarea id="description" name="description" class="form-input" rows="3" placeholder="Partagez le contexte de l'événement pour l'IA..." style="border-radius: 0.75rem; padding: 0.75rem 1rem;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="date" style="font-weight: 600;">Date de l'événement</label>
                        <input type="date" id="date" name="date" class="form-input" required style="border-radius: 0.75rem;">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="scheduled_at" style="font-weight: 600;">Heure d'ouverture</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-input" style="border-radius: 0.75rem;">
                    </div>
                </div>

                <div style="background: var(--muted); padding: 1.25rem; border-radius: 1rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="font-weight: 700; font-size: 0.875rem;">Collecter la présence</p>
                        <p style="font-size: 0.75rem; color: var(--muted-foreground);">Enregistrer les coordonnées des participants.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="collect_presence" value="1" class="toggle-input" onchange="updateToggleVisualsInModal(this)">
                        <span class="toggle-track"></span>
                        <span class="toggle-thumb"></span>
                    </label>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="closeCreateModal()" class="btn-brand" style="background: var(--muted); color: var(--foreground); flex: 1; border: none; font-weight: 600;">Annuler</button>
                    <button type="submit" class="btn-brand" style="flex: 2; border: none; font-weight: 700; box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3);">Lancer la session</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de Modification d'Événement --}}
    <div id="edit-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:1.5rem; backdrop-filter: blur(8px); overflow-y: auto;">
        <div class="card" style="width:100%; max-width:40rem; padding: 2rem; border-radius: 1.5rem; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative;">
            <button onclick="closeEditModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: var(--muted); border: none; width: 2rem; height: 2rem; border-radius: 50%; font-size: 1.25rem; cursor: pointer; color: var(--muted-foreground);">&times;</button>
            
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
                    <button type="button" onclick="closeEditModal()" class="btn-brand" style="background: var(--muted); color: var(--foreground); flex: 1; border: none;">Annuler</button>
                    <button type="submit" class="btn-brand" style="flex: 2; border: none;">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Nom</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Code</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Date</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase;">Statut</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem;">
                            <a href="{{ route('dashboard.events.show', $event->id) }}" style="font-weight: 600; color: var(--foreground); text-decoration: none;">{{ $event->name }}</a>
                        </td>
                        <td style="padding: 1rem;">
                            <code style="background: var(--muted); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;">{{ $event->code }}</code>
                        </td>
                        <td style="padding: 1rem; font-size: 0.875rem;">{{ $event->date->format('d/m/Y') }}</td>
                        <td style="padding: 1rem;">
                            @if($event->is_forced_open)
                                <span class="badge" style="background: #ecfdf5; color: #059669; font-weight: 700;">Ouvert</span>
                            @elseif($event->scheduled_at && $event->scheduled_at->isFuture())
                                <span class="badge" style="background: var(--brand-light); color: var(--brand);">Programmé</span>
                            @else
                                <span class="badge" style="background: {{ $event->status == 'active' ? '#ecfdf5' : '#f3f4f6' }}; color: {{ $event->status == 'active' ? '#059669' : '#6b7280' }};">
                                    {{ ucfirst($event->status) }}
                                </span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                <form action="{{ route('dashboard.events.toggle-status', $event->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-brand" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; background: {{ $event->status == 'active' ? '#f3f4f6' : '#ecfdf5' }}; color: {{ $event->status == 'active' ? '#374151' : '#059669' }};">
                                        {{ $event->status == 'active' ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                <a href="{{ route('dashboard.moderator.index', $event->id) }}" class="btn-brand" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">Console</a>
                                <button class="open-edit-modal-btn" data-event='@json($event)' style="padding: 0.375rem; color: var(--muted-foreground); background: none; border: none; cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <form action="{{ route('dashboard.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Supprimer cet événement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding: 0.375rem; color: #dc2626; background: none; border: none; cursor: pointer;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 3rem; text-align: center; color: var(--muted-foreground);">
                            Vous n'avez pas encore d'événement.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    window.onload = function() {
        @if(session('open_create_modal'))
            openCreateModal();
        @endif
    };

    function openCreateModal() {
        document.getElementById('create-modal').style.display = 'flex';
    }
    function closeCreateModal() {
        document.getElementById('create-modal').style.display = 'none';
    }

    function updateToggleVisualsInModal(input) {
        const track = input.nextElementSibling;
        const thumb = track.nextElementSibling;
        if (input.checked) {
            track.style.background = 'var(--brand)';
            thumb.style.transform = 'translateX(1.125rem)';
        } else {
            track.style.background = 'var(--muted)';
            thumb.style.transform = 'translateX(0)';
        }
    }

    function openEditModal(event) {
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

        document.getElementById('edit-modal').style.display = 'flex';
    }

    // Gestionnaire global pour ouvrir le modal d'édition sans erreur de syntaxe JSON
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-edit-modal-btn');
        if (btn) {
            const eventData = JSON.parse(btn.getAttribute('data-event'));
            openEditModal(eventData);
        }
    });

    function closeEditModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }

    function updateToggleVisuals() {
        document.querySelectorAll('#edit-modal .toggle-input').forEach(input => {
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
    document.querySelectorAll('#edit-modal .toggle-input').forEach(input => {
        input.addEventListener('change', updateToggleVisuals);
    });
</script>
@endpush

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
@keyframes tada {
    0% { transform: scale(1); }
    10%, 20% { transform: scale(0.9) rotate(-3deg); }
    30%, 50%, 70%, 90% { transform: scale(1.1) rotate(3deg); }
    40%, 60%, 80% { transform: scale(1.1) rotate(-3deg); }
    100% { transform: scale(1) rotate(0); }
}

/* Styles pour les toggles (au cas où non présents dans dashboard.css) */
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
