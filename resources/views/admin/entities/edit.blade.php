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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <p class="text-sm text-gray-400 mb-6">Entity record created. Editing sections will be added here.</p>

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

        </div>
    </div>
</x-app-layout>
