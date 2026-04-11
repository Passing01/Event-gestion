@extends('layouts.dashboard')

@section('title', 'Nouvel Administrateur')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Nouvel Administrateur</h1>
            <p class="dash-subtitle">Créer un compte d'accès administratif.</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">Retour</a>
        </div>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('admin.admins.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="name" style="font-weight: 500; font-size: 0.875rem;">Nom complet</label>
                <input type="text" name="name" id="name" required class="btn btn-outline" style="text-align: left; cursor: text; padding: 0.75rem;">
                @error('name') <span style="color: var(--destructive); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="email" style="font-weight: 500; font-size: 0.875rem;">Adresse Email</label>
                <input type="email" name="email" id="email" required class="btn btn-outline" style="text-align: left; cursor: text; padding: 1rem;">
                @error('email') <span style="color: var(--destructive); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <div style="background: rgba(59, 130, 246, 0.05); padding: 1rem; border-radius: 0.5rem; border: 1px dashed var(--border);">
                <p style="font-size: 0.8rem; color: var(--muted-foreground); margin: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; display: inline; vertical-align: text-bottom; margin-right: 0.25rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    Le mot de passe sera généré automatiquement et envoyé à l'adresse email spécifiée.
                </p>
            </div>

            <div style="margin-top: 1rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                    Créer l'administrateur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
