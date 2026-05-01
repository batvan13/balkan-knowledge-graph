<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    public function entityType(): BelongsTo
    {
        return $this->belongsTo(EntityType::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entityAmenities(): HasMany
    {
        return $this->hasMany(EntityAmenity::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(EntityMedia::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(EntityContact::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(EntityLink::class);
    }
}
