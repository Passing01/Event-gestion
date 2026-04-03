<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Panelist extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'sector',
        'presentation_duration',
        'presentation_started_at',
        'presentation_path',
        'is_document_shared',
        'is_projecting',
        'notes',
    ];

    protected $casts = [
        'presentation_duration' => 'integer',
        'presentation_started_at' => 'datetime',
        'is_document_shared' => 'boolean',
        'is_projecting' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
