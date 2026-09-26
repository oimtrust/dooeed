<?php

namespace App\Models;

use Database\Factories\SavingsCapacityProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'monthly_income', 'monthly_expenses', 'date_of_birth', 'retirement_age', 'inheritance_age'])]
class SavingsCapacityProfile extends Model
{
    /** @use HasFactory<SavingsCapacityProfileFactory> */
    use HasFactory, HasUuids;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return ['monthly_income' => 'decimal:2', 'monthly_expenses' => 'decimal:2', 'date_of_birth' => 'date', 'retirement_age' => 'integer', 'inheritance_age' => 'integer'];
    }
}
