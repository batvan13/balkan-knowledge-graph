<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntitySource extends Model
{
    protected $fillable = [
        'entity_id',
        'source_type',
        'source_url',
        'is_official',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'is_official'    => 'boolean',
        'first_seen_at'  => 'datetime',
        'last_seen_at'   => 'datetime',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
