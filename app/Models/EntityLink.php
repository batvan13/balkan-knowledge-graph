<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityLink extends Model
{
    protected $fillable = ['entity_id', 'type', 'url', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
