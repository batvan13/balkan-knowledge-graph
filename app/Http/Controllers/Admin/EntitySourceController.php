<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntitySource;
use Illuminate\Http\Request;

class EntitySourceController extends Controller
{
    public function store(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'source_type'   => ['required', 'in:official_website,social_profile,manual_entry,third_party_listing'],
            'source_url'    => ['nullable', 'string', 'url', 'max:2048'],
            'is_official'   => ['boolean'],
            'first_seen_at' => ['nullable', 'date'],
            'last_seen_at'  => ['nullable', 'date'],
        ]);

        $source = new EntitySource();
        $source->entity_id    = $entity->id;
        $source->source_type  = $validated['source_type'];
        $source->source_url   = $validated['source_url'] ?? null;
        $source->is_official  = (bool) $validated['is_official'];
        $source->first_seen_at = $validated['first_seen_at'] ?? null;
        $source->last_seen_at  = $validated['last_seen_at'] ?? null;
        $source->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Source added.');
    }

    public function edit(Entity $entity, EntitySource $source)
    {
        abort_if($source->entity_id !== $entity->id, 404);

        return view('admin.entities.sources.edit', compact('entity', 'source'));
    }

    public function update(Request $request, Entity $entity, EntitySource $source)
    {
        abort_if($source->entity_id !== $entity->id, 404);

        $validated = $request->validate([
            'source_type'   => ['required', 'in:official_website,social_profile,manual_entry,third_party_listing'],
            'source_url'    => ['nullable', 'string', 'url', 'max:2048'],
            'is_official'   => ['boolean'],
            'first_seen_at' => ['nullable', 'date'],
            'last_seen_at'  => ['nullable', 'date'],
        ]);

        $source->source_type   = $validated['source_type'];
        $source->source_url    = $validated['source_url'] ?? null;
        $source->is_official   = (bool) $validated['is_official'];
        $source->first_seen_at = $validated['first_seen_at'] ?? null;
        $source->last_seen_at  = $validated['last_seen_at'] ?? null;
        $source->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Source updated.');
    }
}
