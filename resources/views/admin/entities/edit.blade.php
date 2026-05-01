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
