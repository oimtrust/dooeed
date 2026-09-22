<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'accounts_count' => $this->whenHas('accounts_count', fn () => (int) $this->accounts_count),
            'transactions_count' => $this->whenHas('transactions_count', fn () => (int) $this->transactions_count),
            'can_manage' => $request->user()->id !== $this->id,
        ];
    }
}
