<?php

namespace App\Http\Resources\Wealth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InitialWealthEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'debt_type' => $this->debt_type,
            'name' => $this->name,
            'amount' => $this->amount,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
