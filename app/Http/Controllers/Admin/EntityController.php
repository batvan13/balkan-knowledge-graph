<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityType;
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
        $entity->load(['entityType', 'place', 'user', 'translations']);

        return view('admin.entities.edit', compact('entity'));
    }
}
