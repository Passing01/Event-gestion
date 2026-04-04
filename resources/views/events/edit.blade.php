@extends('layouts.dashboard')

@section('title', 'Modifier l\'événement')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <h1>Modifier l'événement</h1>
        <p>Code : <strong>{{ $event->code }}</strong></p>
    </div>

    <div class="card" style="max-width: 40rem;">
        <form method="POST" action="{{ route('dashboard.events.update', $event->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Bannière actuelle</label>
                @if($event->image_path)
                    <img src="{{ asset('storage/' . $event->image_path) }}" style="width: 100%; height: 8rem; object-fit: cover; border-radius: 0.75rem; margin-bottom: 1rem;">
                @else
                    <div style="background: var(--muted); height: 8rem; border-radius: 0.75rem; display: grid; place-items: center; margin-bottom: 1rem; color: var(--muted-foreground);">Aucune image</div>
                @endif
                <label class="form-label" for="image">Changer de bannière</label>
                <input type="file" id="image" name="image" class="form-input" accept="image/*">
                @error('image')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Nom de l'événement</label>
                <input type="text" id="name" name="name" class="form-input"
                       value="{{ old('name', $event->name) }}"
                       required>
                @error('name')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (pour l'IA)</label>
                <textarea id="description" name="description" class="form-input" rows="4"
                          placeholder="Décrivez le thème et les objectifs de l'événement pour aider l'IA à mieux répondre aux questions.">{{ old('description', $event->description) }}</textarea>
                @error('description')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="date">Date de début</label>
                    <input type="date" id="date" name="date" class="form-input"
                           value="{{ old('date', $event->date->format('Y-m-d')) }}"
                           required>
                    @error('date')
                        <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="end_date">Date de fin <span style="font-size: 0.7rem; color: var(--muted-foreground);">(optionnel)</span></label>
                    <input type="date" id="end_date" name="end_date" class="form-input"
                           value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d') : '') }}">
                    @error('end_date')
                        <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="scheduled_at">Heure de démarrage</label>
                    <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-input"
                           value="{{ old('scheduled_at', $event->scheduled_at ? $event->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                    @error('scheduled_at')
                        <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Statut</label>
                <select id="status" name="status" class="form-input" required style="appearance: auto;">
                    <option value="active" {{ $event->status == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="archived" {{ $event->status == 'archived' ? 'selected' : '' }}>Archivé</option>
                </select>
                @error('status')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div style="height: 1px; background: var(--border); margin: 1.5rem 0;"></div>

            <div class="form-group">
                <label class="form-label">Paramètres</label>
                <div style="display: grid; gap: 1rem; margin-top: 0.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 0.875rem; font-weight: 600;">Modération manuelle</h3>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground);">Approuver les questions avant projection.</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="moderation_enabled" value="1" class="toggle-input" {{ $event->moderation_enabled ? 'checked' : '' }}
                                   onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                            <span class="toggle-track" style="background: {{ $event->moderation_enabled ? 'var(--brand)' : 'var(--muted)' }};"></span>
                            <span class="toggle-thumb" style="transform: {{ $event->moderation_enabled ? 'translateX(1.125rem)' : 'translateX(0)' }};"></span>
                        </label>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 0.875rem; font-weight: 600;">Questions anonymes</h3>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground);">Autoriser les participants sans pseudo.</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="anonymous_allowed" value="1" class="toggle-input" {{ $event->anonymous_allowed ? 'checked' : '' }}
                                   onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                            <span class="toggle-track" style="background: {{ $event->anonymous_allowed ? 'var(--brand)' : 'var(--muted)' }};"></span>
                            <span class="toggle-thumb" style="transform: {{ $event->anonymous_allowed ? 'translateX(1.125rem)' : 'translateX(0)' }};"></span>
                        </label>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 0.875rem; font-weight: 600;">Publier sur Marketplace</h3>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground);">Rendre le replay accessible publiquement.</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_on_marketplace" value="1" class="toggle-input" {{ $event->is_on_marketplace ? 'checked' : '' }}
                                   onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                            <span class="toggle-track" style="background: {{ $event->is_on_marketplace ? 'var(--brand)' : 'var(--muted)' }};"></span>
                            <span class="toggle-thumb" style="transform: {{ $event->is_on_marketplace ? 'translateX(1.125rem)' : 'translateX(0)' }};"></span>
                        </label>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 0.875rem; font-weight: 600;">Récupérer la liste de présence</h3>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground);">Demander l'email, téléphone, secteur et entreprise.</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="collect_presence" value="1" class="toggle-input" {{ $event->collect_presence ? 'checked' : '' }}
                                   onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                            <span class="toggle-track" style="background: {{ $event->collect_presence ? 'var(--brand)' : 'var(--muted)' }};"></span>
                            <span class="toggle-thumb" style="transform: {{ $event->collect_presence ? 'translateX(1.125rem)' : 'translateX(0)' }};"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <a href="{{ route('dashboard.events.index') }}" class="btn-brand" style="background: var(--muted); color: var(--foreground); flex: 1; text-align: center; line-height: 2.5rem;">Annuler</a>
                <button type="submit" class="btn-brand" style="flex: 2;">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

@endsection
