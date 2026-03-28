@extends('layouts.dashboard')

@section('title', 'Appareils – Smart Home')
@section('meta_description', 'Tous vos appareils connectés.')

@section('content')

<section class="card card-lg">
    <div class="page-header">
        <h1>Appareils</h1>
        <p>Tous vos appareils connectés.</p>
    </div>

    @php
    $devices = [
        ['id'=>1, 'name'=>'Lumière salon',    'room'=>'Salon',       'on'=>true,  'icon'=>'lightbulb'],
        ['id'=>2, 'name'=>'Réfrigérateur',    'room'=>'Cuisine',     'on'=>true,  'icon'=>'fridge'],
        ['id'=>3, 'name'=>'PC Bureau',        'room'=>'Studio',      'on'=>false, 'icon'=>'laptop'],
        ['id'=>4, 'name'=>'Climatisation',    'room'=>'Chambre',     'on'=>true,  'icon'=>'therm'],
        ['id'=>5, 'name'=>'Lave-linge',       'room'=>'Buanderie',   'on'=>false, 'icon'=>'washer'],
        ['id'=>6, 'name'=>'Lumière cuisine',  'room'=>'Cuisine',     'on'=>true,  'icon'=>'lightbulb'],
    ];

    $iconSvgs = [
        'lightbulb' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m1.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
        'fridge'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm0 8h14M9 7v2"/>',
        'laptop'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        'therm'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
        'washer'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 4a2 2 0 012-2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm8 12a4 4 0 100-8 4 4 0 000 8z"/>',
    ];
    @endphp

    <div class="devices-grid">
        @foreach($devices as $d)
        <div class="device-item">
            <div class="device-item-inner">
                <div class="device-item-meta">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor"
                         style="width:1.25rem;height:1.25rem;color:{{ $d['on'] ? 'var(--brand)' : 'var(--muted-foreground)' }};">
                        {!! $iconSvgs[$d['icon']] !!}
                    </svg>
                    <div>
                        <p class="device-item-name">{{ $d['name'] }}</p>
                        <p class="device-item-room">{{ $d['room'] }}</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" class="toggle-input" {{ $d['on'] ? 'checked' : '' }}
                           onchange="this.nextElementSibling.style.background = this.checked ? 'var(--brand)' : 'var(--muted)'; this.parentElement.querySelector('.toggle-thumb').style.transform = this.checked ? 'translateX(1.125rem)' : 'translateX(0)'">
                    <span class="toggle-track" style="background:{{ $d['on'] ? 'var(--brand)' : 'var(--muted)' }};"></span>
                    <span class="toggle-thumb" style="transform:{{ $d['on'] ? 'translateX(1.125rem)' : 'translateX(0)' }};"></span>
                </label>
            </div>
        </div>
        @endforeach
    </div>
</section>

@endsection
