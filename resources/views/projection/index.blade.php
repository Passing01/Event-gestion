<!DOCTYPE html>
<html lang="fr" data-brand="purple" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projection : {{ $event->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    <style>
        body {
            background: #000;
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }
        .projection-container {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
            height: 100%;
            padding: 4rem;
            transition: all 0.5s ease-in-out;
        }
        .projection-container.with-sidebar {
            grid-template-columns: 1fr 22rem;
            gap: 4rem;
        }
        .question-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .question-content {
            font-size: 4rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 2rem;
            text-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        .question-author {
            font-size: 1.5rem;
            color: var(--brand);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .answered-badge {
            background: #6b7280;
            color: #fff;
            padding: 0.5rem 1.5rem;
            border-radius: 9999px;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-transform: uppercase;
        }
        .event-logo {
            position: absolute;
            top: 3rem;
            left: 3rem;
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 10;
        }
        .event-logo-box {
            width: 3.5rem;
            height: 3.5rem;
            background: var(--brand);
            border-radius: 1rem;
            display: grid;
            place-items: center;
            color: #fff;
        }
        .empty-state {
            font-size: 2rem;
            color: var(--muted-foreground);
            font-style: italic;
        }
        .hands-sidebar {
            background: rgba(255,255,255,0.05);
            border-radius: 1.5rem;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            height: fit-content;
            align-self: center;
        }
        .hand-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 1rem;
            transition: all 0.3s;
        }
        .hand-item.called {
            background: var(--brand);
            transform: scale(1.05);
            box-shadow: 0 0 20px var(--brand);
        }
        .hand-rank {
            width: 2rem;
            height: 2rem;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 0.875rem;
        }
    </style>
</head>
<body data-brand="{{ $event->user->brand_color ?? 'purple' }}">

    <div class="event-logo">
        <div class="event-logo-box">Q&A</div>
        <span>{{ $event->name }}</span>
    </div>

    <div class="projection-container" id="projection-wrap">
        <div class="question-box" id="projection-content">
            @if($answering)
                <div class="question-content">"{{ $answering->content }}"</div>
                <div class="question-author">{{ $answering->pseudo }}</div>
            @else
                <div class="empty-state">En attente de la prochaine question...</div>
            @endif
        </div>

        <div class="hands-sidebar" id="hands-sidebar" style="display: none;">
            <h3 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.7; margin-bottom: 1rem;">✋ Mains Levées</h3>
            <div id="hands-list" style="display: grid; gap: 0.75rem;">
                <!-- Rempli par JS -->
            </div>
        </div>
    </div>

    <script>
        // Polling pour mettre à jour la question et les mains levées en temps réel
        let currentQuestionId = {{ $answering ? $answering->id : 'null' }};
        let currentStatus = '{{ $answering ? $answering->status : "" }}';
        
        async function fetchAnswering() {
            try {
                const response = await fetch('{{ route("projection.api", $event->code) }}');
                const data = await response.json();
                
                // Mise à jour de la question
                if (data.id !== currentQuestionId || data.status !== currentStatus) {
                    currentQuestionId = data.id;
                    currentStatus = data.status;
                    const container = document.getElementById('projection-content');
                    
                    if (data.id) {
                        container.innerHTML = `
                            ${data.status === 'answered' ? '<div class="answered-badge">Répondu</div>' : ''}
                            <div class="question-content" style="opacity: 0; transform: translateY(20px); transition: all 0.5s;">"${data.content}"</div>
                            <div class="question-author" style="opacity: 0; transform: translateY(10px); transition: all 0.5s;">${data.pseudo}</div>
                        `;
                        setTimeout(() => {
                            container.querySelector('.question-content').style.opacity = '1';
                            container.querySelector('.question-content').style.transform = 'translateY(0)';
                            container.querySelector('.question-author').style.opacity = '1';
                            container.querySelector('.question-author').style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        container.innerHTML = `<div class="empty-state">En attente de la prochaine question...</div>`;
                    }
                }

                // Mise à jour des mains levées
                const handsList = document.getElementById('hands-list');
                const sidebar = document.getElementById('hands-sidebar');
                const wrap = document.getElementById('projection-wrap');

                if (data.raised_hands && data.raised_hands.length > 0) {
                    let html = '';
                    data.raised_hands.forEach((hand, index) => {
                        html += `
                            <div class="hand-item ${hand.status === 'called' ? 'called' : ''}">
                                <div class="hand-rank">#${index + 1}</div>
                                <div style="flex: 1; font-weight: 600;">${hand.pseudo}</div>
                                ${hand.status === 'called' ? '<span>🎤</span>' : ''}
                            </div>
                        `;
                    });
                    handsList.innerHTML = html;
                    sidebar.style.display = 'flex';
                    wrap.classList.add('with-sidebar');
                } else {
                    handsList.innerHTML = '';
                    sidebar.style.display = 'none';
                    wrap.classList.remove('with-sidebar');
                }

            } catch (error) {
                console.error('Erreur polling:', error);
            }
        }

        setInterval(fetchAnswering, 3000); // Toutes les 3 secondes
        fetchAnswering(); // Premier appel immédiat
    </script>

</body>
</html>
