@extends('layouts.dashboard')

@section('title', 'Statistiques – Smart Home')
@section('meta_description', 'Statistiques mensuelles de consommation par service.')

@section('content')

<section class="card card-lg">
    <div class="page-header">
        <h1>Statistiques</h1>
        <p>Consommation mensuelle par service (eau, gaz, électricité).</p>
    </div>

    @php
    $data = [
        ['month'=>'Fév', 'water'=>60, 'gas'=>40, 'electricity'=>30],
        ['month'=>'Mar', 'water'=>75, 'gas'=>55, 'electricity'=>35],
        ['month'=>'Avr', 'water'=>68, 'gas'=>60, 'electricity'=>32],
        ['month'=>'Mai', 'water'=>80, 'gas'=>65, 'electricity'=>40],
        ['month'=>'Jun', 'water'=>70, 'gas'=>90, 'electricity'=>45],
        ['month'=>'Jul', 'water'=>72, 'gas'=>95, 'electricity'=>50],
        ['month'=>'Aoû', 'water'=>65, 'gas'=>70, 'electricity'=>42],
        ['month'=>'Sep', 'water'=>66, 'gas'=>65, 'electricity'=>40],
    ];
    $maxVal = 100;
    $chartH = 14; // rem
    @endphp

    <div class="stats-chart-wrap">
        <div class="consumption-chart-bars" style="height:{{ $chartH + 2 }}rem;">
            @foreach($data as $d)
            <div class="consumption-month">
                <div class="consumption-bars" style="height:{{ $chartH }}rem;">
                    <div class="consumption-bar"
                         style="height:{{ round(($d['water']/$maxVal)*$chartH,2) }}rem;background:#6D4DFF;width:0.625rem;"></div>
                    <div class="consumption-bar"
                         style="height:{{ round(($d['gas']/$maxVal)*$chartH,2) }}rem;background:#FF7A00;width:0.625rem;"></div>
                    <div class="consumption-bar"
                         style="height:{{ round(($d['electricity']/$maxVal)*$chartH,2) }}rem;background:#FF3D71;width:0.625rem;"></div>
                </div>
                <span class="consumption-month-label">{{ $d['month'] }}</span>
            </div>
            @endforeach
        </div>

        <div class="chart-legend" style="margin-top:1rem;">
            <div class="chart-legend-item">
                <span class="legend-dot" style="background:#6D4DFF;"></span>Eau
            </div>
            <div class="chart-legend-item">
                <span class="legend-dot" style="background:#FF7A00;"></span>Gaz
            </div>
            <div class="chart-legend-item">
                <span class="legend-dot" style="background:#FF3D71;"></span>Électricité
            </div>
        </div>
    </div>

    {{-- Résumé statistiques --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1.5rem;">
        <div class="card card-sm" style="text-align:center;">
            <div style="font-size:1.5rem;font-weight:700;color:#6D4DFF;">72 m³</div>
            <div style="font-size:0.75rem;color:var(--muted-foreground);margin-top:0.25rem;">Eau ce mois</div>
        </div>
        <div class="card card-sm" style="text-align:center;">
            <div style="font-size:1.5rem;font-weight:700;color:#FF7A00;">67 m³</div>
            <div style="font-size:0.75rem;color:var(--muted-foreground);margin-top:0.25rem;">Gaz ce mois</div>
        </div>
        <div class="card card-sm" style="text-align:center;">
            <div style="font-size:1.5rem;font-weight:700;color:#FF3D71;">41 kWh</div>
            <div style="font-size:0.75rem;color:var(--muted-foreground);margin-top:0.25rem;">Électricité ce mois</div>
        </div>
    </div>
</section>

@endsection
