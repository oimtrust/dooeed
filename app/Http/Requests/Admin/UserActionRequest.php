<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin' && $this->user()?->status === 'active';
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['suspend', 'activate', 'reset_password', 'change_role', 'delete'])],
            'reason' => ['required_if:action,suspend', 'nullable', 'string', 'max:2000'],
            'role' => ['required_if:action,change_role', Rule::in(['user', 'admin'])],
            'confirmed' => ['accepted'],
        ];
    }
}
