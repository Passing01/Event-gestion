@forelse($hands as $index => $hand)
<div style="min-width: 12rem; background: var(--muted); padding: 1rem; border-radius: 0.75rem; border: 1px solid {{ $hand->status == 'called' ? 'var(--brand)' : 'transparent' }};">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
        <span class="badge" style="background: var(--brand); color: #fff;">#{{ $index + 1 }}</span>
        <span style="font-size: 0.625rem; color: var(--muted-foreground);">{{ $hand->created_at->diffForHumans() }}</span>
    </div>
    <p style="font-weight: 700; margin-bottom: 1rem;">{{ $hand->pseudo }}</p>
    <div style="display: flex; gap: 0.5rem;">
        @if($hand->status == 'pending')
        <form action="{{ route('dashboard.moderator.hand-status', $hand->id) }}" method="POST" style="flex: 1;">
            @csrf
            <input type="hidden" name="status" value="called">
            <button type="submit" class="btn-brand" style="padding: 0.375rem; font-size: 0.75rem;">Appeler</button>
        </form>
        @endif
        <form action="{{ route('dashboard.moderator.hand-status', $hand->id) }}" method="POST" style="flex: 1;">
            @csrf
            <input type="hidden" name="status" value="dismissed">
            <button type="submit" class="btn-brand" style="background: #fff; color: var(--foreground); border: 1px solid var(--border); padding: 0.375rem; font-size: 0.75rem;">Terminer</button>
        </form>
    </div>
</div>
@empty
<p style="font-size: 0.875rem; color: var(--muted-foreground); padding: 1rem 0;">Aucune main levée pour le moment.</p>
@endforelse
