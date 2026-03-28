<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Q&A : {{ $event->name }}</title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #1f2937; line-height: 1.5; padding: 2rem; max-width: 800px; margin: 0 auto; }
        h1 { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; color: #7c3aed; }
        .meta { font-size: 0.875rem; color: #6b7280; margin-bottom: 2rem; border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; }
        .section { margin-bottom: 2rem; }
        .section-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: #111827; }
        .question-card { background: #f9fafb; padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 1rem; border: 1px solid #e5e7eb; page-break-inside: avoid; }
        .question-meta { font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem; }
        .question-content { font-weight: 600; margin-bottom: 1rem; }
        .reply { margin-left: 1.5rem; padding-left: 1rem; border-left: 2px solid #e5e7eb; margin-top: 1rem; font-size: 0.875rem; }
        .reply-meta { font-weight: 700; font-size: 0.75rem; margin-bottom: 0.25rem; }
        .badge { background: #7c3aed; color: #fff; padding: 0.125rem 0.375rem; border-radius: 9999px; font-size: 0.625rem; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; margin-bottom: 2rem;">
        <button onclick="window.print()" style="background: #7c3aed; color: #fff; border: none; padding: 0.5rem 1.5rem; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">Imprimer / Sauvegarder en PDF</button>
    </div>

    <header>
        <h1>Rapport Q&A : {{ $event->name }}</h1>
        <div class="meta">
            Date de l'événement : {{ $event->date->format('d/m/Y') }} | 
            Généré le : {{ now()->format('d/m/Y H:i') }} | 
            Total Questions : {{ $event->questions->count() }}
        </div>
    </header>

    <div class="section">
        <h2 class="section-title">Questions & Réponses</h2>
        @foreach($event->questions as $q)
        <div class="question-card">
            <div class="question-meta">Par {{ $q->pseudo }} • {{ $q->votes_count }} votes</div>
            <div class="question-content">{{ $q->content }}</div>
            
            @foreach($q->replies as $reply)
            <div class="reply">
                <div class="reply-meta">
                    {{ $reply->pseudo }} 
                    @if($reply->is_moderator) <span class="badge">MODÉRATEUR</span> @endif
                </div>
                <div>{{ $reply->content }}</div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <footer style="margin-top: 4rem; text-align: center; font-size: 0.75rem; color: #9ca3af;">
        Rapport généré par Event Q&A - Votre plateforme d'interaction en temps réel.
    </footer>

</body>
</html>
