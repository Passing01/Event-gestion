@extends('layouts.dashboard')

@section('title', 'Console Panéliste - ' . $event->name)

@section('content')

<div class="space-y-5">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin-top: 0.5rem;">Console Panéliste : {{ $event->name }}</h1>
                <p>Bienvenue, <strong>{{ Auth::user()->name }}</strong> ({{ $panelist->sector }})</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button class="btn-brand" onclick="document.getElementById('upload-modal').style.display='flex'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1.25rem;height:1.25rem;margin-right:0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    Uploader PowerPoint/Notes
                </button>
            </div>
        </div>
    </div>

    @if($panelist->presentation_path)
        <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 1rem; border-radius: 0.75rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1.5rem;height:1.5rem;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p style="font-weight: 600;">Document chargé</p>
                <p style="font-size: 0.875rem;">L'IA analyse votre document pour vous assister dans les réponses.</p>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Liste des Questions -->
        <div class="card">
            <h2 class="section-title">Questions du Public</h2>
            <div id="questions-list" class="space-y-4" style="margin-top: 1rem;">
                @forelse($questions as $question)
                    <div class="question-card" style="background: var(--muted); padding: 1.25rem; border-radius: 1rem; border: 1px solid var(--border);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 2rem; height: 2rem; background: var(--brand); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                    {{ substr($question->user->pseudo ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <p style="font-weight: 600; font-size: 0.875rem;">{{ $question->user->pseudo ?? 'Anonyme' }}</p>
                                    <p style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $question->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn-brand" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;" onclick="suggestAI('{{ $question->id }}')">
                                    💡 Suggestion IA
                                </button>
                            </div>
                        </div>
                        <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">{{ $question->content }}</p>
                        
                        <!-- Réponses existantes -->
                        <div id="replies-{{ $question->id }}" class="space-y-2" style="margin-left: 1rem; border-left: 2px solid var(--border); padding-left: 1rem;">
                            @foreach($question->replies as $reply)
                                <div style="font-size: 0.875rem; background: white; padding: 0.75rem; border-radius: 0.5rem;">
                                    <p style="font-weight: 600;">{{ $reply->user->name ?? 'Panéliste' }}</p>
                                    <p>{{ $reply->content }}</p>
                                </div>
                            @endforeach
                        </div>

                        <!-- Formulaire de réponse -->
                        <form action="{{ route('dashboard.moderator.reply', $question->id) }}" method="POST" style="margin-top: 1rem;">
                            @csrf
                            <div style="display: flex; gap: 0.5rem;">
                                <textarea name="content" id="ai-response-{{ $question->id }}" class="form-input" rows="2" placeholder="Votre réponse..." required style="font-size: 0.875rem;"></textarea>
                                <button type="submit" class="btn-brand" style="width: auto; padding: 0 1.5rem;">Répondre</button>
                            </div>
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                <button type="button" class="btn-brand" style="background: #f3f4f6; color: #374151; width: auto; padding: 0.25rem 0.75rem; font-size: 0.75rem;" onclick="startVoiceRecording('{{ $question->id }}')">
                                    🎤 Réponse Vocale
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <p style="text-align: center; color: var(--muted-foreground); padding: 2rem;">Aucune question pour le moment.</p>
                @endforelse
            </div>
        </div>

        <!-- Sidebar Panéliste -->
        <div class="space-y-5">
            <div class="card">
                <h2 class="section-title">Thème de l'événement</h2>
                <p style="font-size: 0.875rem; color: var(--muted-foreground); line-height: 1.6;">
                    {{ $event->description ?? 'Aucune description fournie.' }}
                </p>
            </div>
            
            <div class="card">
                <h2 class="section-title">Actions Rapides</h2>
                <div style="display: grid; gap: 0.75rem;">
                    <a href="{{ route('projection.index', $event->code) }}" target="_blank" class="btn-brand" style="background: var(--muted); color: var(--foreground); text-align: center;">
                        Voir la Projection
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div id="upload-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div class="card" style="max-width: 30rem; width: 90%;">
        <h2 class="section-title">Uploader un document</h2>
        <p style="margin-bottom: 1.5rem; font-size: 0.875rem; color: var(--muted-foreground);">L'IA utilisera ce document pour vous proposer des réponses pertinentes.</p>
        
        <form action="{{ route('panelist.upload', $event->code) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <input type="file" name="presentation" class="form-input" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" class="btn-brand" style="background: var(--muted); color: var(--foreground);" onclick="document.getElementById('upload-modal').style.display='none'">Annuler</button>
                <button type="submit" class="btn-brand">Uploader</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function suggestAI(questionId) {
        const textarea = document.getElementById('ai-response-' + questionId);
        textarea.placeholder = "L'IA réfléchit...";
        
        fetch("{{ route('panelist.ai-suggest', $event->code) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ question_id: questionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.suggestion) {
                textarea.value = data.suggestion;
            } else {
                alert("L'IA n'a pas pu générer de suggestion.");
            }
            textarea.placeholder = "Votre réponse...";
        })
        .catch(error => {
            console.error('Error:', error);
            textarea.placeholder = "Votre réponse...";
        });
    }

    function startVoiceRecording(questionId) {
        if (!('webkitSpeechRecognition' in window)) {
            alert("Votre navigateur ne supporte pas la reconnaissance vocale.");
            return;
        }

        const recognition = new webkitSpeechRecognition();
        recognition.lang = 'fr-FR';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        recognition.onstart = function() {
            console.log('Voice recognition started');
        };

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            document.getElementById('ai-response-' + questionId).value = transcript;
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error', event.error);
        };

        recognition.start();
    }
</script>
@endpush

@endsection
