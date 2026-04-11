@extends('layouts.dashboard')

@section('title', 'Modifier Administrateur')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Modifier Administrateur</h1>
            <p class="dash-subtitle">{{ $admin->name }}</p>
        </div>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $admin->name) }}">
            </div>
            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input type="email" name="email" id="email" required value="{{ old('email', $admin->email) }}">
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
