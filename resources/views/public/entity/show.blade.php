@extends('layouts.public')

@section('title', $bgTranslation->name . ' — ' . config('app.name', 'BKG'))

@section('content')

    {{-- Cover image --}}
    @if($coverMedia && $coverMedia->url)
        <div class="mb-6 rounded-lg overflow-hidden bg-gray-100 aspect-video">
            <img src="{{ $coverMedia->url }}"
                 alt="{{ $bgTranslation->name }}"
                 class="w-full h-full object-cover">
        </div>
    @endif

    {{-- Identity header --}}
    <div class="mb-6">
        <div class="flex flex-wrap items-center gap-2 mb-2">
            @if($entity->entityType)
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-mono">
                    {{ $entity->entityType->code }}
                </span>
            @endif
            @if($bgPlaceName)
                <span class="text-xs text-gray-400">{{ $bgPlaceName }}</span>
            @endif
        </div>

        <h1 class="text-2xl font-bold text-gray-900 leading-snug">
            {{ $bgTranslation->name }}
        </h1>

        @if($bgTranslation->address)
            <p class="text-sm text-gray-500 mt-1">{{ $bgTranslation->address }}</p>
        @endif
    </div>

    {{-- BG description --}}
    @if($bgTranslation->description)
        <div class="mb-8 text-gray-700 text-sm leading-relaxed whitespace-pre-line">
            {{ $bgTranslation->description }}
        </div>
    @endif

    {{-- Type-specific details --}}
    @if($detail)
        @if($family === 'accommodation')
            <div class="mb-8">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Настаняване</h2>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    @if($detail->star_rating)
                        <div>
                            <dt class="text-gray-400 text-xs">Категория</dt>
                            <dd class="text-gray-800">{{ $detail->star_rating }} ★</dd>
                        </div>
                    @endif
                    @if($detail->check_in_from || $detail->check_in_to)
                        <div>
                            <dt class="text-gray-400 text-xs">Настаняване</dt>
                            <dd class="text-gray-800">
                                {{ $detail->check_in_from ?? '—' }}{{ $detail->check_in_to ? ' – ' . $detail->check_in_to : '' }}
                            </dd>
                        </div>
                    @endif
                    @if($detail->check_out_from || $detail->check_out_to)
                        <div>
                            <dt class="text-gray-400 text-xs">Напускане</dt>
                            <dd class="text-gray-800">
                                {{ $detail->check_out_from ?? '—' }}{{ $detail->check_out_to ? ' – ' . $detail->check_out_to : '' }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

        @elseif($family === 'food_place')
            <div class="mb-8">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Заведение</h2>

                @php
                    $foodFlags = [];
                    if ($detail->accepts_reservations) $foodFlags[] = 'резервации';
                    if ($detail->takeaway_available)    $foodFlags[] = 'за вкъщи';
                    if ($detail->delivery_available)    $foodFlags[] = 'доставка';
                    if ($detail->serves_breakfast)      $foodFlags[] = 'закуска';
                    if ($detail->serves_lunch)          $foodFlags[] = 'обяд';
                    if ($detail->serves_dinner)         $foodFlags[] = 'вечеря';
                @endphp

                @if(count($foodFlags))
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($foodFlags as $flag)
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $flag }}</span>
                        @endforeach
                    </div>
                @endif

                @if($detail->price_range)
                    <p class="text-sm text-gray-600">
                        <span class="text-gray-400 text-xs">Ценово ниво:</span>
                        {{ $detail->price_range }}
                    </p>
                @endif
            </div>

        @elseif($family === 'attraction')
            <div class="mb-8">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Атракция</h2>

                @php
                    $attrFlags = [];
                    if ($detail->is_natural)       $attrFlags[] = 'природна';
                    if ($detail->is_cultural)      $attrFlags[] = 'културна';
                    if ($detail->is_indoor)        $attrFlags[] = 'на закрито';
                    if ($detail->is_outdoor)       $attrFlags[] = 'на открито';
                    if ($detail->is_free)          $attrFlags[] = 'безплатна';
                    if ($detail->is_family_friendly) $attrFlags[] = 'семейна';
                    if ($detail->is_accessible)    $attrFlags[] = 'достъпна';
                    if ($detail->is_seasonal)      $attrFlags[] = 'сезонна';
                @endphp

                @if(count($attrFlags))
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($attrFlags as $flag)
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $flag }}</span>
                        @endforeach
                    </div>
                @endif

                @if($detail->has_entry_fee)
                    <p class="text-xs text-gray-500 mb-2">Необходим билет</p>
                @endif

                @if($detail->estimated_visit_minutes)
                    <p class="text-sm text-gray-600">
                        <span class="text-gray-400 text-xs">Времетраене:</span>
                        @if($detail->estimated_visit_minutes >= 60)
                            ~{{ round($detail->estimated_visit_minutes / 60, 1) }} ч.
                        @else
                            ~{{ $detail->estimated_visit_minutes }} мин.
                        @endif
                    </p>
                @endif
            </div>
        @endif
    @endif

    {{-- Amenities --}}
    @if($entity->entityAmenities->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Удобства</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($entity->entityAmenities as $ea)
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                        {{ $ea->amenity->translations->firstWhere('locale', 'bg')?->name ?? $ea->amenity->code }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Price signals --}}
    @if($entity->priceSignals->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Цени</h2>
            <div class="space-y-1.5">
                @foreach($entity->priceSignals as $signal)
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        @if($signal->price_category)
                            <span class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded">
                                {{ $signal->price_category }}
                            </span>
                        @endif
                        @if($signal->currency)
                            @if($signal->amount_min !== null && $signal->amount_max !== null)
                                <span class="text-gray-700">
                                    {{ number_format($signal->amount_min, 0) }} – {{ number_format($signal->amount_max, 0) }} {{ $signal->currency }}
                                </span>
                            @elseif($signal->amount_min !== null)
                                <span class="text-gray-700">от {{ number_format($signal->amount_min, 0) }} {{ $signal->currency }}</span>
                            @elseif($signal->amount_max !== null)
                                <span class="text-gray-700">до {{ number_format($signal->amount_max, 0) }} {{ $signal->currency }}</span>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Primary contacts --}}
    @if($primaryContacts->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Контакти</h2>
            <ul class="space-y-1.5">
                @foreach($primaryContacts as $contact)
                    <li class="text-sm text-gray-700">
                        <span class="text-gray-400 text-xs font-mono mr-1">{{ $contact->type }}</span>
                        {{ $contact->value }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Primary / website links --}}
    @if($primaryLinks->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Връзки</h2>
            <ul class="space-y-1.5">
                @foreach($primaryLinks as $link)
                    <li class="text-sm">
                        <a href="{{ $link->url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="text-blue-600 hover:underline break-all">{{ $link->url }}</a>
                        <span class="text-gray-400 text-xs ml-1">{{ $link->type }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- EN translation (secondary block) --}}
    @if($enTranslation)
        <div class="border-t border-gray-100 pt-8 mt-8">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">EN</p>
            <h2 class="text-lg font-semibold text-gray-700 mb-1">{{ $enTranslation->name }}</h2>
            @if($enTranslation->address)
                <p class="text-sm text-gray-500 mb-2">{{ $enTranslation->address }}</p>
            @endif
            @if($enTranslation->description)
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $enTranslation->description }}</p>
            @endif
        </div>
    @endif

@endsection
