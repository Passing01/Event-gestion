@extends('layouts.dashboard')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Modifier l'utilisateur</h1>
            <p class="dash-subtitle">Mettre à jour les informations de {{ $user->name }}.</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Retour</a>
        </div>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf
            @method('PUT')
            
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="name" style="font-weight: 500; font-size: 0.875rem;">Nom complet</label>
                <input type="text" name="name" id="name" value="{{ $user->name }}" required class="btn btn-outline" style="text-align: left; cursor: text; padding: 0.75rem;">
                @error('name') <span style="color: var(--destructive); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="email" style="font-weight: 500; font-size: 0.875rem;">Adresse Email</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}" required class="btn btn-outline" style="text-align: left; cursor: text; padding: 0.75rem;">
                @error('email') <span style="color: var(--destructive); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-top: 1rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
