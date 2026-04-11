@extends('layouts.dashboard')

@section('title', 'Détails Marketplace')

@section('content')
<div class="dash-content">
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Détails Marketplace</h1>
            <p class="dash-subtitle">{{ $event->name }}</p>
        </div>
        <div class="dash-actions">
            <a href="{{ route('admin.marketplace.index') }}" class="btn btn-outline">Retour</a>
        </div>
    </div>

    <div class="card">
        <h3>Informations</h3>
        <p><strong>Organisateur :</strong> {{ $event->user?->name }}</p>
        <p><strong>Prix :</strong> {{ $event->marketplace_price ?? 0 }} XOF</p>
        <p><strong>Publié le :</strong> {{ $event->updated_at->format('d/m/Y') }}</p>
    </div>
</div>
@endsection
