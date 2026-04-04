<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste de Présence - {{ $event->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .banner {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .event-name {
            font-size: 28px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 10px 0;
        }
        .event-info {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .moderator-box, .panelists-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .panelist-item {
            margin-bottom: 10px;
        }
        .panelist-name {
            font-weight: bold;
            display: block;
        }
        .panelist-detail {
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 600;
            text-align: left;
            padding: 10px;
            font-size: 12px;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 10px;
            font-size: 12px;
            border-bottom: 1px solid #f3f4f6;
        }
        .day-group {
            margin-top: 40px;
        }
        .day-title {
            background: #eff6ff;
            color: #1e40af;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($event->image_path)
            {{-- Use absolute path for dompdf --}}
            <img src="{{ public_path('storage/' . $event->image_path) }}" class="banner">
        @endif
        <h1 class="event-name">{{ $event->name }}</h1>
        <div class="event-info">
            {{ $event->date->format('d/m/Y') }} 
            @if($event->end_date)
                au {{ $event->end_date->format('d/m/Y') }}
            @endif
            | Code : {{ $event->code }}
        </div>
    </div>

    <div>
        <div class="section-title">Organisation</div>
        <div class="moderator-box">
            <span class="panelist-name">Modérateur : {{ $event->user->name }}</span>
            <span class="panelist-detail">{{ $event->user->email }}</span>
        </div>

        @if($event->panelists->count() > 0)
            <div class="section-title">Panélistes</div>
            <div class="panelists-box">
                @foreach($event->panelists as $panelist)
                    <div class="panelist-item">
                        <span class="panelist-name">{{ $panelist->user->name }}</span>
                        <span class="panelist-detail">{{ $panelist->sector }} | {{ $panelist->user->email }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="section-title">Liste des Participants</div>
    
    @php
        $groupedParticipants = $event->participants->sortBy('joined_date')->groupBy(function($p) {
            return $p->joined_date ? $p->joined_date->format('d/m/Y') : $p->created_at->format('d/m/Y');
        });
    @endphp

    @foreach($groupedParticipants as $date => $participants)
        <div class="day-group">
            <div class="day-title">Journée du {{ $date }} ({{ $participants->count() }} participants)</div>
            <table>
                <thead>
                    <tr>
                        <th>Pseudo / Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Secteur</th>
                        <th>Entreprise</th>
                        <th>Heure</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $participant)
                        <tr>
                            <td style="font-weight: 500;">{{ $participant->pseudo }}</td>
                            <td>{{ $participant->email ?? '-' }}</td>
                            <td>{{ $participant->phone ?? '-' }}</td>
                            <td>{{ $participant->sector ?? '-' }}</td>
                            <td>{{ $participant->company ?? '-' }}</td>
                            <td>{{ $participant->last_seen_at->format('H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer">
        Généré le {{ now()->format('d/m/Y à H:i') }} | Plateforme Event Q&A
    </div>
</body>
</html>
