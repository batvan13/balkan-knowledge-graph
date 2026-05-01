<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.entities.edit', $entity) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Entity #{{ $entity->id }}</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Price Signal
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('admin.entities.price-signals.update', [$entity, $priceSignal]) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Signal Type <span class="text-red-500">*</span>
                            </label>
                            <select name="signal_type"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('signal_type') border-red-500 @enderror">
                                <option value="">— select —</option>
                                @foreach(['observed', 'owner_declared'] as $st)
                                    <option value="{{ $st }}" @selected(old('signal_type', $priceSignal->signal_type) === $st)>{{ $st }}</option>
                                @endforeach
                            </select>
                            @error('signal_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Price Category</label>
                            <select name="price_category"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('price_category') border-red-500 @enderror">
                                <option value="">— none —</option>
                                @foreach(['budget', 'midrange', 'premium', 'luxury'] as $pc)
                                    <option value="{{ $pc }}" @selected(old('price_category', $priceSignal->price_category) === $pc)>{{ $pc }}</option>
                                @endforeach
                            </select>
                            @error('price_category')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Currency</label>
                            <input type="text"
                                   name="currency"
                                   value="{{ old('currency', $priceSignal->currency) }}"
                                   maxlength="10"
                                   placeholder="USD / EUR / BGN"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm font-mono @error('currency') border-red-500 @enderror">
                            @error('currency')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Amount Min</label>
                            <input type="number"
                                   name="amount_min"
                                   value="{{ old('amount_min', $priceSignal->amount_min) }}"
                                   min="0"
                                   step="0.01"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('amount_min') border-red-500 @enderror">
                            @error('amount_min')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Amount Max</label>
                            <input type="number"
                                   name="amount_max"
                                   value="{{ old('amount_max', $priceSignal->amount_max) }}"
                                   min="0"
                                   step="0.01"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('amount_max') border-red-500 @enderror">
                            @error('amount_max')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Observed At</label>
                        <input type="datetime-local"
                               name="observed_at"
                               value="{{ old('observed_at', $priceSignal->observed_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm @error('observed_at') border-red-500 @enderror">
                        @error('observed_at')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <p class="text-xs text-gray-400 mb-5">At least one of price category, amount min, or amount max is required. Currency is required when amounts are specified.</p>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.entities.edit', $entity) }}"
                           class="text-sm text-gray-500 hover:underline">Cancel</a>
                        <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                            Update Price Signal
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
