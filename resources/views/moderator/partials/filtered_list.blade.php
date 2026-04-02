<div class="space-y-4">
    @forelse($filteredByAI as $q)
    <div class="card" style="border-left: 4px solid #f97316; background: #fffaf5;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div>
                <span class="badge" style="background: #ffedd5; color: #9a3412; margin-bottom: 0.5rem;">FILTRÉ PAR IA (🤖)</span>
                <p style="font-size: 1.125rem; font-weight: 500;">{{ $q->content }}</p>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.5rem;">
                    <span>Par <strong>{{ $q->pseudo }}</strong></span>
                    <span>•</span>
                    <span>{{ $q->created_at->diffForHumans() }}</span>
                </div>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <form action="{{ route('dashboard.moderator.status', $q->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn-brand" style="background: #059669; padding: 0.5rem 1rem; font-size: 0.75rem;">✔️ Récupérer la question</button>
                </form>
            </div>
        </div>

        {{-- Explication de l'IA --}}
        <div style="background: #fff; padding: 1rem; border-radius: 0.75rem; border: 1px solid #fed7aa; margin-top: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--brand); font-weight: 700; font-size: 0.75rem;">
                <span>🤖 ANALYSE DE L'ASSISTANT</span>
            </div>
            @foreach($q->replies->where('pseudo', 'Assistant Modérateur') as $reply)
                <p style="font-size: 0.875rem; color: var(--foreground);">{{ $reply->content }}</p>
            @endforeach
        </div>
    </div>
    @empty
    <div class="card" style="text-align: center; padding: 4rem; color: var(--muted-foreground);">
        Aucune question filtrée par l'IA pour le moment.
    </div>
    @endforelse
</div>
