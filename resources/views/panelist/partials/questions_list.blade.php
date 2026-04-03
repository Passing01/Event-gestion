@forelse($questions as $question)
    <div class="question-card" style="background: var(--muted); padding: 1.25rem; border-radius: 1rem; border: 1px solid var(--border);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 2rem; height: 2rem; background: var(--brand); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                    {{ substr($question->pseudo ?? 'A', 0, 1) }}
                </div>
                <div>
                    <p style="font-weight: 600; font-size: 0.875rem;">{{ $question->pseudo ?? 'Anonyme' }}</p>
                    <div style="display: flex; gap: 0.25rem; margin-top: 0.25rem; flex-wrap: wrap;">
                        @if($question->type == 'contribution')
                            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 9px; padding: 0.1rem 0.4rem;">💡 APPORT</span>
                        @else
                            <span class="badge" style="background: #f0fdf4; color: #15803d; font-size: 9px; padding: 0.1rem 0.4rem;">❓ QUESTION</span>
                        @endif

                        @if($question->panelist_id == session('panelist_id'))
                            <span class="badge" style="background: #4f46e5; color: white; font-size: 9px; padding: 0.1rem 0.4rem; animation: pulse 2s infinite;">🎯 CIBLÉE SUR VOUS</span>
                        @elseif($question->panelist)
                            <span class="badge" style="background: #f3f4f6; color: #374151; font-size: 9px; padding: 0.1rem 0.4rem; border: 1px solid var(--border);">Pour : {{ $question->panelist->pseudo }}</span>
                        @endif
                    </div>
                    <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.25rem;">{{ $question->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                @if($question->status === 'pending')
                    <form action="{{ route('dashboard.moderator.status', $question->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn-brand" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #10b981;">
                            Approuver
                        </button>
                    </form>
                    <form action="{{ route('dashboard.moderator.status', $question->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn-brand" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #ef4444;">
                            Rejeter
                        </button>
                    </form>
                @endif
                <button class="btn-brand" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;" onclick="suggestAI('{{ $question->id }}', this)">
                    💡 Suggestion IA
                </button>
                <span class="badge" style="font-size: 0.625rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: {{ $question->status === 'pending' ? '#f59e0b' : ($question->status === 'approved' ? '#10b981' : ($question->status === 'answering' ? '#3b82f6' : '#6b7280')) }}; color: white;">
                    {{ strtoupper($question->status) }}
                </span>
            </div>
        </div>
        <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">{{ $question->content }}</p>
        
        @if($question->audio_path)
            <div style="margin-bottom: 1rem;">
                <audio controls style="height: 30px; max-width: 100%;">
                    <source src="{{ asset('storage/' . $question->audio_path) }}" type="audio/webm">
                </audio>
            </div>
        @endif
        
        <!-- Réponses existantes -->
        <div id="replies-{{ $question->id }}" class="space-y-2" style="margin-left: 1rem; border-left: 2px solid var(--border); padding-left: 1rem;">
            @foreach($question->replies as $reply)
                <div style="font-size: 0.875rem; background: white; padding: 0.75rem; border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 700;">{{ $reply->pseudo ?? 'Panéliste' }}</span>
                        @if($reply->is_moderator)
                            @if($reply->pseudo == 'Modérateur')
                                <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 0.625rem;">SUGGESTION MODÉRATEUR</span>
                            @else
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

        <!-- Formulaire de réponse -->
        <form action="{{ route('dashboard.moderator.reply', $question->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
            @csrf
            <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                <textarea name="content" id="ai-response-{{ $question->id }}" class="form-input" rows="3" placeholder="Votre réponse..." style="font-size: 0.875rem;" maxlength="5000"></textarea>
                <input type="file" name="audio" id="audio-input-{{ $question->id }}" style="display: none;">
                
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <div id="voice-status-{{ $question->id }}" style="display: none; align-items: center; gap: 0.5rem; background: #fee2e2; color: #dc2626; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                            <span style="width: 0.4rem; height: 0.4rem; background: #dc2626; border-radius: 50%; animation: pulse 1s infinite;"></span>
                            <span id="voice-timer-{{ $question->id }}">0s</span>
                        </div>
                        <button type="button" id="voice-btn-{{ $question->id }}" class="btn-brand" style="background: #f3f4f6; color: #374151; width: auto; padding: 0.25rem 0.75rem; font-size: 0.75rem; display: flex; align-items: center; gap: 0.375rem;" onclick="toggleVoiceRecording('{{ $question->id }}')">
                            <span id="voice-icon-{{ $question->id }}">🎤</span> <span id="voice-text-{{ $question->id }}">Vocal</span>
                        </button>
                    </div>
                    <button type="submit" id="submit-btn-{{ $question->id }}" class="btn-brand" style="width: auto; padding: 0.4rem 1.5rem; font-size: 0.875rem;">Répondre</button>
                </div>
            </div>
        </form>
    </div>
@empty
    <p style="text-align: center; color: var(--muted-foreground); padding: 2rem;">Aucune question pour le moment.</p>
@endforelse
