<div class="space-y-4">
    @forelse($questions as $q)
    <div class="card" id="q-{{ $q->id }}" style="border-left: 4px solid {{ $q->status == 'answering' ? 'var(--brand)' : ($q->status == 'approved' ? '#059669' : ($q->status == 'answered' ? '#6b7280' : 'var(--border)')) }};">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span class="badge" style="background: {{ $q->status == 'answering' ? 'var(--brand)' : ($q->status == 'answered' ? '#f3f4f6' : '#ecfdf5') }}; color: {{ $q->status == 'answering' ? '#fff' : ($q->status == 'answered' ? '#6b7280' : '#059669') }};">
                        {{ strtoupper($q->status == 'answering' ? 'en cours' : ($q->status == 'answered' ? 'répondu' : 'approuvé')) }}
                    </span>
                    <span style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $q->votes_count }} votes</span>
                </div>
                <p style="font-size: 1.125rem; font-weight: 500; color: var(--foreground);">{{ $q->content }}</p>
                
                @if($q->audio_path)
                    <div style="margin-top: 0.75rem;">
                        <audio controls style="height: 35px; width: 100%; max-width: 300px;">
                            <source src="{{ asset('storage/' . $q->audio_path) }}" type="audio/webm">
                        </audio>
                    </div>
                @endif
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button onclick="getAISuggestion('{{ $q->id }}')" class="btn-brand" style="background: var(--muted); color: var(--brand); padding: 0.5rem; border: 1px solid var(--brand); font-size: 0.75rem;" title="Demander à l'IA">
                    ✨ IA
                </button>
                <button onclick="openReplyModal('{{ $q->id }}', '{{ addslashes($q->content) }}')" class="btn-brand" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                    Répondre
                </button>
            </div>
        </div>

        {{-- Réponses existantes --}}
        @if($q->replies->count() > 0)
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <div class="space-y-3">
                @foreach($q->replies as $reply)
                <div style="background: var(--muted); padding: 0.75rem; border-radius: 0.75rem; font-size: 0.875rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 700;">{{ $reply->pseudo }}</span>
                        @if($reply->is_moderator)
                        <span class="badge" style="background: var(--brand-light); color: var(--brand); font-size: 0.625rem;">PANÉLISTE</span>
                        @endif
                    </div>
                    <p style="color: var(--muted-foreground);">{{ $reply->content }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @empty
    <div style="text-align: center; padding: 4rem 2rem; background: #fff; border-radius: 1rem; border: 2px dashed var(--border);">
        <p style="color: var(--muted-foreground); font-size: 1rem;">En attente de questions approuvées par les modérateurs...</p>
    </div>
    @endforelse
</div>
