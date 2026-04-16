<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'scheduled_at',
        'code',
        'date',
        'moderation_enabled',
        'anonymous_allowed',
        'status',
        'is_on_marketplace',
        'marketplace_price',
        'closed_at',
        'ai_summary',
        'ai_keywords',
        'ai_sentiment',
        'ai_report',
        'image_path',
        'collect_presence',
        'end_date',
        'is_forced_open',
    ];

    protected $casts = [
        'date' => 'date',
        'scheduled_at' => 'datetime',
        'closed_at' => 'datetime',
        'moderation_enabled' => 'boolean',
        'anonymous_allowed' => 'boolean',
        'is_on_marketplace' => 'boolean',
        'ai_keywords' => 'json',
        'collect_presence' => 'boolean',
        'end_date' => 'date',
        'is_forced_open' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function panelists(): HasMany
    {
        return $this->hasMany(Panelist::class);
    }

    public function projectionLogs(): HasMany
    {
        return $this->hasMany(ProjectionLog::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function raisedHands(): HasMany
    {
        return $this->hasMany(RaisedHand::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }
}
