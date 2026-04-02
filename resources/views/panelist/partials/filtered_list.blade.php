<div class="space-y-4">
    <div style="background: var(--muted); border-radius: 1rem; padding: 2rem; text-align: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--brand);">Réception des questions filtrées par l'Assistant IA</h2>
        <p style="color: var(--muted-foreground);">Ces questions (doublons ou hors-sujet) ont été écartées. En tant que panéliste, vous pouvez les repêcher.</p>
    </div>

    @forelse($filteredByAI as $q)
    <div class="question-card" style="border-left: 4px solid #f97316; background: #fffaf5; padding: 1.25rem; border-radius: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 2rem; height: 2rem; background: #f97316; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                    {{ substr($q->pseudo ?? 'A', 0, 1) }}
                </div>
                <div>
                    <span class="badge" style="background: #ffedd5; color: #9a3412; margin-bottom: 0.25rem; font-size: 0.625rem;">FILTRÉ PAR IA 🤖</span>
                    <p style="font-weight: 600; font-size: 0.875rem;">{{ $q->pseudo ?? 'Anonyme' }}</p>
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
        
        <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">{{ $q->content }}</p>

        {{-- Analyse de l'IA --}}
        <div style="background: #fff; padding: 1rem; border-radius: 0.75rem; border: 1px solid #fed7aa; margin-top: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: #f97316; font-weight: 700; font-size: 0.75rem;">
                <span>🤖 ANALYSE DE L'ASSISTANT</span>
            </div>
            @foreach($q->replies->where('pseudo', 'Assistant Modérateur') as $reply)
                <p style="font-size: 0.875rem; color: var(--foreground);">{{ $reply->content }}</p>
            @endforeach
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 4rem; color: var(--muted-foreground);">
        Aucune question filtrée par l'IA pour le moment.
    </div>
    @endforelse
</div>
