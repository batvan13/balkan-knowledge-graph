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

    public function translations(): HasMany
    {
        return $this->hasMany(EntityTranslation::class);
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

    public function sources(): HasMany
    {
        return $this->hasMany(EntitySource::class);
    }

    public function outgoingRelations(): HasMany
    {
        return $this->hasMany(EntityRelation::class, 'from_entity_id');
    }

    public function incomingRelations(): HasMany
    {
        return $this->hasMany(EntityRelation::class, 'to_entity_id');
    }

    public function priceSignals(): HasMany
    {
        return $this->hasMany(EntityPriceSignal::class);
    }

    public static function detailFamilyForCode(?string $code): ?string
    {
        if (in_array($code, [
            'hotel', 'guesthouse', 'apartment', 'house', 'villa',
            'hostel', 'bungalow', 'camping', 'lodge',
        ])) {
            return 'accommodation';
        }

        if (in_array($code, [
            'restaurant', 'tavern', 'bar', 'pub', 'cafe',
            'bistro', 'fast_food', 'pastry_shop',
        ])) {
            return 'food_place';
        }

        if (in_array($code, [
            'museum', 'gallery', 'monument', 'monastery', 'church', 'chapel',
            'fortress', 'castle', 'palace', 'tomb', 'megalith', 'waterfall',
            'cave', 'beach', 'park', 'reservoir', 'spring', 'rock_formation',
            'heritage_tree', 'observatory', 'planetarium', 'zoo',
        ])) {
            return 'attraction';
        }

        return null;
    }

    public function detailFamily(): ?string
    {
        return static::detailFamilyForCode($this->entityType?->code);
    }
}
