<?php

namespace App\Http\Controllers;

use App\Models\AccommodationDetail;
use App\Models\AttractionDetail;
use App\Models\Entity;
use App\Models\FoodPlaceDetail;

class EntityPageController extends Controller
{
    public function show(string $slug)
    {
        $entity = Entity::where('slug', $slug)
            ->where('status', 'published')
            ->whereHas('translations', fn($q) => $q
                ->where('locale', 'bg')
                ->whereNotNull('name')
                ->where('name', '!=', '')
            )
            ->firstOrFail();

        $entity->load([
            'entityType',
            'place.translations',
            'translations',
            'media',
            'entityAmenities.amenity.translations',
            'contacts',
            'links',
            'priceSignals',
        ]);

        $bgTranslation = $entity->translations->firstWhere('locale', 'bg');
        $enTranslation = $entity->translations->firstWhere('locale', 'en');

        $bgPlaceName = $entity->place?->translations
            ->firstWhere('locale', 'bg')?->name
            ?? $entity->place?->slug;

        $coverMedia = $entity->media->firstWhere('is_cover', true)
            ?? $entity->media->first();

        $primaryContacts = $entity->contacts->where('is_primary', true);
        if ($primaryContacts->isEmpty()) {
            $primaryContacts = $entity->contacts->take(3);
        }

        $primaryLinks = $entity->links->where('is_primary', true);
        if ($primaryLinks->isEmpty()) {
            $primaryLinks = $entity->links->where('type', 'website');
        }

        $family = $entity->detailFamily();

        $detail = match ($family) {
            'accommodation' => AccommodationDetail::where('entity_id', $entity->id)->first(),
            'food_place'    => FoodPlaceDetail::where('entity_id', $entity->id)->first(),
            'attraction'    => AttractionDetail::where('entity_id', $entity->id)->first(),
            default         => null,
        };

        return view('public.entity.show', compact(
            'entity', 'family', 'detail',
            'bgTranslation', 'enTranslation',
            'bgPlaceName', 'coverMedia',
            'primaryContacts', 'primaryLinks'
        ));
    }
}
