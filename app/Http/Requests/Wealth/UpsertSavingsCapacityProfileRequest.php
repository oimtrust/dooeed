<?php

namespace App\Http\Requests\Wealth;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpsertSavingsCapacityProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'monthly_income' => ['required', 'numeric', 'min:0'],
            'monthly_expenses' => ['required', 'numeric', 'min:0'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'retirement_age' => ['required', 'integer', 'min:1'],
            'inheritance_age' => ['required', 'integer', 'min:1'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('date_of_birth')) {
                return;
            }
            $age = Carbon::parse($this->input('date_of_birth'))->floatDiffInYears(now());
            if ((int) $this->input('retirement_age') <= $age) {
                $validator->errors()->add('retirement_age', 'Retirement age must be greater than your current age.');
            }
            if ((int) $this->input('inheritance_age') <= (int) $this->input('retirement_age')) {
                $validator->errors()->add('inheritance_age', 'Inheritance age must be greater than retirement age.');
            }
        }];
    }
}
