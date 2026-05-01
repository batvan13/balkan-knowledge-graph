<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityPriceSignal;
use Illuminate\Http\Request;

class EntityPriceSignalController extends Controller
{
    private function baseRules(): array
    {
        return [
            'signal_type'    => ['required', 'in:observed,owner_declared'],
            'price_category' => ['nullable', 'in:budget,midrange,premium,luxury'],
            'currency'       => ['nullable', 'string', 'max:10'],
            'amount_min'     => ['nullable', 'numeric', 'min:0'],
            'amount_max'     => ['nullable', 'numeric', 'min:0'],
            'observed_at'    => ['nullable', 'date'],
        ];
    }

    private function applyAppRules(array $validated)
    {
        $hasCategory  = !empty($validated['price_category']);
        $hasAmountMin = isset($validated['amount_min']) && $validated['amount_min'] !== null && $validated['amount_min'] !== '';
        $hasAmountMax = isset($validated['amount_max']) && $validated['amount_max'] !== null && $validated['amount_max'] !== '';

        if (!$hasCategory && !$hasAmountMin && !$hasAmountMax) {
            return ['price_category' => 'At least one of price category, amount min, or amount max must be provided.'];
        }

        if (($hasAmountMin || $hasAmountMax) && empty($validated['currency'])) {
            return ['currency' => 'Currency is required when specifying amounts.'];
        }

        if ($hasAmountMin && $hasAmountMax) {
            if ((float) $validated['amount_max'] < (float) $validated['amount_min']) {
                return ['amount_max' => 'Amount max must be greater than or equal to amount min.'];
            }
        }

        return null;
    }

    public function store(Request $request, Entity $entity)
    {
        $validated = $request->validate($this->baseRules());

        if ($errors = $this->applyAppRules($validated)) {
            return back()->withInput()->withErrors($errors);
        }

        $signal = new EntityPriceSignal();
        $signal->entity_id      = $entity->id;
        $signal->signal_type    = $validated['signal_type'];
        $signal->price_category = $validated['price_category'] ?? null;
        $signal->currency       = $validated['currency'] ?? null;
        $signal->amount_min     = isset($validated['amount_min']) && $validated['amount_min'] !== '' ? $validated['amount_min'] : null;
        $signal->amount_max     = isset($validated['amount_max']) && $validated['amount_max'] !== '' ? $validated['amount_max'] : null;
        $signal->observed_at    = $validated['observed_at'] ?? null;
        $signal->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Price signal added.');
    }

    public function edit(Entity $entity, EntityPriceSignal $priceSignal)
    {
        abort_if($priceSignal->entity_id !== $entity->id, 404);

        return view('admin.entities.price-signals.edit', compact('entity', 'priceSignal'));
    }

    public function update(Request $request, Entity $entity, EntityPriceSignal $priceSignal)
    {
        abort_if($priceSignal->entity_id !== $entity->id, 404);

        $validated = $request->validate($this->baseRules());

        if ($errors = $this->applyAppRules($validated)) {
            return back()->withInput()->withErrors($errors);
        }

        $priceSignal->signal_type    = $validated['signal_type'];
        $priceSignal->price_category = $validated['price_category'] ?? null;
        $priceSignal->currency       = $validated['currency'] ?? null;
        $priceSignal->amount_min     = isset($validated['amount_min']) && $validated['amount_min'] !== '' ? $validated['amount_min'] : null;
        $priceSignal->amount_max     = isset($validated['amount_max']) && $validated['amount_max'] !== '' ? $validated['amount_max'] : null;
        $priceSignal->observed_at    = $validated['observed_at'] ?? null;
        $priceSignal->save();

        return redirect()->route('admin.entities.edit', $entity)
            ->with('success', 'Price signal updated.');
    }
}
