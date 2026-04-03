@extends('layouts.dashboard')

@section('title', 'Créer un événement')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <h1>Créer un nouvel événement</h1>
        <p>Configurez votre session Q&A.</p>
    </div>

    <div class="card" style="max-width: 40rem;">
        <form method="POST" action="{{ route('dashboard.events.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" for="image">Bannière de l'événement (Image)</label>
                <input type="file" id="image" name="image" class="form-input" accept="image/*">
                <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.25rem;">Recommandé : 1200x400px (JPG, PNG).</p>
                @error('image')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Nom de l'événement</label>
                <input type="text" id="name" name="name" class="form-input"
                       value="{{ old('name') }}"
                       placeholder="Ex: Conférence Annuelle 2026"
                       required>
                @error('name')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description (pour l'IA)</label>
                <textarea id="description" name="description" class="form-input" rows="4"
                          placeholder="Décrivez le thème et les objectifs de l'événement pour aider l'IA à mieux répondre aux questions.">{{ old('description') }}</textarea>
                @error('description')
                    <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="date">Date de l'événement</label>
                    <input type="date" id="date" name="date" class="form-input"
                           value="{{ old('date') }}"
                           required>
                    @error('date')
                        <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="scheduled_at">Heure de démarrage</label>
                    <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-input"
                           value="{{ old('scheduled_at') }}">
                    @error('scheduled_at')
                        <span style="font-size:0.75rem;color:var(--destructive);">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="height: 1px; background: var(--border); margin: 1.5rem 0;"></div>

            <div class="form-group">
                <label class="form-label">Paramètres par défaut</label>
                <div style="display: grid; gap: 1rem; margin-top: 0.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 0.875rem; font-weight: 600;">Modération manuelle</h3>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground);">Approuver les questions avant projection.</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="moderation_enabled" value="1" class="toggle-input" {{ Auth::user()->default_moderation ? 'checked' : '' }}
                                   onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                            <span class="toggle-track" style="background: {{ Auth::user()->default_moderation ? 'var(--brand)' : 'var(--muted)' }};"></span>
                            <span class="toggle-thumb" style="transform: {{ Auth::user()->default_moderation ? 'translateX(1.125rem)' : 'translateX(0)' }};"></span>
                        </label>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 0.875rem; font-weight: 600;">Questions anonymes</h3>
                            <p style="font-size: 0.75rem; color: var(--muted-foreground);">Autoriser les participants sans pseudo.</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="anonymous_allowed" value="1" class="toggle-input" checked
                                   onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                            <span class="toggle-track" style="background: var(--brand);"></span>
                            <span class="toggle-thumb" style="transform: translateX(1.125rem);"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <a href="{{ route('dashboard.events.index') }}" class="btn-brand" style="background: var(--muted); color: var(--foreground); flex: 1; text-align: center; line-height: 2.5rem;">Annuler</a>
                <button type="submit" class="btn-brand" style="flex: 2;">Créer l'événement</button>
            </div>
        </form>
    </div>
</div>

@endsection
