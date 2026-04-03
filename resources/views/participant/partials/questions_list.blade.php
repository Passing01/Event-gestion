<div class="space-y-4">
    @forelse($questions as $q)
    <div class="card" id="q-{{ $q->id }}" style="padding: 1rem; border-left: 4px solid {{ $q->status == 'answering' ? 'var(--brand)' : ($q->status == 'answered' ? '#6b7280' : ($q->status == 'rejected' ? '#dc2626' : 'var(--border)')) }};">
        <div style="display: flex; justify-content: space-between; gap:0.5rem; margin-bottom: 0.75rem;">
            <div style="flex: 1;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    @if($q->type == 'contribution')
                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 10px;">💡 APPORT</span>
                    @else
                        <span class="badge" style="background: #f0fdf4; color: #15803d; font-size: 10px;">❓ QUESTION</span>
                    @endif

                    @if($q->panelist)
                        <span class="badge" style="background: #f3f4f6; color: #374151; font-size: 10px; border: 1px solid var(--border);">@ {{ $q->panelist->pseudo }}</span>
                    @endif

                    @if($q->status == 'rejected')
                        <span class="badge" style="background: #fee2e2; color: #dc2626; font-size: 10px;">FILTRÉ PAR IA 🤖</span>
                    @elseif($q->status == 'pending')
                        <span class="badge" style="background: #fef3c7; color: #d97706; font-size: 10px;">EN ATTENTE</span>
                    @elseif($q->status == 'answering')
                        <span class="badge" style="background: var(--brand); color: white; font-size: 10px;">EN COURS🎤</span>
                    @endif
                    <span style="font-size: 10px; color: var(--muted-foreground);">{{ $q->created_at->diffForHumans() }}</span>
                </div>
                <p style="font-size: 0.95rem; font-weight: 500; line-height: 1.4; margin: 0;">{{ $q->content }}</p>
                @if($q->audio_path)
                    <div style="margin-top: 0.5rem;">
                        <audio controls style="height: 30px; max-width: 100%;">
                            <source src="{{ asset('storage/' . $q->audio_path) }}" type="audio/webm">
                        </audio>
                    </div>
                @endif
            </div>
            
            <div style="flex-shrink: 0;">
                <form action="{{ route('participant.vote', $q->id) }}" method="POST">
                    @csrf
                    <button type="submit" style="background: {{ in_array($q->id, session('voted_questions', [])) ? 'var(--brand)' : '#f3f4f6' }}; color: {{ in_array($q->id, session('voted_questions', [])) ? '#fff' : 'var(--foreground)' }}; border: none; border-radius: 0.5rem; padding: 0.4rem 0.6rem; display: flex; flex-direction: column; align-items: center; gap: 2px;">
                        <span style="font-size: 1rem;">👍</span>
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
                        @if($reply->is_moderator)
                            @if($reply->pseudo == 'Modérateur')
                                <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 0.625rem;">SUGGESTION MODÉRATEUR</span>
                            @elseif($reply->pseudo != 'Assistant Modérateur')
                                <span class="badge" style="background: var(--brand-light); color: var(--brand); font-size: 0.625rem;">OFFICIEL</span>
                            @endif
                        @endif
                    </div>
                    <p>{{ $reply->content }}</p>
                    @if($reply->audio_path)
                        <div style="margin-top: 0.5rem;">
                            <audio controls style="height: 25px; max-width: 100%;">
                                <source src="{{ asset('storage/' . $reply->audio_path) }}" type="audio/webm">
                            </audio>
                        </div>
                    @endif
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
