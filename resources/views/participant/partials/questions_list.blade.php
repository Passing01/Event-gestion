<div class="space-y-4">
    @forelse($questions as $q)
    <div class="card" id="q-{{ $q->id }}" style="border-left: 4px solid {{ $q->status == 'answering' ? 'var(--brand)' : ($q->status == 'answered' ? '#6b7280' : ($q->status == 'rejected' ? '#dc2626' : 'var(--border)')) }};">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    @if($q->status == 'rejected')
                        <span class="badge" style="background: #fee2e2; color: #dc2626;">FILTRÉ PAR IA 🤖</span>
                    @elseif($q->status == 'pending')
                        <span class="badge" style="background: #fef3c7; color: #d97706;">EN ATTENTE DE MODÉRATION</span>
                    @elseif($q->status == 'answering')
                        <span class="badge" style="background: var(--brand); color: white;">EN COURS DE RÉPONSE 🎤</span>
                    @endif
                    <span style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $q->created_at->diffForHumans() }}</span>
                </div>
                <p style="font-size: 1rem; font-weight: 500;">{{ $q->content }}</p>

                @if($q->audio_path)
                    <div style="margin-top: 0.5rem;">
                        <audio controls style="height: 30px; max-width: 100%;">
                            <source src="{{ asset('storage/' . $q->audio_path) }}" type="audio/webm">
                        </audio>
                    </div>
                @endif
            </div>

            <div style="text-align: right; min-width: 60px;">
                <form action="{{ route('participant.vote', $q->id) }}" method="POST">
                    @csrf
                    <button type="submit" style="background: none; border: 1px solid var(--border); border-radius: 0.5rem; padding: 0.5rem; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                        <span style="font-size: 1.25rem;">👍</span>
                        <span style="font-size: 0.75rem; font-weight: 700;">{{ $q->votes_count }}</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Réponses --}}
        @if($q->replies->count() > 0)
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <div class="space-y-3">
                @foreach($q->replies as $reply)
                <div style="background: var(--muted); padding: 0.75rem; border-radius: 0.75rem; font-size: 0.875rem; {{ $reply->pseudo == 'Assistant Modérateur' ? 'border: 1px solid #fed7aa; background: #fffaf5;' : '' }}">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 700;">{{ $reply->pseudo }}</span>
                        @if($reply->is_moderator && $reply->pseudo != 'Assistant Modérateur')
                            <span class="badge" style="background: var(--brand-light); color: var(--brand); font-size: 0.625rem;">OFFICIEL</span>
                        @endif
                    </div>
                    <p>{{ $reply->content }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- Lien pour répondre soi-même --}}
        @if($q->status != 'rejected')
        <div style="margin-top: 1rem;">
            <button onclick="toggleReplyForm('{{ $q->id }}')" style="font-size: 0.75rem; color: var(--brand); background: none; border: none; cursor: pointer; padding: 0;">Ajouter un commentaire...</button>
            <form id="reply-form-{{ $q->id }}" action="{{ route('participant.reply', $q->id) }}" method="POST" style="display: none; margin-top: 0.5rem;">
                @csrf
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" name="content" class="form-input" placeholder="Votre réponse..." style="font-size: 0.75rem; padding: 0.4rem;" required>
                    <button type="submit" class="btn-brand" style="width: auto; padding: 0 1rem; font-size: 0.75rem;">Envoyer</button>
                </div>
            </form>
        </div>
        @endif
    </div>
    @empty
    <div class="card" style="text-align: center; padding: 4rem 2rem; color: var(--muted-foreground);">
        <p>Soyez le premier à poser une question !</p>
    </div>
    @endforelse
</div>
