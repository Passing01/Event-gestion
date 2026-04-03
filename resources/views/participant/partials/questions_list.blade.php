<div style="display: grid; gap: 1.25rem;">
    @forelse($questions as $q)
    <div class="card" id="q-{{ $q->id }}" style="padding: 1.25rem; border-radius: 1.25rem; border: 1px solid var(--border); border-left: 6px solid {{ $q->status == 'answering' ? 'var(--brand)' : ($q->status == 'answered' ? '#94a3b8' : ($q->status == 'rejected' ? '#ef4444' : '#e2e8f0')) }}; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        
        {{-- En-tête de la carte --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1rem;">
            <div style="flex: 1;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    @if($q->status == 'answering')
                        <span style="background: var(--brand); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 999px; display: flex; align-items: center; gap: 0.25rem;">🎤 EN DIRECT</span>
                    @endif

                    @if($q->type == 'contribution')
                        <span style="background: #f0f9ff; color: #0369a1; font-size: 0.65rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 999px; border: 1px solid #bae6fd;">💡 APPORT</span>
                    @else
                        <span style="background: #f0fdf4; color: #166534; font-size: 0.65rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 999px; border: 1px solid #bbf7d0;">❓ QUESTION</span>
                    @endif

                    @if($q->panelist)
                        <span style="background: #f8fafc; color: var(--brand); font-size: 0.65rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 999px; border: 1px solid var(--brand-soft); display: flex; align-items: center; gap: 0.25rem;">
                            🎯 CIBLÉ : {{ $q->panelist->pseudo }}
                        </span>
                    @endif

                    @if($q->status == 'pending')
                        <span style="background: #fffbeb; color: #92400e; font-size: 0.65rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 999px; border: 1px solid #fde68a;">⏳ MODÉRATION</span>
                    @endif

                    @if($q->status == 'rejected')
                        <span style="background: #fef2f2; color: #991b1b; font-size: 0.65rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 999px; border: 1px solid #fecaca;">🤖 FILTRÉ IA</span>
                    @endif

                    <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 600;">{{ $q->created_at->diffForHumans() }}</span>
                </div>

                <div style="font-size: 0.95rem; font-weight: 600; color: var(--foreground); line-height: 1.5; word-wrap: break-word;">
                    {!! nl2br(e($q->content)) !!}
                </div>

                @if($q->audio_path)
                    <div style="margin-top: 1rem; background: #f8fafc; padding: 0.5rem; border-radius: 1rem; border: 1px solid #f1f5f9;">
                        <audio controls style="height: 32px; width: 100%;">
                            <source src="{{ asset('storage/' . $q->audio_path) }}" type="audio/webm">
                        </audio>
                    </div>
                @endif
            </div>

            {{-- Vote Button --}}
            <div style="flex-shrink: 0;">
                <form action="{{ route('participant.vote', $q->id) }}" method="POST">
                    @csrf
                    @php $voted = in_array($q->id, session('voted_questions', [])); @endphp
                    <button type="submit" style="background: {{ $voted ? 'var(--brand)' : '#f1f5f9' }}; color: {{ $voted ? '#fff' : '#64748b' }}; border: none; border-radius: 1rem; padding: 0.6rem 0.75rem; display: flex; flex-direction: column; align-items: center; gap: 0.25rem; transition: all 0.2s; min-width: 3.5rem; cursor: pointer; box-shadow: {{ $voted ? '0 4px 10px var(--brand-soft)' : 'none' }};">
                        <span style="font-size: 1.1rem;">{{ $voted ? '❤️' : '🤍' }}</span>
                        <span style="font-size: 0.85rem; font-weight: 800;">{{ $q->votes_count }}</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Section Réponses --}}
        @if($q->replies->count() > 0)
        <div style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach($q->replies as $reply)
                <div style="max-width: 90%; align-self: flex-start; background: #f8fafc; padding: 0.85rem; border-radius: 0 1rem 1rem 1rem; border: 1px solid #f1f5f9; position: relative; {{ $reply->pseudo == 'Assistant Modérateur' ? 'border-color: #fed7aa; background: #fffcf0;' : '' }}">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: var(--foreground);">{{ $reply->pseudo }}</span>
                        @if($reply->is_moderator)
                            @if($reply->pseudo == 'Modérateur')
                                <span style="background: #f1f5f9; color: #475569; font-size: 0.6rem; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 4px;">MODÉRATEUR</span>
                            @elseif($reply->pseudo != 'Assistant Modérateur')
                                <span style="background: var(--brand); color: #fff; font-size: 0.6rem; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 4px;">RÉPONSE OFFICIELLE</span>
                            @endif
                        @endif
                    </div>
                    <div style="font-size: 0.85rem; color: #475569; line-height: 1.5;">
                        {{ $reply->content }}
                    </div>
                    @if($reply->audio_path)
                        <div style="margin-top: 0.5rem;">
                            <audio controls style="height: 25px; width: 100%;">
                                <source src="{{ asset('storage/' . $reply->audio_path) }}" type="audio/webm">
                            </audio>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif
        
        {{-- Commenter --}}
        @if($q->status != 'rejected')
        <div style="margin-top: 1.25rem;">
            <button onclick="toggleReplyForm('{{ $q->id }}')" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; font-size: 0.75rem; font-weight: 700; padding: 0.5rem 1rem; border-radius: 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;">
                💬 Ajouter un commentaire...
            </button>
            <form id="reply-form-{{ $q->id }}" action="{{ route('participant.reply', $q->id) }}" method="POST" style="display: none; margin-top: 0.75rem; animation: slideIn 0.2s ease;">
                @csrf
                <div style="display: flex; gap: 0.5rem; width: 100%;">
                    <input type="text" name="content" class="form-input" placeholder="Écrivez votre réponse..." style="font-size: 0.85rem; padding: 0.75rem; border-radius: 0.75rem; flex: 1;" required>
                    <button type="submit" class="btn-brand" style="width: auto; padding: 0 1.25rem; font-size: 0.8rem; border-radius: 0.75rem; font-weight: 800;">
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
    @empty
    <div style="text-align: center; padding: 5rem 2rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">💬</div>
        <p style="color: #94a3b8; font-weight: 600; font-size: 1rem;">Aucune question pour le moment.<br>Soyez le premier à prendre la parole !</p>
    </div>
    @endforelse
</div>
