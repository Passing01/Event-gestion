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
            grid-template-columns: 1fr 30%;
            width: 100%;
            height: 100%;
            padding: 2rem;
            gap: 2rem;
            transition: all 0.5s ease-in-out;
        }
        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .question-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
        }
        .question-content {
            font-size: 3.5rem;
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
        .qa-sidebar {
            background: rgba(255,255,255,0.05);
            border-radius: 1.5rem;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            overflow-y: auto;
        }
        .qa-item {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
        }
        .qa-item .q { font-weight: 700; margin-bottom: 0.5rem; color: var(--brand); }
        .qa-item .a { font-size: 0.75rem; opacity: 0.8; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.5rem; margin-top: 0.5rem; }
        
        .fullscreen-btn {
            position: absolute;
            bottom: 2rem;
            right: 2rem;
            background: rgba(255,255,255,0.1);
            border: none;
            color: #fff;
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            z-index: 100;
        }
        .fullscreen-btn:hover { background: var(--brand); }
        
        .voice-indicator {
            display: flex;
            gap: 4px;
            height: 20px;
            align-items: center;
            margin-top: 1rem;
        }
        .voice-bar {
            width: 4px;
            background: var(--brand);
            border-radius: 2px;
            animation: voice-pulse 1s infinite ease-in-out;
        }
        @keyframes voice-pulse {
            0%, 100% { height: 4px; }
            50% { height: 20px; }
        }
    </style>
</head>
<body data-brand="{{ $event->user->brand_color ?? 'purple' }}">

    <div class="event-logo">
        <div class="event-logo-box">Q&A</div>
        <span>{{ $event->name }}</span>
    </div>

    <div class="projection-container" id="projection-wrap">
        <div class="main-content">
            <div class="question-box" id="projection-content">
                @if($answering)
                    <div class="question-content">"{{ $answering->content }}"</div>
                    <div class="question-author">{{ $answering->pseudo }}</div>
                @else
                    <div class="empty-state" style="font-size: 2rem; color: var(--muted-foreground); font-style: italic;">En attente de la prochaine question...</div>
                @endif
            </div>
            
            <div id="voice-indicator" class="voice-indicator" style="display: none;">
                <div class="voice-bar" style="animation-delay: 0s;"></div>
                <div class="voice-bar" style="animation-delay: 0.2s;"></div>
                <div class="voice-bar" style="animation-delay: 0.4s;"></div>
                <div class="voice-bar" style="animation-delay: 0.6s;"></div>
                <div class="voice-bar" style="animation-delay: 0.8s;"></div>
            </div>

            <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Plein écran">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                </svg>
            </button>
        </div>

        <div class="qa-sidebar">
            <h3 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.7; margin-bottom: 1rem;">Questions & Réponses</h3>
            <div id="qa-list" style="display: grid; gap: 1rem;">
                <!-- Rempli par JS -->
            </div>
        </div>
    </div>

    <script>
        let currentQuestionId = {{ $answering ? $answering->id : 'null' }};
        let currentStatus = '{{ $answering ? $answering->status : "" }}';
        
        async function fetchAnswering() {
            try {
                const response = await fetch('{{ route("projection.api", $event->code) }}');
                const data = await response.json();
                
                // Mise à jour de la question principale
                if (data.id !== currentQuestionId || data.status !== currentStatus) {
                    currentQuestionId = data.id;
                    currentStatus = data.status;
                    const container = document.getElementById('projection-content');
                    
                    if (data.id) {
                        let contentHtml = data.content ? `<div class="question-content" style="opacity: 0; transform: translateY(20px); transition: all 0.5s;">"${data.content}"</div>` : '';
                        let audioHtml = data.audio_path ? `
                            <div style="margin-top: 1rem; opacity: 0; transform: translateY(10px); transition: all 0.5s;" class="audio-container">
                                <div style="font-size: 1.5rem; color: var(--brand); margin-bottom: 1rem;">🎤 Message Vocal</div>
                                <audio controls style="height: 50px; width: 400px;">
                                    <source src="/storage/${data.audio_path}" type="audio/webm">
                                </audio>
                            </div>
                        ` : '';
                        
                        container.innerHTML = `
                            ${contentHtml}
                            ${audioHtml}
                            <div class="question-author" style="opacity: 0; transform: translateY(10px); transition: all 0.5s;">${data.pseudo}</div>
                        `;
                        setTimeout(() => {
                            if (container.querySelector('.question-content')) {
                                container.querySelector('.question-content').style.opacity = '1';
                                container.querySelector('.question-content').style.transform = 'translateY(0)';
                            }
                            if (container.querySelector('.audio-container')) {
                                container.querySelector('.audio-container').style.opacity = '1';
                                container.querySelector('.audio-container').style.transform = 'translateY(0)';
                            }
                            container.querySelector('.question-author').style.opacity = '1';
                            container.querySelector('.question-author').style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        container.innerHTML = `<div class="empty-state" style="font-size: 2rem; color: var(--muted-foreground); font-style: italic;">En attente de la prochaine question...</div>`;
                    }
                }

                // Mise à jour de la liste Q&A
                const qaList = document.getElementById('qa-list');
                if (data.all_questions) {
                    qaList.innerHTML = data.all_questions.map(q => `
                        <div class="qa-item">
                            <div class="q">
                                ${q.content ? q.content : ''}
                                ${q.audio_path ? '<div style="font-size: 0.75rem; color: var(--brand); margin-top: 0.25rem;">🎤 Message Vocal</div>' : ''}
                            </div>
                            ${q.replies.length > 0 ? `<div class="a">${q.replies[0].content}</div>` : ''}
                        </div>
                    `).join('');
                }

                // Indicateur vocal (si quelqu'un parle)
                const voiceIndicator = document.getElementById('voice-indicator');
                if (data.raised_hands && data.raised_hands.some(h => h.status === 'called')) {
                    voiceIndicator.style.display = 'flex';
                } else {
                    voiceIndicator.style.display = 'none';
                }

            } catch (error) {
                console.error('Erreur polling:', error);
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        setInterval(fetchAnswering, 3000);
        fetchAnswering();
    </script>

</body>
</html>
