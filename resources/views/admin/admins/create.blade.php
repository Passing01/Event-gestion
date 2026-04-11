@extends('layouts.dashboard')

@section('title', 'Créer un Administrateur')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Nouvel Administrateur</h1>
        </div>
    </div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.admins.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nom complet</label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}">
        </div>
        <div class="form-group">
            <label for="email">Adresse Email</label>
            <input type="email" name="email" id="email" required value="{{ old('email') }}">
        </div>
        
        <p style="color: #94a3b8; font-size: 0.875rem; margin-bottom: 2rem;">
            * Le mot de passe sera généré automatiquement et envoyé par email à l'utilisateur.
        </p>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Créer l'administrateur</button>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
</div>
@endsection
