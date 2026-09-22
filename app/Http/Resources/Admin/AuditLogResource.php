<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admin_id' => $this->admin_id,
            'admin_email' => $this->admin->email,
            'target_user_id' => $this->target_user_id,
            'target_user_email' => $this->targetUser->email,
            'action' => $this->action,
            'reason' => $this->reason,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
