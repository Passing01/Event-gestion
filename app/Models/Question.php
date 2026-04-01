<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'event_id',
        'pseudo',
        'content',
        'audio_path',
        'status',
        'votes_count',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }
}
