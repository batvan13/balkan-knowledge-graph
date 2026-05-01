<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Place::class, 'parent_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PlaceTranslation::class);
    }

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }
}
