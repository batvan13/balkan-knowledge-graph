<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.entities.edit', $entity) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Entity #{{ $entity->id }}</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Source</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('admin.entities.sources.update', [$entity, $source]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Source Type <span class="text-red-500">*</span>
                        </label>
                        <select name="source_type"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('source_type') border-red-500 @enderror">
                            @foreach(['official_website', 'social_profile', 'manual_entry', 'third_party_listing'] as $st)
                                <option value="{{ $st }}" @selected(old('source_type', $source->source_type) === $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                        @error('source_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Source URL</label>
                        <input type="url"
                               name="source_url"
                               value="{{ old('source_url', $source->source_url) }}"
                               maxlength="2048"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('source_url') border-red-500 @enderror">
                        @error('source_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">First Seen</label>
                            <input type="datetime-local"
                                   name="first_seen_at"
                                   value="{{ old('first_seen_at', $source->first_seen_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('first_seen_at') border-red-500 @enderror">
                            @error('first_seen_at')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Last Seen</label>
                            <input type="datetime-local"
                                   name="last_seen_at"
                                   value="{{ old('last_seen_at', $source->last_seen_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('last_seen_at') border-red-500 @enderror">
                            @error('last_seen_at')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mb-6">
                        <input type="hidden" name="is_official" value="0">
                        <input type="checkbox" name="is_official" value="1" id="edit_is_official"
                               @checked(old('is_official', $source->is_official))
                               class="rounded border-gray-300 text-gray-800">
                        <label for="edit_is_official" class="text-xs font-medium text-gray-600 select-none">Official</label>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.entities.edit', $entity) }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                            Update Source
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
