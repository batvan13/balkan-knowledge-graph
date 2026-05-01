<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.entities.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Entities</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Entity #{{ $entity->id }}
                <span class="text-gray-400 font-normal text-base ml-1">{{ $entity->entityType?->code }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Core entity info --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Core</h3>
                <dl class="grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">ID</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $entity->id }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Status</dt>
                        <dd class="mt-0.5">
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                @if($entity->status === 'published') bg-green-100 text-green-800
                                @elseif($entity->status === 'archived') bg-gray-200 text-gray-600
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $entity->status }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Type</dt>
                        <dd class="font-mono text-gray-900 mt-0.5">{{ $entity->entityType?->code ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Place</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $entity->place?->slug ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Slug</dt>
                        <dd class="font-mono text-xs text-gray-400 mt-0.5">{{ $entity->slug }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Owner</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $entity->user?->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Created</dt>
                        <dd class="text-gray-400 mt-0.5">{{ $entity->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Updated</dt>
                        <dd class="text-gray-400 mt-0.5">{{ $entity->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Details section --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Details</h3>

                @if($family === 'accommodation')
                    @include('admin.entities.details.accommodation', ['entity' => $entity, 'detail' => $detail])
                @elseif($family === 'food_place')
                    @include('admin.entities.details.food_place', ['entity' => $entity, 'detail' => $detail])
                @elseif($family === 'attraction')
                    @include('admin.entities.details.attraction', ['entity' => $entity, 'detail' => $detail])
                @else
                    <p class="text-sm text-gray-400">No structured details available for this entity type.</p>
                @endif
            </div>

            {{-- Contacts section --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Contacts</h3>

                @if($entity->contacts->isNotEmpty())
                    <table class="w-full text-sm text-left mb-6">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">Value</th>
                                <th class="px-3 py-2">Primary</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($entity->contacts->sortBy('type') as $contact)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono text-gray-700">{{ $contact->type }}</td>
                                    <td class="px-3 py-2 text-gray-900">{{ $contact->value }}</td>
                                    <td class="px-3 py-2 text-gray-500">{{ $contact->is_primary ? '✓' : '—' }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('admin.entities.contacts.edit', [$entity, $contact]) }}"
                                           class="text-blue-600 hover:underline text-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-400 mb-6">No contacts yet.</p>
                @endif

                <div class="border-t border-gray-100 pt-5">
                    <h4 class="text-sm font-medium text-gray-600 mb-4">Add Contact</h4>

                    <form method="POST" action="{{ route('admin.entities.contacts.store', $entity) }}">
                        @csrf

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Type <span class="text-red-500">*</span>
                                </label>
                                <select name="type"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('type') border-red-500 @enderror">
                                    <option value="">— select —</option>
                                    @foreach(['phone', 'mobile', 'email', 'viber', 'whatsapp'] as $t)
                                        <option value="{{ $t }}" @selected(old('type') === $t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                                @error('type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Value <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="value"
                                       value="{{ old('value') }}"
                                       maxlength="255"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('value') border-red-500 @enderror">
                                @error('value')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            <input type="hidden" name="is_primary" value="0">
                            <input type="checkbox" name="is_primary" value="1" id="contact_is_primary"
                                   @checked(old('is_primary'))
                                   class="rounded border-gray-300 text-gray-800">
                            <label for="contact_is_primary" class="text-xs font-medium text-gray-600 select-none">Primary</label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                                Add Contact
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Links section --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Links</h3>

                @if($entity->links->isNotEmpty())
                    <table class="w-full text-sm text-left mb-6">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">URL</th>
                                <th class="px-3 py-2">Primary</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($entity->links->sortBy('type') as $link)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono text-gray-700">{{ $link->type }}</td>
                                    <td class="px-3 py-2 text-gray-900 max-w-xs truncate">
                                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                                           class="hover:underline text-blue-600">{{ $link->url }}</a>
                                    </td>
                                    <td class="px-3 py-2 text-gray-500">{{ $link->is_primary ? '✓' : '—' }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('admin.entities.links.edit', [$entity, $link]) }}"
                                           class="text-blue-600 hover:underline text-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-400 mb-6">No links yet.</p>
                @endif

                <div class="border-t border-gray-100 pt-5">
                    <h4 class="text-sm font-medium text-gray-600 mb-4">Add Link</h4>

                    <form method="POST" action="{{ route('admin.entities.links.store', $entity) }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('type') border-red-500 @enderror">
                                <option value="">— select —</option>
                                @foreach(['website', 'facebook', 'instagram', 'tiktok', 'youtube', 'menu', 'booking'] as $t)
                                    <option value="{{ $t }}" @selected(old('type') === $t)>{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url"
                                   name="url"
                                   value="{{ old('url') }}"
                                   maxlength="2048"
                                   placeholder="https://"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('url') border-red-500 @enderror">
                            @error('url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            <input type="hidden" name="is_primary" value="0">
                            <input type="checkbox" name="is_primary" value="1" id="link_is_primary"
                                   @checked(old('is_primary'))
                                   class="rounded border-gray-300 text-gray-800">
                            <label for="link_is_primary" class="text-xs font-medium text-gray-600 select-none">Primary</label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                                Add Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sources section --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Sources</h3>

                @if($entity->sources->isNotEmpty())
                    <table class="w-full text-sm text-left mb-6">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">URL</th>
                                <th class="px-3 py-2">Official</th>
                                <th class="px-3 py-2">First Seen</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($entity->sources->sortBy('source_type') as $source)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono text-gray-700">{{ $source->source_type }}</td>
                                    <td class="px-3 py-2 text-gray-500 max-w-xs truncate">
                                        @if($source->source_url)
                                            <a href="{{ $source->source_url }}" target="_blank" rel="noopener noreferrer"
                                               class="hover:underline text-blue-600">{{ $source->source_url }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-500">{{ $source->is_official ? '✓' : '—' }}</td>
                                    <td class="px-3 py-2 text-gray-400">{{ $source->first_seen_at?->format('Y-m-d') ?? '—' }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('admin.entities.sources.edit', [$entity, $source]) }}"
                                           class="text-blue-600 hover:underline text-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-400 mb-6">No sources yet.</p>
                @endif

                <div class="border-t border-gray-100 pt-5">
                    <h4 class="text-sm font-medium text-gray-600 mb-4">Add Source</h4>

                    <form method="POST" action="{{ route('admin.entities.sources.store', $entity) }}">
                        @csrf

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Source Type <span class="text-red-500">*</span>
                                </label>
                                <select name="source_type"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('source_type') border-red-500 @enderror">
                                    <option value="">— select —</option>
                                    @foreach(['official_website', 'social_profile', 'manual_entry', 'third_party_listing'] as $st)
                                        <option value="{{ $st }}" @selected(old('source_type') === $st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                                @error('source_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Source URL</label>
                                <input type="url"
                                       name="source_url"
                                       value="{{ old('source_url') }}"
                                       maxlength="2048"
                                       placeholder="https://"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('source_url') border-red-500 @enderror">
                                @error('source_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">First Seen</label>
                                <input type="datetime-local"
                                       name="first_seen_at"
                                       value="{{ old('first_seen_at') }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('first_seen_at') border-red-500 @enderror">
                                @error('first_seen_at')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Last Seen</label>
                                <input type="datetime-local"
                                       name="last_seen_at"
                                       value="{{ old('last_seen_at') }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('last_seen_at') border-red-500 @enderror">
                                @error('last_seen_at')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            <input type="hidden" name="is_official" value="0">
                            <input type="checkbox" name="is_official" value="1" id="source_is_official"
                                   @checked(old('is_official'))
                                   class="rounded border-gray-300 text-gray-800">
                            <label for="source_is_official" class="text-xs font-medium text-gray-600 select-none">Official</label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                                Add Source
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Media section --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Media</h3>

                @if($entity->media->isNotEmpty())
                    <table class="w-full text-sm text-left mb-6">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">Path</th>
                                <th class="px-3 py-2">URL</th>
                                <th class="px-3 py-2">Cover</th>
                                <th class="px-3 py-2">Sort</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($entity->media->sortBy('sort_order') as $mediaRow)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono text-gray-700">{{ $mediaRow->type }}</td>
                                    <td class="px-3 py-2 text-gray-500 max-w-xs truncate font-mono text-xs">
                                        {{ $mediaRow->path ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-500 max-w-xs truncate">
                                        @if($mediaRow->url)
                                            <a href="{{ $mediaRow->url }}" target="_blank" rel="noopener noreferrer"
                                               class="hover:underline text-blue-600">{{ $mediaRow->url }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-500">{{ $mediaRow->is_cover ? '✓' : '—' }}</td>
                                    <td class="px-3 py-2 text-gray-400">{{ $mediaRow->sort_order }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('admin.entities.media.edit', [$entity, $mediaRow]) }}"
                                           class="text-blue-600 hover:underline text-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-400 mb-6">No media yet.</p>
                @endif

                <div class="border-t border-gray-100 pt-5">
                    <h4 class="text-sm font-medium text-gray-600 mb-4">Add Media</h4>

                    <form method="POST" action="{{ route('admin.entities.media.store', $entity) }}">
                        @csrf

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Type <span class="text-red-500">*</span>
                                </label>
                                <select name="type"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('type') border-red-500 @enderror">
                                    <option value="">— select —</option>
                                    @foreach(['image', 'video'] as $mt)
                                        <option value="{{ $mt }}" @selected(old('type') === $mt)>{{ $mt }}</option>
                                    @endforeach
                                </select>
                                @error('type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                                <input type="number"
                                       name="sort_order"
                                       value="{{ old('sort_order', 0) }}"
                                       min="0"
                                       max="65535"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('sort_order') border-red-500 @enderror">
                                @error('sort_order')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Path</label>
                            <input type="text"
                                   name="path"
                                   value="{{ old('path') }}"
                                   maxlength="1000"
                                   placeholder="storage/media/..."
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm font-mono @error('path') border-red-500 @enderror">
                            @error('path')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">URL</label>
                            <input type="url"
                                   name="url"
                                   value="{{ old('url') }}"
                                   maxlength="2048"
                                   placeholder="https://"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('url') border-red-500 @enderror">
                            @error('url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <p class="text-xs text-gray-400 mb-4">At least one of Path or URL is required.</p>

                        <div class="flex items-center gap-2 mb-4">
                            <input type="hidden" name="is_cover" value="0">
                            <input type="checkbox" name="is_cover" value="1" id="media_is_cover"
                                   @checked(old('is_cover'))
                                   class="rounded border-gray-300 text-gray-800">
                            <label for="media_is_cover" class="text-xs font-medium text-gray-600 select-none">Cover</label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                                Add Media
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Relations section --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Relations</h3>

                {{-- Outgoing --}}
                @if($entity->outgoingRelations->isNotEmpty())
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Outgoing</h4>
                    <table class="w-full text-sm text-left mb-6">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">Target Entity</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($entity->outgoingRelations->sortBy('relation_type') as $rel)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono text-gray-700">{{ $rel->relation_type }}</td>
                                    <td class="px-3 py-2 text-gray-700">
                                        #{{ $rel->to_entity_id }}
                                        <span class="text-gray-400 font-mono text-xs ml-1">[{{ $rel->toEntity?->entityType?->code ?? '?' }}]</span>
                                        <span class="text-gray-500 ml-1">{{ $rel->toEntity?->translations->first()?->name ?? $rel->toEntity?->slug ?? '' }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('admin.entities.relations.edit', [$entity, $rel]) }}"
                                           class="text-blue-600 hover:underline text-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-400 mb-6">No outgoing relations yet.</p>
                @endif

                {{-- Add outgoing relation form --}}
                <div class="border-t border-gray-100 pt-5 mb-6">
                    <h4 class="text-sm font-medium text-gray-600 mb-4">Add Relation</h4>

                    <form method="POST" action="{{ route('admin.entities.relations.store', $entity) }}">
                        @csrf

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Relation Type <span class="text-red-500">*</span>
                                </label>
                                <select name="relation_type"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('relation_type') border-red-500 @enderror">
                                    <option value="">— select —</option>
                                    @foreach(['located_in', 'near', 'part_of'] as $rt)
                                        <option value="{{ $rt }}" @selected(old('relation_type') === $rt)>{{ $rt }}</option>
                                    @endforeach
                                </select>
                                @error('relation_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Target Entity <span class="text-red-500">*</span>
                                </label>
                                <select name="to_entity_id"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('to_entity_id') border-red-500 @enderror">
                                    <option value="">— select —</option>
                                    @foreach($otherEntities as $e)
                                        <option value="{{ $e->id }}" @selected((int) old('to_entity_id') === $e->id)>
                                            #{{ $e->id }} [{{ $e->entityType?->code ?? '?' }}] {{ $e->translations->first()?->name ?? $e->slug }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_entity_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                                Add Relation
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Incoming (read-only) --}}
                @if($entity->incomingRelations->isNotEmpty())
                    <div class="border-t border-gray-100 pt-5">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Incoming (read-only)</h4>
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b border-gray-200 text-gray-400 uppercase text-xs tracking-wide">
                                <tr>
                                    <th class="px-3 py-2">Type</th>
                                    <th class="px-3 py-2">From Entity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($entity->incomingRelations->sortBy('relation_type') as $rel)
                                    <tr>
                                        <td class="px-3 py-2 font-mono text-gray-500">{{ $rel->relation_type }}</td>
                                        <td class="px-3 py-2 text-gray-500">
                                            #{{ $rel->from_entity_id }}
                                            <span class="text-gray-400 font-mono text-xs ml-1">[{{ $rel->fromEntity?->entityType?->code ?? '?' }}]</span>
                                            <span class="ml-1">{{ $rel->fromEntity?->translations->first()?->name ?? $rel->fromEntity?->slug ?? '' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Amenities section --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Amenities</h3>

                <form method="POST" action="{{ route('admin.entities.amenities.sync', $entity) }}">
                    @csrf

                    @if($allAmenities->isEmpty())
                        <p class="text-sm text-gray-400 mb-4">No amenities defined in dictionary.</p>
                    @else
                        <div class="grid grid-cols-3 gap-x-6 gap-y-2 mb-5">
                            @foreach($allAmenities as $amenity)
                                <label class="flex items-center gap-2 text-sm text-gray-700 select-none cursor-pointer">
                                    <input type="checkbox"
                                           name="amenities[]"
                                           value="{{ $amenity->id }}"
                                           @checked(in_array($amenity->id, $selectedAmenityIds))
                                           class="rounded border-gray-300 text-gray-800">
                                    {{ $amenity->translations->first()?->name ?? $amenity->code }}
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                            Save Amenities
                        </button>
                    </div>
                </form>
            </div>

            {{-- Translations section --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Translations</h3>

                @if($entity->translations->isNotEmpty())
                    <table class="w-full text-sm text-left mb-6">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-3 py-2">Locale</th>
                                <th class="px-3 py-2">Name</th>
                                <th class="px-3 py-2">Address</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($entity->translations->sortBy('locale') as $translation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono font-medium text-gray-700">{{ $translation->locale }}</td>
                                    <td class="px-3 py-2 text-gray-900">{{ $translation->name }}</td>
                                    <td class="px-3 py-2 text-gray-500">{{ $translation->address ?? '—' }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('admin.entities.translations.edit', [$entity, $translation]) }}"
                                           class="text-blue-600 hover:underline text-sm">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-400 mb-6">No translations yet.</p>
                @endif

                {{-- Add translation form --}}
                <div class="border-t border-gray-100 pt-5">
                    <h4 class="text-sm font-medium text-gray-600 mb-4">Add Translation</h4>

                    <form method="POST" action="{{ route('admin.entities.translations.store', $entity) }}">
                        @csrf

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Locale <span class="text-red-500">*</span>
                                </label>
                                <select name="locale"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('locale') border-red-500 @enderror">
                                    <option value="">— select —</option>
                                    <option value="bg" @selected(old('locale') === 'bg')>bg</option>
                                    <option value="en" @selected(old('locale') === 'en')>en</option>
                                </select>
                                @error('locale')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name') }}"
                                       maxlength="255"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                            <input type="text"
                                   name="address"
                                   value="{{ old('address') }}"
                                   maxlength="255"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('address') border-red-500 @enderror">
                            @error('address')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                            <textarea name="description"
                                      rows="3"
                                      class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                                Add Translation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
