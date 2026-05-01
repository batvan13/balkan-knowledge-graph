<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.entities.edit', $entity) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Entity #{{ $entity->id }}</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Media Row</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('admin.entities.media.update', [$entity, $media]) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('type') border-red-500 @enderror">
                                @foreach(['image', 'video'] as $mt)
                                    <option value="{{ $mt }}" @selected(old('type', $media->type) === $mt)>{{ $mt }}</option>
                                @endforeach
                            </select>
                            @error('type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                            <input type="number"
                                   name="sort_order"
                                   value="{{ old('sort_order', $media->sort_order) }}"
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
                               value="{{ old('path', $media->path) }}"
                               maxlength="1000"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm font-mono @error('path') border-red-500 @enderror">
                        @error('path')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">URL</label>
                        <input type="url"
                               name="url"
                               value="{{ old('url', $media->url) }}"
                               maxlength="2048"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('url') border-red-500 @enderror">
                        @error('url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <p class="text-xs text-gray-400 mb-4">At least one of Path or URL is required.</p>

                    <div class="flex items-center gap-2 mb-6">
                        <input type="hidden" name="is_cover" value="0">
                        <input type="checkbox" name="is_cover" value="1" id="edit_is_cover"
                               @checked(old('is_cover', $media->is_cover))
                               class="rounded border-gray-300 text-gray-800">
                        <label for="edit_is_cover" class="text-xs font-medium text-gray-600 select-none">Cover</label>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.entities.edit', $entity) }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                            Update Media
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
