@extends('layouts.dashboard')

@section('title', 'Messages – Smart Home')
@section('meta_description', 'Vos derniers messages et alertes système.')

@section('content')

<section class="card card-lg">
    <div class="page-header">
        <h1>Messages</h1>
        <p>Vos dernières conversations et alertes système.</p>
    </div>

    @php
    $threads = [
        ['id'=>1, 'name'=>'Lave-linge',       'preview'=>'Cycle terminé',                              'time'=>'2min',    'badge'=>'Appareil'],
        ['id'=>2, 'name'=>'Porte d\'entrée',   'preview'=>'Porte verrouillée',                          'time'=>'12min',   'badge'=>'Sécurité'],
        ['id'=>3, 'name'=>'HVAC',              'preview'=>'Rappel filtre dans 7 jours',                 'time'=>'1h',      'badge'=>'Système'],
        ['id'=>4, 'name'=>'Lumières cuisine',  'preview'=>'Allumé via scène',                           'time'=>'3h',      'badge'=>'Éclairage'],
        ['id'=>5, 'name'=>'Caméra jardin',     'preview'=>'Mouvement détecté à 07:15',                  'time'=>'Hier',    'badge'=>'Sécurité'],
        ['id'=>6, 'name'=>'Thermostat',        'preview'=>'Température réglée à 21°C',                  'time'=>'Hier',    'badge'=>'Système'],
    ];
    @endphp

    <ul class="thread-list">
        @foreach($threads as $t)
        <li class="thread-item">
            <div class="thread-avatar">{{ $t['name'][0] }}</div>
            <div class="thread-meta">
                <div class="thread-name-row">
                    <p class="thread-name">{{ $t['name'] }}</p>
                    <span class="thread-time">{{ $t['time'] }}</span>
                </div>
                <p class="thread-preview">{{ $t['preview'] }}</p>
            </div>
            <span class="badge">{{ $t['badge'] }}</span>
        </li>
        @endforeach
    </ul>
</section>

@endsection
