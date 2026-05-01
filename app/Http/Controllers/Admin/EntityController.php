<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccommodationDetail;
use App\Models\Amenity;
use App\Models\AttractionDetail;
use App\Models\Entity;
use App\Models\EntityType;
use App\Models\FoodPlaceDetail;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EntityController extends Controller
{
    public function index(Request $request)
    {
        $query = Entity::with(['entityType', 'place'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $entities = $query->paginate(25)->withQueryString();

        return view('admin.entities.index', compact('entities'));
    }

    public function create()
    {
        $entityTypes = EntityType::orderBy('code')->get();
        $places = Place::orderBy('slug')->get();

        return view('admin.entities.create', compact('entityTypes', 'places'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_type_id' => ['required', 'integer', 'exists:entity_types,id'],
            'place_id'       => ['required', 'integer', 'exists:places,id'],
            'status'         => ['required', 'in:draft,published,archived'],
        ]);

        $entity = new Entity();
        $entity->entity_type_id = $validated['entity_type_id'];
        $entity->place_id       = $validated['place_id'];
        $entity->status         = $validated['status'];
        $entity->user_id        = $request->user()->id;
        $entity->slug           = 'entity-' . Str::uuid();
        $entity->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Entity created.');
    }

    public function edit(Entity $entity)
    {
        $entity->load([
            'entityType', 'place', 'user', 'translations',
            'contacts', 'links', 'sources', 'media', 'entityAmenities',
            'priceSignals',
            'outgoingRelations.toEntity.entityType',
            'outgoingRelations.toEntity.translations',
            'incomingRelations.fromEntity.entityType',
            'incomingRelations.fromEntity.translations',
        ]);

        $family = $entity->detailFamily();

        $detail = match ($family) {
            'accommodation' => AccommodationDetail::where('entity_id', $entity->id)->first(),
            'food_place'    => FoodPlaceDetail::where('entity_id', $entity->id)->first(),
            'attraction'    => AttractionDetail::where('entity_id', $entity->id)->first(),
            default         => null,
        };

        $allAmenities      = Amenity::with(['translations' => fn($q) => $q->where('locale', 'bg')])->orderBy('code')->get();
        $selectedAmenityIds = $entity->entityAmenities->pluck('amenity_id')->toArray();

        $otherEntities = Entity::with(['entityType', 'translations'])
            ->where('id', '!=', $entity->id)
            ->orderBy('id')
            ->get();

        $entityTypes = EntityType::orderBy('code')->get();
        $places      = Place::orderBy('slug')->get();

        return view('admin.entities.edit', compact(
            'entity', 'family', 'detail',
            'allAmenities', 'selectedAmenityIds',
            'otherEntities',
            'entityTypes', 'places'
        ));
    }

    public function generateSlug(Entity $entity)
    {
        if (!str_starts_with($entity->slug, 'entity-')) {
            return redirect()->route('admin.entities.edit', $entity)
                ->with('error', 'Slug is already set and cannot be regenerated.');
        }

        $bgTranslation = $entity->translations()
            ->where('locale', 'bg')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->first();

        if (!$bgTranslation) {
            return redirect()->route('admin.entities.edit', $entity)
                ->with('error', 'No valid Bulgarian translation found. Add a BG translation with a name first.');
        }

        $newSlug = Entity::generateSlugFromBgName($bgTranslation->name, $entity->id);

        if ($newSlug === '') {
            return redirect()->route('admin.entities.edit', $entity)
                ->with('error', 'Could not generate a usable slug from the Bulgarian name.');
        }

        $entity->slug = $newSlug;
        $entity->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Slug generated: ' . $newSlug);
    }

    public function updateCore(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'entity_type_id' => ['required', 'integer', 'exists:entity_types,id'],
            'place_id'       => ['required', 'integer', 'exists:places,id'],
            'status'         => ['required', 'in:draft,published,archived'],
        ]);

        $entity->loadMissing('entityType');
        $currentFamily = $entity->detailFamily();

        $newType   = EntityType::find($validated['entity_type_id']);
        $newFamily = Entity::detailFamilyForCode($newType->code);

        if ($currentFamily !== null && $currentFamily !== $newFamily) {
            $detailExists = match ($currentFamily) {
                'accommodation' => AccommodationDetail::where('entity_id', $entity->id)->exists(),
                'food_place'    => FoodPlaceDetail::where('entity_id', $entity->id)->exists(),
                'attraction'    => AttractionDetail::where('entity_id', $entity->id)->exists(),
                default         => false,
            };

            if ($detailExists) {
                return back()->withInput()->withErrors([
                    'entity_type_id' => 'Cannot change entity type across families: a ' . $currentFamily . ' detail record exists. Remove the detail data first.',
                ]);
            }
        }

        $entity->entity_type_id = $validated['entity_type_id'];
        $entity->place_id       = $validated['place_id'];
        $entity->status         = $validated['status'];
        $entity->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Core entity data updated.');
    }
}
