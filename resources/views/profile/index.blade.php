@extends('layouts.dashboard')

@section('title', 'Mon Profil')

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <h1>Mon Profil</h1>
        <p>Gérez vos informations personnelles et vos préférences de marque.</p>
    </div>

    @if(session('success'))
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        
        <div class="space-y-5">
            {{-- Informations Générales --}}
            <section class="card">
                <h2 class="section-title">Informations Générales</h2>
                <form method="POST" action="{{ route('dashboard.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email (non modifiable)</label>
                            <input type="email" class="form-input" value="{{ $user->email }}" disabled style="background: var(--muted);">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Organisation</label>
                            <input type="text" name="organization_name" class="form-input" value="{{ old('organization_name', $user->organization_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Secteur d'activité</label>
                            <select name="industry" class="form-input" required style="appearance: auto;">
                                @foreach(['Tech', 'Éducation', 'Santé', 'Finance', 'Autre'] as $ind)
                                <option value="{{ $ind }}" {{ $user->industry == $ind ? 'selected' : '' }}>{{ $ind }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="height: 1px; background: var(--border); margin: 1.5rem 0;"></div>

                    <h2 class="section-title">Identité Visuelle</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Couleur de marque</label>
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                @foreach(['#7c3aed', '#2563eb', '#0d9488', '#ea580c', '#db2777'] as $color)
                                <label style="cursor: pointer;">
                                    <input type="radio" name="brand_color" value="{{ $color }}" class="hidden" {{ $user->brand_color == $color ? 'checked' : '' }} style="display: none;">
                                    <div class="color-swatch-ui" style="background: {{ $color }}; width: 2rem; height: 2rem; border-radius: 9999px; border: 3px solid {{ $user->brand_color == $color ? 'var(--foreground)' : 'transparent' }}; transition: border-color 0.2s;"></div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Style de projection</label>
                            <select name="projection_layout" class="form-input" required style="appearance: auto;">
                                <option value="modern" {{ $user->projection_layout == 'modern' ? 'selected' : '' }}>Moderne</option>
                                <option value="classic" {{ $user->projection_layout == 'classic' ? 'selected' : '' }}>Classique</option>
                                <option value="minimal" {{ $user->projection_layout == 'minimal' ? 'selected' : '' }}>Minimaliste</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" class="btn-brand" style="width: auto; padding: 0.5rem 2rem;">Enregistrer les modifications</button>
                    </div>
                </form>
            </section>

            {{-- Sécurité --}}
            <section class="card">
                <h2 class="section-title">Sécurité</h2>
                <form method="POST" action="{{ route('dashboard.profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-input" required>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <button type="submit" class="btn-brand" style="background: var(--muted); color: var(--foreground); width: auto; padding: 0.5rem 2rem;">Mettre à jour le mot de passe</button>
                    </div>
                </form>
            </section>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-5">
            <section class="card" style="text-align: center;">
                <div class="avatar" style="width: 4rem; height: 4rem; font-size: 1.5rem; margin: 0 auto 1rem;">{{ substr($user->name, 0, 2) }}</div>
                <h3 style="font-weight: 700;">{{ $user->name }}</h3>
                <p style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $user->email }}</p>
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 0.5rem;">Plan actuel</p>
                    <span class="badge" style="background: var(--brand-light); color: var(--brand); font-size: 0.875rem; padding: 0.5rem 1rem;">{{ ucfirst($user->plan) }}</span>
                </div>
            </section>
        </div>

    </div>
</div>

<script>
    document.querySelectorAll('input[name="brand_color"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.color-swatch-ui').forEach(sw => sw.style.borderColor = 'transparent');
            if (this.checked) {
                this.nextElementSibling.style.borderColor = 'var(--foreground)';
            }
        });
    });
</script>

@endsection
