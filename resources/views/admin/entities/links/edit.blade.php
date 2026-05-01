<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.entities.edit', $entity) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Entity #{{ $entity->id }}</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Link</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('admin.entities.links.update', [$entity, $link]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('type') border-red-500 @enderror">
                            @foreach(['website', 'facebook', 'instagram', 'tiktok', 'youtube', 'menu', 'booking'] as $t)
                                <option value="{{ $t }}" @selected(old('type', $link->type) === $t)>{{ $t }}</option>
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
                               value="{{ old('url', $link->url) }}"
                               maxlength="2048"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('url') border-red-500 @enderror">
                        @error('url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-2 mb-6">
                        <input type="hidden" name="is_primary" value="0">
                        <input type="checkbox" name="is_primary" value="1" id="edit_link_is_primary"
                               @checked(old('is_primary', $link->is_primary))
                               class="rounded border-gray-300 text-gray-800">
                        <label for="edit_link_is_primary" class="text-xs font-medium text-gray-600 select-none">Primary</label>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.entities.edit', $entity) }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                            Update Link
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
