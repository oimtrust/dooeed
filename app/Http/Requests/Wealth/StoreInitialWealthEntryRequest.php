<?php

namespace App\Http\Requests\Wealth;

use App\Models\InitialWealthEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInitialWealthEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $requiresAssetFields = fn (): bool => InitialWealthEntry::isAssetCategory((string) $this->input('category'));

        return [
            'category' => ['required', 'string', Rule::in(InitialWealthEntry::categories())],
            'name' => ['required', 'string', 'max:255'],
            'amount' => [Rule::requiredIf(fn (): bool => ! $requiresAssetFields()), 'nullable', 'numeric', 'min:0'],
            'unit_price' => [Rule::requiredIf($requiresAssetFields), 'nullable', 'numeric', 'min:0'],
            'quantity' => [Rule::requiredIf($requiresAssetFields), 'nullable', 'numeric', 'gt:0'],
            'debt_type' => [Rule::requiredIf(fn (): bool => $this->input('category') === InitialWealthEntry::Debt), 'nullable', 'string', Rule::in(['other', 'credit_card_paylater'])],
        ];
    }
}
