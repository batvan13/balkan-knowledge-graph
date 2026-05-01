@extends('layouts.public')

@section('title', 'Каталог — ' . config('app.name', 'BKG'))

@section('content')

    <div class="mb-8">
        <h1 class="text-xl font-bold text-gray-900">Каталог</h1>
        <p class="text-sm text-gray-500 mt-1">Публикувани обекти</p>
    </div>

    @if($entities->isEmpty())
        <p class="text-sm text-gray-400">Няма публикувани обекти.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            @foreach($entities as $entity)
                @php
                    $bgName      = $entity->translations->first()?->name;
                    $coverUrl    = $entity->media->first()?->url;
                    $placeName   = $entity->place?->translations->first()?->name
                                   ?? $entity->place?->slug;
                @endphp

                <a href="{{ route('entity.show', $entity->slug) }}"
                   class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow block">

                    @if($coverUrl)
                        <div class="aspect-video bg-gray-100 overflow-hidden">
                            <img src="{{ $coverUrl }}"
                                 alt="{{ $bgName }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="p-4">
                        <h2 class="font-semibold text-gray-900 text-sm leading-snug mb-2">
                            {{ $bgName }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-2">
                            @if($entity->entityType)
                                <span class="text-xs font-mono text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">
                                    {{ $entity->entityType->code }}
                                </span>
                            @endif
                            @if($placeName)
                                <span class="text-xs text-gray-400">{{ $placeName }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div>
            {{ $entities->links() }}
        </div>
    @endif

@endsection
