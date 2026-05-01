<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityMedia;
use Illuminate\Http\Request;

class EntityMediaController extends Controller
{
    public function store(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'type'       => ['required', 'in:image,video'],
            'path'       => ['nullable', 'string', 'max:1000', 'required_without:url'],
            'url'        => ['nullable', 'string', 'url', 'max:2048', 'required_without:path'],
            'is_cover'   => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $media = new EntityMedia();
        $media->entity_id  = $entity->id;
        $media->type       = $validated['type'];
        $media->path       = $validated['path'] ?? null;
        $media->url        = $validated['url'] ?? null;
        $media->is_cover   = (bool) $validated['is_cover'];
        $media->sort_order = (int) ($validated['sort_order'] ?? 0);
        $media->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Media row added.');
    }

    public function edit(Entity $entity, EntityMedia $media)
    {
        abort_if($media->entity_id !== $entity->id, 404);

        return view('admin.entities.media.edit', compact('entity', 'media'));
    }

    public function update(Request $request, Entity $entity, EntityMedia $media)
    {
        abort_if($media->entity_id !== $entity->id, 404);

        $validated = $request->validate([
            'type'       => ['required', 'in:image,video'],
            'path'       => ['nullable', 'string', 'max:1000', 'required_without:url'],
            'url'        => ['nullable', 'string', 'url', 'max:2048', 'required_without:path'],
            'is_cover'   => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $media->type       = $validated['type'];
        $media->path       = $validated['path'] ?? null;
        $media->url        = $validated['url'] ?? null;
        $media->is_cover   = (bool) $validated['is_cover'];
        $media->sort_order = (int) ($validated['sort_order'] ?? 0);
        $media->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Media row updated.');
    }
}
