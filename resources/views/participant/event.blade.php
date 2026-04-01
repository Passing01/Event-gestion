@extends('layouts.auth')

@section('title', $event->name . ' – Q&A')

@section('content')

<main class="auth-page" style="padding: 1rem; background: var(--muted);">
    <div class="auth-card" style="max-width: 40rem; width: 100%; padding: 1.5rem;">

        {{-- Header --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
            <div>
                <h1 style="font-size: 1.25rem; margin-bottom: 0.25rem;">{{ $event->name }}</h1>
                <p style="font-size: 0.75rem; color: var(--muted-foreground);">Connecté en tant que <strong>{{ session('participant_pseudo') }}</strong></p>
            </div>
            <div style="background: var(--brand); color: #fff; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600;">
                {{ $event->code }}
            </div>
        </div>

        {{-- Section Main Levée --}}
        @php
            $myHand = $event->raisedHands()->where('pseudo', session('participant_pseudo'))->first();
            $rank = $myHand ? $event->raisedHands()->where('status', 'pending')->where('created_at', '<', $myHand->created_at)->count() + 1 : null;
        @endphp

        <div style="background: var(--muted); padding: 1rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.5rem; height: 2.5rem; background: {{ $myHand ? 'var(--brand)' : '#fff' }}; color: {{ $myHand ? '#fff' : 'var(--brand)' }}; border-radius: 50%; display: grid; place-items: center; font-size: 1.25rem; transition: all 0.3s;">
                    ✋
                </div>
                <div>
                    @if($myHand)
                        <p style="font-size: 0.875rem; font-weight: 700; margin: 0;">Main levée !</p>
                        <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">
                            @if($myHand->status == 'called')
                                <span style="color: var(--brand); font-weight: 800;">C'est à vous de parler !</span>
                            @else
                                Votre rang dans la file : <strong>#{{ $rank }}</strong>
                            @endif
                        </p>
                    @else
                        <p style="font-size: 0.875rem; font-weight: 700; margin: 0;">Une question à l'oral ?</p>
                        <p style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0;">Signalez-vous au modérateur.</p>
                    @endif
                </div>
            </div>
            
            @if($myHand)
                <form action="{{ route('participant.lower-hand', $event->code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-brand" style="background: #fff; color: var(--foreground); border: 1px solid var(--border); width: auto; padding: 0.5rem 1rem; font-size: 0.75rem;">Baisser la main</button>
                </form>
            @else
                <form action="{{ route('participant.raise-hand', $event->code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-brand" style="width: auto; padding: 0.5rem 1rem; font-size: 0.75rem;">Lever la main</button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;font-size:0.875rem;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Input Section --}}
        <div class="card" style="margin-bottom: 2rem; padding: 1.25rem; border: 1px solid var(--brand);">
            <h2 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">Posez votre question</h2>
            <form action="{{ route('participant.ask', $event->code) }}" method="POST">
                @csrf
                <textarea name="content" id="question-content" class="form-input" rows="3" placeholder="Votre question ici..." style="resize: none; margin-bottom: 0.75rem;" required maxlength="200"></textarea>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <button type="button" class="btn-brand" style="background: #f3f4f6; color: #374151; width: auto; padding: 0.5rem 1rem; font-size: 0.75rem;" onclick="startVoiceRecording()">
                            🎤 Vocal
                        </button>
                        <span style="font-size: 0.75rem; color: var(--muted-foreground);">Max 200 caractères</span>
                    </div>
                    <button type="submit" class="btn-brand" style="width: auto; padding: 0.5rem 1.5rem;">Envoyer</button>
                </div>
            </form>
        </div>

        {{-- Questions List --}}
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="font-size: 1rem; font-weight: 600;">Questions du public</h2>
                <span style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $questions->count() }} questions visibles</span>
            </div>

            <div style="display: grid; gap: 1rem; margin-bottom: 2rem;">
                @forelse($questions as $q)
                <div class="card" style="padding: 1rem; {{ $q->status == 'answering' ? 'border: 2px solid var(--brand);' : '' }}">
                    @if($q->status == 'answering')
                        <div style="font-size: 0.625rem; font-weight: 700; color: var(--brand); text-transform: uppercase; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.25rem;">
                            <span style="width: 0.5rem; height: 0.5rem; background: var(--brand); border-radius: 9999px; display: inline-block; animation: pulse 1.5s infinite;"></span>
                            En cours de réponse
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="flex: 1; padding-right: 1rem;">
                            <p style="font-size: 0.875rem; margin-bottom: 0.5rem; line-height: 1.4;">{{ $q->content }}</p>
                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--muted-foreground);">
                                <span>Par <strong>{{ $q->pseudo }}</strong></span>
                                <span>•</span>
                                <span>{{ $q->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <form action="{{ route('participant.vote', $q->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background: {{ in_array($q->id, session('voted_questions', [])) ? 'var(--brand)' : 'var(--muted)' }}; color: {{ in_array($q->id, session('voted_questions', [])) ? '#fff' : 'var(--foreground)' }}; border: none; border-radius: 0.5rem; padding: 0.375rem 0.625rem; display: flex; align-items: center; gap: 0.375rem; cursor: pointer; transition: all 0.2s;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span style="font-weight: 600;">{{ $q->votes_count }}</span>
                            </button>
                        </form>
                    </div>

                    {{-- Réponses --}}
                    @if($q->replies->count() > 0 || session('participant_pseudo'))
                    <div style="margin-top: 1rem; padding-left: 1rem; border-left: 2px solid var(--muted);">
                        <div style="display: grid; gap: 0.5rem;">
                            @foreach($q->replies as $reply)
                            <div style="background: var(--muted); padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <span style="font-weight: 600;">{{ $reply->pseudo }}</span>
                                    @if($reply->is_moderator)
                                    <span style="font-size: 0.625rem; background: var(--brand); color: #fff; padding: 0.125rem 0.375rem; border-radius: 9999px;">MODÉRATEUR</span>
                                    @endif
                                    <span style="font-size: 0.625rem; color: var(--muted-foreground);">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <p>{{ $reply->content }}</p>
                            </div>
                            @endforeach
                        </div>
                        
                        @if(session('participant_pseudo'))
                        <form action="{{ route('participant.reply', $q->id) }}" method="POST" style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                            @csrf
                            <input type="text" name="content" class="form-input" placeholder="Répondre..." style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" required maxlength="200">
                            <button type="submit" class="btn-brand" style="width: auto; padding: 0.25rem 0.75rem; font-size: 0.75rem;">Envoyer</button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
                @empty
                <div style="text-align: center; padding: 2rem; color: var(--muted-foreground); font-size: 0.875rem;">
                    Aucune question pour le moment. Soyez le premier à en poser une !
                </div>
                @endforelse
            </div>
        </div>

        {{-- Participants List --}}
        <div style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="font-size: 1rem; font-weight: 600;">Participants connectés</h2>
                <span id="participant-count" style="font-size: 0.75rem; color: var(--muted-foreground);">0 en ligne</span>
            </div>
            <div id="participants-list" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <!-- Rempli par JS -->
            </div>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="{{ route('participant.join') }}" style="font-size: 0.75rem; color: var(--muted-foreground); text-decoration: none;">Quitter l'événement</a>
        </div>
    </div>
</main>

<script>
    // --- Heartbeat & Typing ---
    const eventCode = '{{ $event->code }}';
    const csrfToken = '{{ csrf_token() }}';

    async function sendHeartbeat() {
        try {
            await fetch(`/e/${eventCode}/heartbeat`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
        } catch (e) {}
    }

    let typingTimer;
    async function setTyping(isTyping) {
        try {
            await fetch(`/e/${eventCode}/typing`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_typing: isTyping })
            });
        } catch (e) {}
    }

    document.querySelector('textarea[name="content"]')?.addEventListener('input', () => {
        clearTimeout(typingTimer);
        setTyping(true);
        typingTimer = setTimeout(() => setTyping(false), 3000);
    });

    // --- Active Participants List ---
    async function fetchParticipants() {
        try {
            const response = await fetch(`/e/${eventCode}/active-participants`);
            const data = await response.json();
            
            const list = document.getElementById('participants-list');
            const count = document.getElementById('participant-count');
            
            count.textContent = `${data.length} en ligne`;
            
            list.innerHTML = data.map(p => `
                <div style="background: #fff; border: 1px solid var(--border); padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; display: flex; align-items: center; gap: 0.5rem; ${p.is_speaking ? 'border-color: var(--brand); background: var(--brand-light);' : ''}">
                    <span style="width: 0.5rem; height: 0.5rem; background: ${p.is_speaking ? 'var(--brand)' : '#10b981'}; border-radius: 50%;"></span>
                    <span style="font-weight: 600;">${p.pseudo}</span>
                    ${p.is_typing ? '<span class="typing-dot">...</span>' : ''}
                    ${p.is_speaking ? '🎤' : ''}
                </div>
            `).join('');
        } catch (e) {}
    }

    function startVoiceRecording() {
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
            document.getElementById('question-content').value = transcript;
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error', event.error);
        };

        recognition.start();
    }

    setInterval(sendHeartbeat, 15000);
    setInterval(fetchParticipants, 5000);
    sendHeartbeat();
    fetchParticipants();
</script>

<style>
@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
    100% { opacity: 1; transform: scale(1); }
}
.typing-dot {
    font-weight: 800;
    color: var(--brand);
    animation: blink 1s infinite;
}
@keyframes blink {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}
</style>

@endsection
