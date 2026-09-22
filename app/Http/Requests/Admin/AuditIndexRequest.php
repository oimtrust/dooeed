<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AuditIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'admin_id' => ['nullable', 'uuid'],
            'target_user_id' => ['nullable', 'uuid'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
